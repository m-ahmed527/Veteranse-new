<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{



    // public function handle(Request $request)
    // {
    //     $payload = $request->getContent();
    //     $sigHeader = $request->header('Stripe-Signature');
    //     $secret = env("STRIPE_WEBHOOK_SECRET");

    //     try {
    //         $event = Webhook::constructEvent($payload, $sigHeader, $secret);
    //     } catch (\Exception $e) {
    //         return response('Webhook Error: ' . $e->getMessage(), 400);
    //     }

    //     $type = $event->type;
    //     $data = $event->data->object;
    //     // Log the event for debugging
    //     \Log::info('Stripe Webhook Event', ['type' => $type, 'data' => $data]);
    //     // Handle subscription status updates
    //     if (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted', 'invoice.payment_succeeded', 'invoice.payment_failed'])) {
    //         $user = User::where('stripe_subscription_id', $data->id)->first();
    //         if ($user) {
    //             // $user->subscription_status = $data->status; // active, canceled, incomplete, etc.
    //             $user->subscription_ends_at = $data->cancel_at ? now()->timestamp($data->cancel_at) : ($data->canceled_at ? now()->timestamp($data->canceled_at) : null);
    //             $user->has_subscribed = $data->status == 'active' ? 1 : 0; // Assuming user has subscribed if we receive this event
    //             if ($data->status == 'active') {
    //                 $user->stripe_payment_status = 'paid';
    //             } elseif ($data->status == 'incomplete') {
    //                 $user->stripe_payment_status = 'failed';
    //             }
    //             $user->save();
    //         }
    //     }

    //     return response()->json(['status' => 'Webhook handled']);
    // }


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

        \Log::info('Stripe Webhook Event', ['type' => $type, 'data' => $data]);

        switch ($type) {
            case 'customer.subscription.created':
                $this->handleSubscriptionCreatedEvent($data);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdatedEvent($data);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeletedEvent($data);
                break;

            case 'invoice.payment_succeeded':
            case 'invoice.payment_failed':
                $this->handleInvoiceEvent($data);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentEvent($data);
            default:
                \Log::info("Unhandled Stripe event type: $type");
                break;
        }

        return response()->json(['status' => 'Webhook handled']);
    }

    protected function handleSubscriptionCreatedEvent($data)
    {
        $user = User::where('stripe_subscription_id', $data->id)->first();
        if (!$user) return;

        if ($data->status == 'active') {
            $user->update([
                'stripe_payment_status' => $data->status,
                'subscription_status' => $data->status,
                'has_subscribed' => 1,
            ]);
        }
    }
    protected function handleSubscriptionUpdatedEvent($data)
    {
        $user = User::where('stripe_subscription_id', $data->id)->first();
        if (!$user) return;
        $user->update([
            'has_subscribed' => $data->status == 'active' ? 1 : 0,
            'subscription_ends_at' => $data->cancel_at ? Carbon::createFromTimestamp($data->cancel_at) : null,
        ]);
    }

    protected function handleSubscriptionDeletedEvent($data)
    {
        $user = User::where('stripe_subscription_id', $data->id)->first();
        if (!$user) return;

        $user->update([
            'has_subscribed' => 0,
            'subscription_ends_at' => $data->canceled_at ? Carbon::createFromTimestamp($data->canceled_at) : null,
        ]);
    }
    protected function handleInvoiceEvent($data)
    {


        $user = User::where('stripe_customer_id', $data->customer)->first();
        if (!$user) return;

        if ($data->status == 'paid') {
            $user->update([
                'stripe_payment_status' => $data->status,
                'has_subscribed' => 1,
            ]);
        }
    }

    protected function handlePaymentIntentEvent($data)
    {
        $user = User::where('stripe_customer_id', $data->customer)->first();
        if ($data->description == 'Booking') {
            $booking = $user->bookings()->where('payment_intent', $data->id)->first();
            if (!$user || !$booking) return;

            if ($data->status == 'succeeded') {
                $booking->update([
                    'payment_status' => $data->status,
                    'booking_status' => 'confirmed',
                ]);
            }
        } elseif ($data->description == 'Product') {
            $order = $user->orders()->where('payment_intent', $data->id)->first();
            if (!$user || !$order) return;
            if ($data->status == 'succeeded') {
                $order->update([
                    'payment_status' => $data->status,
                    'order_status' => 'placed',
                ]);
            }
        }
    }
}
