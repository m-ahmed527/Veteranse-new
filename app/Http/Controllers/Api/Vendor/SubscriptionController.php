<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionController extends Controller
{





    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'payment_method' => 'required_if:plan_id,' . Plan::where('slug', 'titanium')->value('id')
            ]);

            $user = auth()->user();
            $plan = Plan::findOrFail($request->plan_id);

            if ($user->has_subscribed && $user->subscription_status == 'active') {
                if ($user->plan_id == $plan->id) {
                    return responseError('You are already subscribed to this plan.', 400);
                }
                return responseError('Please cancel your current subscription before changing plans.', 400);
            }

            // ✅ FREE PLAN
            if ($plan->slug == 'gold-plan') {
                $this->saveSubscriptionToUser($user, $plan, null, 'free');
                return responseSuccess('Free Subscription created successfully', [
                    'user' => $user->load('plan'),
                ]);
            }

            // ✅ PAID PLAN — Try wallet first
            // if ($user->wallet_balance >= $plan->price) {
            //     DB::beginTransaction();
            //     $this->payWithWallet($user, $plan);
            //     DB::commit();
            //     return responseSuccess('Subscription activated using wallet.', [
            //         'wallet_used' => true,
            //         'user' => $user,
            //     ]);
            // }

            // ✅ INSUFFICIENT WALLET BALANCE — Use Stripe
            setStripeKey();
            $this->createStripeCustomer($user);

            if ($plan->slug == 'titanium-plan') {
                $this->attachPaymentMethod($user, $request->payment_method);
            }

            $subscription = $this->createStripeSubscription($user, $plan);

            DB::beginTransaction();
            $this->saveSubscriptionToUser($user, $plan, 'card', $subscription);
            DB::commit();

            return responseSuccess('Subscription created using card.', [
                'wallet_used' => false,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
    public function subscribeWithWallet(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
            ]);

            $user = auth()->user();
            $plan = Plan::findOrFail($request->plan_id);

            if ($user->has_subscribed && $user->subscription_status == 'active') {
                return responseError('You are already subscribed to a plan.', 400);
            }

            if ($user->wallet_balance < $plan->price) {
                return responseError('Insufficient wallet balance,please use other payment method.', 400);
            }

            DB::beginTransaction();
            $this->payWithWallet($user, $plan);
            DB::commit();

            return responseSuccess('Subscription activated using wallet.', [
                'wallet_used' => true,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
    public function cancelSubscription()
    {
        try {
            $user = auth()->user();

            if (!$user?->subscription_status || $user?->subscription_status != 'active') {
                return responseError('No active subscription found to cancel.', 400);
            }
            if (!$user->stripe_subscription_id) {
                // If no subscription ID, but has free plan, just update status
                DB::beginTransaction();
                $this->cancelFreeOrWalletSubscription($user);
                DB::commit();
                return responseSuccess('Subscription canceled successfully.', $user);
            }


            setStripeKey();

            $subscription = Subscription::retrieve($user->stripe_subscription_id);

            // ✅ Check if within 7 days of subscription start
            $startedAt = Carbon::parse($user->subscription_started_at);

            $diffDays = $startedAt->diffInRealDays(now());
            DB::beginTransaction();
            $this->cancelPaidSubscription($diffDays, $subscription, $user);
            DB::commit();

            return responseSuccess('Subscription canceled successfully.', [
                'wallet_refunded' => $diffDays <= 7,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError('Error: ' . $e->getMessage(), 400);
        }
    }







    private function createStripeCustomer($user)
    {
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->stripe_customer_id = $customer->id; // e.g. cus_QtZ90sWkG6cN3v
        }
    }
    private function updateStripeCustomer($user, $paymentMethod)
    {
        // Set this as default card for future invoices
        Customer::update($user->stripe_customer_id, [
            'invoice_settings' => [
                'default_payment_method' => $paymentMethod->id,
            ],
        ]);
    }
    private function attachPaymentMethod($user, $paymentMethodId)
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

        // Detach it first if it was previously attached elsewhere
        if ($paymentMethod->customer && $paymentMethod->customer != $user->stripe_customer_id) {
            $paymentMethod->detach();
        }

        // Attach it to the user
        $paymentMethod->attach([
            'customer' => $user->stripe_customer_id,
        ]);

        $this->updateStripeCustomer($user, $paymentMethod);

        // Save payment method on user for future reference
        $user->stripe_payment_intent = $paymentMethodId; // e.g. pm_1PR5nxLJYAxLxyz
    }
    private function createStripeSubscription($user, $plan)
    {
        // Create subscription on Stripe
        $subscription = Subscription::create([
            'customer' => $user->stripe_customer_id,              // Stripe customer ID (e.g. cus_xxx)
            'items' => [['price' => $plan->stripe_price_id]],     // Stripe price ID for the plan (e.g. price_abc123)
            'expand' => ['latest_invoice.payment_intent'],        // To fetch the payment intent for frontend payment confirmation
        ]);

        return $subscription;
    }
    private function saveSubscriptionToUser($user,  $plan, $paymentType, $subscription = null)
    {
        $user->stripe_subscription_id = $subscription?->id ?? null;                          // e.g. sub_1P9x2qLJYAxLxyz
        $user->stripe_price = $plan->stripe_price_id;                               // e.g. price_abc123
        $user->subscription_plan = $plan->name;                                     // e.g. 'gold' or 'titanium'
        $user->plan_id = $plan->id;                                                 // Foreign key to plans table
        $user->subscription_status = $subscription?->status ?? 'active';                         // e.g. 'incomplete', 'active'
        $user->subscription_started_at = now();                                     // You may also fetch from Stripe if needed
        $user->subscription_ends_at = null;                                         // Will be updated via webhook
        $user->has_subscribed = 1;                                               // Custom field to track that user has subscribed
        $user->stripe_payment_type = $paymentType;                                       // e.g. 'card', 'wallet', 'free'
        if ($paymentType == 'card') {
            $user->subscription_renew_at = $subscription?->items?->data[0]?->current_period_end ? now()->timestamp($subscription?->items?->data[0]->current_period_end) : null;
        } else {
            $user->subscription_renew_at = now()->addMonth();
        }
        $user->save();
    }
    private function payWithWallet($user, $plan)
    {
        $user->debitWallet($plan->price, "Subscribed to {$plan->name} plan via wallet");
        $user->stripe_payment_status = 'wallet_used';
        $this->saveSubscriptionToUser($user, $plan, 'wallet', null);
    }
    private function cancelFreeOrWalletSubscription($user)
    {
        $user->subscription_status = null;
        $user->stripe_payment_intent = null; // Clear payment intent
        $user->plan_id = null; // Clear plan ID
        $user->subscription_plan = null; // Clear plan name
        $user->stripe_price = null; // Clear Stripe price ID
        $user->subscription_ends_at = now();
        // $user->stripe_subscription_id = null; // Clear subscription ID
        $user->has_subscribed = 0; // Reset subscription flag
        $user->save();
    }

    private function cancelPaidSubscription($diffDays, $subscription, $user)
    {

        if ($diffDays <= 7) {
            // ✅ Cancel immediately
            $subscription->cancel(['invoice_now' => true, 'prorate' => false]);

            // ✅ Get last payment amount from invoice and credit to wallet
            $latestInvoiceId = $subscription->latest_invoice;
            $invoice = Invoice::retrieve($latestInvoiceId);

            if ($invoice && isset($invoice->amount_paid)) {
                $amount = $invoice->amount_paid / 100; // Stripe returns amount in cents

                // ✅ Credit to wallet via helper
                $user->creditWallet($amount, 'Refund for subscription cancellation (within 7 days)');
                $user->stripe_payment_status = 'wallet_refunded';
            }
            $user->stripe_payment_intent = null; // Clear payment intent
            $user->plan_id = null; // Clear plan ID
            $user->subscription_plan = null; // Clear plan name
            $user->stripe_price = null; // Clear Stripe price ID
            $user->subscription_status = null;
            // $user->subscription_ends_at = now(); // Set end date to now
        } else {
            // ✅ Cancel at period end
            // $subscription->cancel();
            Subscription::update($subscription->id, [
                'cancel_at_period_end' => true,
            ]);
            $user->subscription_status = 'canceled';
        }
        $user->has_subscribed = 0; // Reset subscription flag
        $user->save();
    }
}

















// public function subscribe(Request $request)
    // {
    //     try {
    //         // ✅ Step 1: Validate the input — plan_id is required and must exist in 'plans' table
    //         $request->validate([
    //             'plan_id' => 'required|exists:plans,id',
    //             'payment_method' => 'required_if:plan_id,' . Plan::where('slug', 'titanium')->value('id') // Titanium requires card
    //         ]);

    //         $user = auth()->user(); // 🎯 Authenticated user (vendor/customer)
    //         // ✅ Step 2: Get plan from DB
    //         $plan = Plan::findOrFail($request->plan_id); // contains Stripe price ID, slug, name, etc.
    //         if ($user->has_subscribed && $user->subscription_status == 'active') {
    //             if ($user->plan_id == $request->plan_id) {
    //                 return responseError('You are already subscribed to this plan', 400);
    //             }
    //             return responseError('You have already subscribed to a plan,Please cancel the previous one to upgrade/degrade', 400);
    //         }
    //         if ($plan->slug == "gold-plan") {
    //             $this->saveSubscriptionToUser($user,  $plan);
    //             return responseSuccess('Free Subscription created successfully', [
    //                 'user' => $user->load('plan'),
    //             ]);
    //         }
    //         // ✅ Step 3: Set Stripe secret key
    //         setStripeKey();
    //         // ✅ Step 4: Create Stripe customer if not exists
    //         $this->createStripeCustomer($user);


    //         // ✅ Step 5: Attach payment method if plan is paid (like titanium)
    //         if ($plan->slug == 'titanium-plan') {
    //             $this->attachPaymentMethod($user, $request->payment_method);
    //         }
    //         // ✅ Step 6: Update Stripe customer with default payment method
    //         $subscription = $this->createStripeSubscription($user, $plan);
    //         DB::beginTransaction();
    //         // ✅ Step 7: Save subscription details to user model
    //         $this->saveSubscriptionToUser($user, $plan, $subscription);

    //         DB::commit();
    //         // ✅ Step 8: Return response to frontend
    //         return responseSuccess('Subscription created successfully', [
    //             'user' => $user->load('plan'),
    //         ]);
    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         return responseError($e->getMessage(), 400);
    //     }
    // }





// public function cancelSubscription()
//     {
//         try {
//             $user = auth()->user();

//             if (!$user->stripe_subscription_id || $user->subscription_status !== 'active') {
//                 return responseError('No active subscription found to cancel.', 400);
//             }

//             setStripeKey();

//             $subscription = Subscription::retrieve($user->stripe_subscription_id);
//             // dd($subscription);
//             // ✅ Check if within 7 days of subscription start
//             $startedAt = Carbon::parse($user->subscription_started_at);
//             $diffDays = $startedAt->diffInRealDays(now());
//             // dd($diffDays <= 7, $startedAt, now(), $diffDays);
//             if ($diffDays <= 7) {
//                 // ✅ Immediate cancel
//                 $subscription->cancel(['invoice_now' => true, 'prorate' => false]);

//                 // ✅ Try refunding latest payment
//                 // $latestInvoiceId = $subscription->latest_invoice;
//                 // $invoice = Invoice::retrieve($latestInvoiceId);
//                 // if ($invoice && isset($invoice->payment_intent)) {
//                 //     $paymentIntentId = $invoice->payment_intent;
//                 //     Refund::create([
//                 //         'payment_intent' => $paymentIntentId,
//                 //     ]);
//                 // }

//                 // $user->subscription_status = 'canceled';
//                 // $user->subscription_ends_at = now();
//                 $user->stripe_payment_status = 'refunded';
//             } else {
//                 // ✅ Just mark cancel at end of period
//                 $subscription->cancel(); // cancel at period end
//                 // $user->stripe_payment_status = 'canceled';
//                 // $user->subscription_ends_at = now();
//             }

//             $user->save();

//             return responseSuccess('Subscription canceled successfully.', [
//                 'refund_given' => $diffDays <= 7,
//                 'user' => $user,
//             ]);
//         } catch (\Exception $e) {
//             return responseError($e->getMessage(), 400);
//         }
//     }
