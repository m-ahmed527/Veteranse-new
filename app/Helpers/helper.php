<?php

use App\Models\Tax;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

function responseSuccess($message, $data = null)
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], 200);
}

function responseError($message, $statusCode = 400)
{
    return response()->json([
        'success' => false,
        'message' => $message,
    ], $statusCode);
}

function responseValidationError($errors, $statusCode = 422)
{
    return response()->json([
        'success' => false,
        'message' => 'Validation Error',
        'errors' => $errors,
    ], $statusCode);
}



function getTax()
{
    $tax = Tax::where('name', 'plateform tax')->where('is_active', true)->first();
    return $tax;
}

function getTaxRate()
{
    $tax = Tax::where('name', 'plateform tax')->where('is_active', true)->first();
    return $tax ? $tax->rate : 0; // Return the tax rate or 0 if not found
}

function setStripeKey()
{
    Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
}


//  Start of Stripe for Vendor Account
function getOrCreateStripeAccount($user)
{
    setStripeKey();
    if (!$user->stripeAccount) {
        $params = [
            "type" => 'express',
            'country' => 'US',
            'email' => $user->email,
            'settings' => [
                'payouts' => [
                    'schedule' => [
                        "delay_days" => null,
                        'interval' => 'manual',
                    ]
                ]
            ],
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ];
        $stripeAccount = Account::create($params);
        $account = $user->stripeAccount()->create([
            'stripe_account_id' => $stripeAccount->id,
            'charges_enabled' => $stripeAccount->charges_enabled,
        ]);

        return $account;
    }

    return $user->stripeAccount;
}

function createAccountLink($accountId, $config = [])
{
    setStripeKey();
    $account_links = AccountLink::create([
        'account' => $accountId,
        'refresh_url' => $config['refresh_url'] ?? '',
        'return_url' => $config['return_url'] ?? '',
        'type' => 'account_onboarding',
    ]);
    return  $account_links;
}

function getStripeAccount($accountId)
{
    setStripeKey();
    $account = Account::retrieve($accountId);
    if ($account) {
        return $account;
    }
    return null;
}



function deleteStripeAccount($accountId)
{
    setStripeKey();
    $account = getStripeAccount($accountId);
    if ($account) {
        $account->delete();
        return true;
    }
    return false;
}

// End of Stripe for Vendor Account



// Start of Stripe  for User payment
function createStripeCustomer($user)
{
    $customer = Customer::create([
        'email' => $user->email,
        'name' => $user->name,
        'metadata' => [
            'user_id' => $user->id,
        ],
        'description' => 'Customer for ' . $user->name,
    ]);
    return $customer;
}

function attachPaymentMethodToCustomer($paymentMethodId, $user)
{
    $paymentMethod = PaymentMethod::retrieve($paymentMethodId)
        ->attach(['customer' => $user->stripe_customer_id]);
    updateStripeCustomer($user, $paymentMethod);
    return $paymentMethod;
}

function updateStripeCustomer($user, $paymentMethod)
{
    Customer::update($user->stripe_customer_id, [
        'invoice_settings' => [
            'default_payment_method' => $paymentMethod->id,
        ],
    ]);
}

function createPaymentIntent($request, $amount, $user, $description = 'Booking', $metaData = [])
{
    $paymentIntent = PaymentIntent::create([
        'amount' => $amount * 100,
        'currency' => 'usd',
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never',
        ],
        'payment_method' => $request->payment_method_id,
        'description' => $description,
        'confirm' => true,
        'customer' => $user->stripe_customer_id ?? null,
        'metadata' => $metaData,
    ]);

    return $paymentIntent;
}

// End of Stripe for User payment
