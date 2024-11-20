<?php

namespace App\Helpers;

use Exception;

class PaystackHelper
{
    public static function initiatePayment($email, $amount, $reference)
    {
        try {
            $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

            $response = $paystack->transaction->initialize([
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo (Paystack works in kobo)
                'reference' => $reference,
                'callback_url' => route('paystack.callback'),
            ]);

            return $response;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function verifyPayment($reference)
    {
        try {
            $paystack = new \Yabacon\Paystack(env('PAYSTACK_SECRET_KEY'));

            $response = $paystack->transaction->verify($reference);

            return $response;
        } catch (Exception $e) {
            return null;
        }
    }
}
