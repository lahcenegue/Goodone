<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SmsPasswordResetController extends Controller
{
    /**
     * Send password reset code via email
     * Uses reset_token fields (separate from email verification)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'success' => false
            ], 404);
        }

        // Check if user account is verified
        if (!$user->verified) {
            return response()->json([
                'message' => 'Account is not verified. Please verify your account first.',
                'success' => false
            ], 400);
        }

        // Check if user account is blocked
        if ($user->blocked) {
            return response()->json([
                'message' => 'Account is blocked. Please contact support.',
                'success' => false
            ], 403);
        }

        try {
            // Generate a random 6-digit token for password reset
            $token = (string) rand(100000, 999999);

            // Store in reset token fields (NOT verification token fields)
            $user->update([
                'reset_token' => $token, 
                'reset_token_expiry' => now()->addMinutes(15)
            ]);

            // Send the reset code via email
            $this->sendResetCodeEmail($user->email, $token);

            Log::info('Password reset code sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'expires_at' => now()->addMinutes(15)->toISOString()
            ]);

            return response()->json([
                'message' => 'Password reset code sent to your email',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to send password reset code', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to send reset code. Please try again later.',
                'success' => false
            ], 500);
        }
    }

    /**
     * Reset password using the reset token
     * Uses reset_token fields (separate from email verification)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required_if:email,null|numeric',
            'email' => 'required_if:phone,null|email',
            'reset_token' => 'required|string|size:6',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Find user by phone or email
            if ($request->phone) {
                $user = User::where('phone', $request->phone)->first();
            } else {
                $user = User::where('email', $request->email)->first();
            }

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'success' => false
                ], 404);
            }

            // Validate reset token using the User model helper method
            if (!$user->isValidResetToken($request->reset_token)) {
                Log::warning('Invalid password reset token attempt', [
                    'user_id' => $user->id,
                    'provided_token' => $request->reset_token,
                    'token_expired' => $user->reset_token_expiry ? $user->reset_token_expiry->isPast() : true,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'Invalid or expired reset token',
                    'success' => false
                ], 400);
            }

            // Update password
            $user->update([
                'password' => bcrypt($request->password),
            ]);

            // Clear reset token using the User model helper method
            $user->clearResetToken();

            Log::info('Password reset completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'message' => 'Password reset successfully',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            Log::error('Password reset operation failed', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? null,
                'phone' => $request->phone ?? null,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'message' => 'Password reset failed. Please try again later.',
                'success' => false
            ], 500);
        }
    }

    /**
     * Send password reset code via email
     *
     * @param string $email
     * @param string $token
     * @return void
     * @throws \Exception
     */
    private function sendResetCodeEmail($email, $token)
    {
        $message = "Your password reset code is: {$token}. This code expires in 15 minutes. If you didn't request this, please ignore this email.";
        
        Mail::to($email)->send(new OtpMail($message));
    }
}