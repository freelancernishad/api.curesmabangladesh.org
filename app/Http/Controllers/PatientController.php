<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doner;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'diagnosedForSMA' => 'required|boolean',
            'symptoms' => 'required|boolean',
            'typeOfSMA' => 'nullable|string|max:255',
            'doctorName' => 'nullable|string|max:255',
            'fatherMobile' => 'nullable|string|max:255',
            'motherMobile' => 'nullable|string|max:255',
            'emergencyContact' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'presentAddress' => 'nullable|string|max:255',
            'permanentAddress' => 'nullable|string|max:255',
            'agreement' => 'required|boolean',
            'dateOfBirth' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Image validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = [
            'name' => $request->fullName,
            'fullName' => $request->fullName,
            'relationship' => $request->relationship,
            'diagnosedForSMA' => $request->diagnosedForSMA,
            'symptoms' => $request->symptoms,
            'typeOfSMA' => $request->typeOfSMA,
            'doctorName' => $request->doctorName,
            'fatherMobile' => $request->fatherMobile,
            'motherMobile' => $request->motherMobile,
            'emergencyContact' => $request->emergencyContact,
            'email' => $request->email,
            'mobile' => '01909756552',
            'presentAddress' => $request->presentAddress,
            'permanentAddress' => $request->permanentAddress,
            'agreement' => $request->agreement,
            'dateOfBirth' => $request->dateOfBirth,
            'password' => Hash::make('defaultpassword'),
        ];

        if ($request->hasFile('image')) {
            $userData['image'] = $this->storeImage($request->file('image'));
        }

        $user = User::create($userData);


        $message1 = "Dear $request->fullName, রেজিস্ট্রেশন এর আবেদনটি সম্পন্ন হয়েছে , অতিসত্তর আপনার সাথে যোগাযোগ করা হবে";
        sendSms($request->fatherMobile,$message1);

        $message2 = "Dear Admin, $request->fullName, রেজিস্ট্রেশন এর আবেদনটি সম্পন্ন করেছে , যোগাযোগ: $request->fatherMobile";
        sendSms("01909756552",$message2);



        return response()->json(['message' => 'Patient registered successfully', 'user' => $user], 201);
    }

    private function storeImage($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('post/banner', $fileName, 'protected');
    }

    public function updateDescriptions(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'annual_cost' => 'nullable|numeric',
            'total_cost' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fullName' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'diagnosedForSMA' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string|max:255',
            'typeOfSMA' => 'nullable|string|max:255',
            'doctorName' => 'nullable|string|max:255',
            'fatherMobile' => 'nullable|string|max:15',
            'motherMobile' => 'nullable|string|max:15',
            'emergencyContact' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'presentAddress' => 'nullable|string|max:255',
            'permanentAddress' => 'nullable|string|max:255',
            'agreement' => 'nullable|boolean',
            'dateOfBirth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if a new image file is uploaded
        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('protected')->delete($user->image);
            }

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('user_images', $fileName, 'protected');
            $user->image = $filePath;
        }

        // Update the fields with either new values or existing values
        $user->update([
            'short_description' => $request->input('short_description', $user->short_description),
            'long_description' => $request->input('long_description', $user->long_description),
            'annual_cost' => $request->input('annual_cost', $user->annual_cost),
            'total_cost' => $request->input('total_cost', $user->total_cost),
            'name' => $request->input('fullName', $user->name),
            'fullName' => $request->input('fullName', $user->fullName),
            'relationship' => $request->input('relationship', $user->relationship),
            'diagnosedForSMA' => $request->input('diagnosedForSMA', $user->diagnosedForSMA),
            'symptoms' => $request->input('symptoms', $user->symptoms),
            'typeOfSMA' => $request->input('typeOfSMA', $user->typeOfSMA),
            'doctorName' => $request->input('doctorName', $user->doctorName),
            'fatherMobile' => $request->input('fatherMobile', $user->fatherMobile),
            'motherMobile' => $request->input('motherMobile', $user->motherMobile),
            'emergencyContact' => $request->input('emergencyContact', $user->emergencyContact),
            'email' => $request->input('email', $user->email),
            'mobile' => $user->mobile ?? '01909756552', // Preserve existing or default
            'presentAddress' => $request->input('presentAddress', $user->presentAddress),
            'permanentAddress' => $request->input('permanentAddress', $user->permanentAddress),
            'agreement' => $request->input('agreement', $user->agreement),
            'dateOfBirth' => $request->input('dateOfBirth', $user->dateOfBirth),
            'status' => 'active',
        ]);

        $message1 = "Dear $request->fullName, আপনার রেজিস্ট্রেশন সম্পন্ন হয়েছে";
        sendSms($request->fatherMobile,$message1);


        return response()->json(['message' => 'User information updated successfully', 'user' => $user], 200);
    }


    public function deleteImage($id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('protected')->delete($user->image);
            $user->image = null;
            $user->save();
        }

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function getUsers(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::paginate($perPage);

        return response()->json($users, 200);
    }




    public function donate(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phoneNumber' => 'required|string',
            'email' => 'required|string|email',
            'currency' => 'required|string',
            'amount' => 'required|numeric',
            'address' => 'required|string',
            'donatePurpose' => 'required|string',
            'agreement' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doner = Doner::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'phoneNumber' => $request->phoneNumber,
            'email' => $request->email,
            'currency' => $request->currency,
            'amount' => $request->amount,
            'address' => $request->address,
            'donatePurpose' => $request->donatePurpose,
            'agreement' => $request->agreement
        ]);

        $user = $id ? User::find($id) : null;
        $amount = $request->amount;
        $applicant_mobile = $request->phoneNumber;
        $trnx_id = ($user ? $user->id : 'guest') . '-' . time();

        $cust_info = [
            "cust_email" => $request->email,
            "cust_id" => $id ? $id : 'guest',
            "cust_mail_addr" => $request->address,
            "cust_mobo_no" => $applicant_mobile,
            "cust_name" => $request->firstName . ' ' . $request->lastName
        ];

        $redirect_url = ekpayToken($trnx_id, $amount, $cust_info, 'payment');

        $req_timestamp = date('Y-m-d H:i:s');
        $customerData = [
            'union' => '-',
            'trxId' => $trnx_id,
            'donate_for' => $id ? $id : null,
            'sonodId' => $doner->id ? $doner->id : null,
            'sonod_type' => 'patient-donate',
            'amount' => $amount,
            'applicant_mobile' => $applicant_mobile,
            'status' => "Pending",
            'paymentUrl' => $redirect_url,
            'method' => 'ekpay',
            'payment_type' => 'online',
            'year' => date('Y'),
            'month' => date('F'),
            'date' => date('Y-m-d'),
            'created_at' => $req_timestamp,
            'donate_for' => $user ? $user->id : null,
        ];
        Payment::create($customerData);

        return $redirect_url;
    }




    // Additional methods as previously defined (donate, updateDescriptions, etc.) ...
}
