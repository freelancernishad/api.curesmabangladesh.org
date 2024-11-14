<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a list of payments filtered by donation status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getPaymentsByDonationStatus(Request $request)
    {
        // Check for the 'withDonation' query parameter
        $withDonation = $request->query('withDonation', false);

        // Get the 'per_page' query parameter (default to 10 if not provided)
        $perPage = $request->query('per_page', 10);


        // Start the query for "Paid" payments
        $paymentsQuery = Payment::where('status', 'Paid');

        // Apply the 'donate_for' filter based on the 'withDonation' query parameter
        if ($request->has('withDonation')) {
            if ($withDonation) {
                // Include only "Paid" payments with a value in 'donate_for'
                $paymentsQuery->whereNotNull('donate_for');
            } else {
                // Include only "Paid" payments without a value in 'donate_for'
                $paymentsQuery->whereNull('donate_for');
            }
        }

        // Paginate the payments based on the query parameters (per_page and page)
        $payments = $paymentsQuery->paginate($perPage);

        // Return the paginated result as JSON
        return response()->json($payments);
    }
}