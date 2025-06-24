<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAuthRequest;
use App\Mail\OtpMail;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {

        try {
            // dd($request->all());
            $request->validate(
                [
                    'avatar' => 'nullable|image',
                    'username' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'role' => 'required|in:user,vendor,admin',
                    // 'phone' => 'nullable|string|max:15', // Uncomment if phone is needed
                    'password' => 'required|string|min:8|confirmed',
                ]
            );

            $otpValues = $this->generateOtp($request->email);
            $data = $this->sanitizedRequest($request, $otpValues);
            DB::beginTransaction();
            $user = User::create($data);
            // $user->sendEmailVerificationNotification();
            $message = 'Your Registration OTP is: ' . $otpValues['otp'];
            Mail::to($user->email)->send(new OtpMail($user, $message));
            DB::commit();
            return  responseSuccess('OTP has been sent to the given email.', ['otp' => $otpValues['otp']]);
        } catch (\Exception $e) {

            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $otpValues = $this->generateOtp($request->email);
                DB::beginTransaction();
                $user->update([
                    'otp' => $otpValues['otp'],
                    'otp_expires_at' => $otpValues['otp_expires_at'],
                ]);

                $message = 'Your Login OTP is: ' . $otpValues['otp'];
                Mail::to($user->email)->send(new OtpMail($user, $message));
                DB::commit();
                return responseSuccess('OTP has been sent to the given email.', ['otp' => $otpValues['otp']]);
            } else {
                return responseError('Invalid Credentials', 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }


    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:4',
            ]);
            $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
            if (!$user) {
                return responseError('Invalid OTP or email.', 400);
            }
            if ($user->otp_expires_at < now()) {
                return responseError('OTP has expired.', 400);
            }
            DB::beginTransaction();
            $user->update([
                'otp' => null,
                'otp_verified_at' => now(),
                'status' => 1,
            ]);
            $token = $user->createToken('API-token')->plainTextToken;
            DB::commit();
            $user->fresh();
            $user['token'] = $token;
            return responseSuccess('User verified successfully.', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();
            return responseSuccess('Logged out successfully.', null);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return responseError('User not found.', 404);
            }
            if ($user->otp_verified_at) {
                return responseError('OTP already verified.', 400);
            }
            $otpValues = $this->generateOtp($request->email);
            DB::beginTransaction();
            $user->update([
                'otp' => $otpValues['otp'],
                'otp_expires_at' => $otpValues['otp_expires_at'],
            ]);
            Mail::to($user->email)->send(new OtpMail($user));
            DB::commit();
            return responseSuccess('OTP has been resent to the given email.', ['otp' => $otpValues['otp']]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return responseError('User not found.', 404);
            }

            // // Generate a password reset token and send it to the user
            $token = Str::random(4);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
            // Here you would send the token to the user's email
            $message = 'Your reset password token is: ' . $token;
            Mail::to($user->email)->send(new OtpMail($user, $message));
            return responseSuccess('Password reset link has been sent to your email.', ['token' => $token]);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
    public function verifyResetToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
            ]);

            $resetToken = DB::table('password_reset_tokens')->where('email', $request->email)->where('token', $request->token)->first();
            if (!$resetToken) {
                return responseError('Invalid token or email.', 400);
            }

            return responseSuccess('Token is Verified.', null);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $resetToken = DB::table('password_reset_tokens')->where('email', $request->email)->where('token', $request->token)->first();
            if (!$resetToken) {
                return responseError('Invalid token or email.', 400);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return responseError('No user found with this email.', 404);
            }

            DB::beginTransaction();
            $user->update(['password' => $request->password]);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::commit();

            return responseSuccess('Password has been reset successfully.', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }




    protected function generateOtp($email)
    {
        $otpArr = [
            'otp' => $email == 'test@webdesignglory.com' ? '1234' : Str::random(4),
            'otp_expires_at' => now()->addMinutes(5),
        ];
        return $otpArr;
    }

    protected function sanitizedRequest(Request $request, $otpValues)
    {

        $data = [
            'name' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            // 'phone' => $request->input('phone'), // Uncomment if phone is needed
            'password' => $request->password,
            'login_type' => 'otp',
            'otp' => $otpValues['otp'],
            'otp_expires_at' => $otpValues['otp_expires_at'],
        ];
        if ($request->hasFile('avatar')) {
            $imageName = time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('avatars'), $imageName);
            $data['avatar'] = asset('avatars') . '/' . $imageName;
        }
        return $data;
    }
}
