<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // Set this in .env

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            switch ($event['type']) {
                case 'customer.subscription.created':
                    // Handle subscription creation
                    break;
                case 'invoice.payment_succeeded':
                    // Handle successful payment
                    break;
                case 'customer.subscription.deleted':
                    // Handle subscription cancellation
                    break;
                // Add more cases as needed
            }
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return response()->json(['status' => 'success']);
    }
}

