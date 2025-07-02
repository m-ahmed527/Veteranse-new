<?php

use App\Models\Tax;
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
