<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    // public function index()
    // {
    //     return view('index');
    // }
    // public function checkout()
    // {
    //     \Stripe\Stripe::setApiKey(config('stripe.sk'));
    //     $session = \Stripe\Checkout\Session::create();
    // }
    // public function success()
    // {
    //     return view('index');
    // }

    // PAYMENT INTENT
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createPaymentIntent(Request $request)
    {
        $amount = $request->input('amount');

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }
}
