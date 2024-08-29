<?php

namespace App\Http\Controllers\Ekpay;

use App\Models\Doner;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EkpayPaymentController extends Controller
{

    public function ipn(Request $request)
    {
        $data = $request->all();
        Log::info(json_encode($data));

        // For debugging: return the data to see the IPN payload
        // return $data;

        // Fetch the payment record using the transaction ID
        $trnx_id = $data['trnx_info']['mer_trnx_id'];
        $payment = Payment::where('trxid', $trnx_id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        $Insertdata = [];

        if ($data['msg_code'] == '1020') { // Payment successful
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data['pi_det_info']['pi_name'],
            ];

            // If there's additional logic for successful donations, it can be added here
            $doner = Doner::find($payment->sonodId);
            if ($doner) {
                $deccription = "Thank you for your donation!";
                // $this->sendDonationConfirmation($doner, $deccription);
            }

        } else { // Payment failed
            $Insertdata = [
                'status' => 'Failed',
            ];
        }

        // Store the IPN response in the payment record
        $Insertdata['ipnResponse'] = json_encode($data);

        // Update the payment record with the new status and IPN response
        $payment->update($Insertdata);

        return response()->json(['message' => 'IPN processed successfully'], 200);
    }

    /**
     * Send a donation confirmation message to the donor.
     *
     * @param Doner $doner
     * @param string $description
     * @return void
     */
    // protected function sendDonationConfirmation(Doner $doner, string $description)
    // {
    //     // Assuming you have an SMS service or email service to notify the donor
    //     SmsNocSmsSend($description, $doner->phoneNumber, 'Donation');
    // }



    public function ReCallIpn(Request $request)
    {
        $trnx_id = $request->trnx_id;
        $trans_date = date("Y-m-d", strtotime($request->trans_date));
        $url = env('AKPAY_API_URL');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url . '/get-status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'trnx_id' => $trnx_id,
                'trans_date' => $trans_date,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response1 = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response1);

        Log::info(json_encode($data));

        // Assuming the donor's ID is stored in `cust_info->cust_id`
        $donorId = $data->cust_info->cust_id;

        // Fetching the payment record using the transaction ID
        $payment = Payment::where('trxid', $trnx_id)->first();

        $Insertdata = [];
        if ($data->msg_code == '1020') {
            // Payment was successful
            $Insertdata = [
                'status' => 'Paid',
                'method' => $data->pi_det_info->pi_name,
            ];

            // Update any additional logic related to successful donations here

        } else {
            // Payment failed
            $Insertdata = [
                'status' => 'Failed',
            ];
        }

        // Store the IPN response in the payment record
        $Insertdata['ipnResponse'] = json_encode($data);

        // Update the payment record with the new status and IPN response
        return $payment->update($Insertdata);
    }





    public function AkpayPaymentCheck(Request $request)
    {

        $trnx_id = $request->trnx_id;
        $trans_date = date("Y-m-d", strtotime($request->trans_date));

        $url = env('AKPAY_API_URL');

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/get-status',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{

         "trnx_id":"'.$trnx_id.'",
         "trans_date":"'.$trans_date.'"

        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response1 = curl_exec($curl);

        curl_close($curl);


        $myserver = Payment::where(['trxId'=>$trnx_id])->first();


      return   $data =  [
        'myserver'=>$myserver,
        'akpay'=> json_decode($response1),
      ];


    }


    public function handlePaymentSuccess(Request $request)
    {
        $transId = $request->transId;
      return  $payment = Payment::where('trxId', $transId)->first();


            $sonod = Sonod::find($payment->sonodId);

            $redirect = "/payment/success/confirm?transId=$transId";



        return response("
            <h3 style='text-align:center'>Please wait 10 seconds. This page will auto redirect you</h3>
            <script>
                setTimeout(() => {
                    window.location.href='$redirect';
                }, 10000);
            </script>
        ");
    }



    public function sonodpaymentSuccess(Request $request)
    {
        $transId =  $request->transId;
         $payment = Payment::where(['trxId' => $transId])->first();
        $id = $payment->sonodId;

        $sonod = Sonod::find($id);







        if($payment->status=='Paid'){
                    $InvoiceUrl =  url("/invoice/c/$id");
                    // $deccription = "অভিনন্দন! আপনার আবেদনটি সফলভাবে পরিশোধিত হয়েছে। অনুমোদনের জন্য অপেক্ষা করুন।";
                    $deccription = "Congratulation! Your application $sonod->sonod_Id has been Paid.Wait for Approval.";
                    // smsSend($deccription, $sonod->applicant_mobile);
                    return view('applicationSuccess', compact('payment', 'sonod'));
        }else{
            echo "
            <div style='text-align:center'>
            <h1 style='text-align:center'>Payment Failed</h1>
            <a href='/' style='border:1px solid black;padding:10px 12px; background:red;color:white'>Back To Home</a>
            <a href='/sonod/payment/$sonod->id' style='border:1px solid black;padding:10px 12px; background:green;color:white'>Pay Again</a>
            </div>
            ";
        }




    }

}
