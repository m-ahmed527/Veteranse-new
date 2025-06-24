<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        try {
            // ✅ Step 1: Validate the input — plan_id is required and must exist in 'plans' table
            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'payment_method' => 'required_if:plan_id,' . Plan::where('slug', 'titanium')->value('id') // Titanium requires card
            ]);
            DB::beginTransaction();
            $user = auth()->user(); // 🎯 Authenticated user (vendor/customer)

            // ✅ Step 2: Get plan from DB
            $plan = Plan::findOrFail($request->plan_id); // contains Stripe price ID, slug, name, etc.
            // ✅ Step 3: Set Stripe secret key
            Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

            // ✅ Step 4: Create Stripe customer if not exists
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                $user->stripe_customer_id = $customer->id; // e.g. cus_QtZ90sWkG6cN3v
            }
            // dd($customer, $request->payment_method);
            // ✅ Step 5: Attach payment method if plan is paid (like titanium)
            if ($plan->slug == 'titanium-plan') {
                $paymentMethod = PaymentMethod::retrieve($request->payment_method);

                // ✅ detach it first (in case it was previously attached elsewhere)
                if ($paymentMethod->customer && $paymentMethod->customer != $user->stripe_customer_id) {
                    $paymentMethod->detach();
                }

                // ✅ then attach it
                $paymentMethod->attach([
                    'customer' => $user->stripe_customer_id,
                ]);
                // dd($paymentMethod->id);
                // Set this as default card for future invoices
                Customer::update($user->stripe_customer_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod->id,
                    ],
                ]);

                // Save payment method on user for future reference
                $user->stripe_payment_intent = $request->payment_method; // e.g. pm_1PR5nxLJYAxLxyz
            }
            // ✅ Step 6: Create subscription on Stripe
            $subscription = Subscription::create([
                'customer' => $user->stripe_customer_id,              // Stripe customer ID (e.g. cus_xxx)
                'items' => [['price' => $plan->stripe_price_id]],     // Stripe price ID for the plan (e.g. price_abc123)
                // 'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],        // To fetch the payment intent for frontend payment confirmation
            ]);

            // ✅ Step 7: Save subscription info to user
            $user->stripe_subscription_id = $subscription->id;                          // e.g. sub_1P9x2qLJYAxLxyz
            $user->stripe_price = $plan->stripe_price_id;                               // e.g. price_abc123
            $user->subscription_plan = $plan->name;                                     // e.g. 'gold' or 'titanium'
            $user->plan_id = $plan->id;                                                 // Foreign key to plans table
            $user->subscription_status = $subscription->status;                         // e.g. 'incomplete', 'active'
            $user->subscription_started_at = now();                                     // You may also fetch from Stripe if needed
            $user->subscription_renew_at = $subscription->items->data[0]->current_period_end ? now()->timestamp($subscription->items->data[0]->current_period_end, $subscription) : null;                            // Default to 1 month, will be updated via webhook
            $user->subscription_ends_at = null;                                         // Will be updated via webhook
            $user->has_subscribed = true;                                               // Custom field to track that user has subscribed
            $user->save();
            // dd($subscription->items->data[0]->current_period_end, $subscription);
            DB::commit();
            // ✅ Step 8: Return response to frontend
            return responseSuccess('Subscription created successfully', [
                'subscription_id' => $user->stripe_subscription_id,
                // 'payment_intent' => $subscription->latest_invoice->payment_intent,
                'status' => $subscription->status,
                'user' => $user->load('plan'),
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
}
