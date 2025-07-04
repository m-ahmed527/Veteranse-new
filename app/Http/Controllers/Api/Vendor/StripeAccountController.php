<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\StripeAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeAccountController extends Controller
{
    public function startOnBoardProcess()
    {
        try {
            $vendor = auth()->user();
            $account = getOrCreateStripeAccount($vendor);
            $getAccountLink = createAccountLink($account->stripe_account_id, [
                "return_url" => url()->current() . "/$account->stripe_account_id/return",
                "refresh_url" => url()->previous()
            ]);
            return responseSuccess('Stripe account link created successfully.', [
                'account_link' => $getAccountLink->url
            ]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function returnFromOnBoardProcess(Request $request, $account_id)
    {
        try {
            $stripeAccount = getStripeAccount($account_id);
            DB::beginTransaction();
            $account = StripeAccount::where("stripe_account_id", $account_id)->first();
            // dd($stripeAccount);
            $account->update([
                'charges_enabled' => $stripeAccount->charges_enabled,
            ]);
            $account->user()->update([
                'stripe_account_connected' => $stripeAccount->payouts_enabled ? true : false,
            ]);
            DB::commit();
            if ($account->charges_enabled) {
                $message = "Stripe connect account is successfuly added!";
            } else if ($stripeAccount->details_submitted == false) {
                $message = "Please try again, You haven't complete the stripe connect process!";
            } else {
                $message = "Stripe connect account is under review!";
            }
            return responseSuccess($message, [
                'stripe_account' => $account
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Delete a Stripe account.
     *
     * @param string $account_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStripeAccount($account_id)
    {
        try {
            $user = auth()->user();
            $account = StripeAccount::where("stripe_account_id", $account_id)->first();
            if (!$account) {
                return responseError('Stripe account not found.', 404);
            }
            $delete = deleteStripeAccount($account->stripe_account_id);
            if ($delete) {
                DB::beginTransaction();
                $account->user()->update([
                    'stripe_account_connected' => false,
                ]);
                $account->delete();

                DB::commit();
            }
            return responseSuccess('Stripe account deleted successfully.', $user->load('stripeAccount'));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
}
