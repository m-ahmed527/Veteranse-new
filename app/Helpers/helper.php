<?php

use App\Models\Tax;
use Stripe\Account;
use Stripe\AccountLink;
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

function setStripeKey()
{
    Stripe::setApiKey(env("STRIPE_SECRET_KEY"));
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

function getStripeAccount($vendorId)
{
    setStripeKey();
    $account = Account::retrieve($vendorId);
    if ($account) {
        return $account;
    }
    return null;
}
