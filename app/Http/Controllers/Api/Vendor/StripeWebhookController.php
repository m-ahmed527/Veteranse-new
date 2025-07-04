<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{



    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env("STRIPE_WEBHOOK_SECRET");

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Webhook Error: ' . $e->getMessage(), 400);
        }

        $type = $event->type;
        $data = $event->data->object;
        // Log the event for debugging
        \Log::info('Stripe Webhook Event', ['type' => $type, 'data' => $data]);
        // Handle subscription status updates
        if (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted', 'invoice.payment_succeeded', 'invoice.payment_failed'])) {
            $user = User::where('stripe_subscription_id', $data->id)->first();
            if ($user) {
                // $user->subscription_status = $data->status; // active, canceled, incomplete, etc.
                $user->subscription_ends_at = $data->cancel_at ? now()->timestamp($data->cancel_at) : ($data->canceled_at ? now()->timestamp($data->canceled_at) : null);
                $user->has_subscribed = $data->status == 'active' ? 1 : 0; // Assuming user has subscribed if we receive this event
                if ($data->status == 'active') {
                    $user->stripe_payment_status = 'paid';
                } elseif ($data->status == 'incomplete') {
                    $user->stripe_payment_status = 'failed';
                }
                $user->save();
            }
        }

        return response()->json(['status' => 'Webhook handled']);
    }
}
