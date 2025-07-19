<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ServiceGallary;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\AppSetting;
use Twilio\Rest\Client;
use App\Mail\OtpMail;
use App\Mail\AccountDeletionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', "register", "sendVerificationCode", "verifyAccount"]]);
    }

    /**
     * edit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $validation = $request->validate([
            'email' => 'email|unique:users,email',
            'password' => 'string',
            'phone' => 'numeric',
            'location' => 'string',
            'city' => 'string',
            'country' => 'string',
            'full_name' => 'string',
            "picture" => "file",
        ]);
        
        if(isset($validation["password"])) {
            $validation["password"] = bcrypt($validation["password"]);
        }
        
        $validation["id"] = auth("api")->user()->id;
        
        if($request->file('picture')){
            $file = $request->file('picture');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["picture"] = $file_name;
        }

        if ($validation) {
            User::where([["id", "=", auth("api")->user()->id]])->update($validation);
            $updated = Auth("api")->user()->fresh();
            return response()->json($updated);
        } else {
            return response()->json(['error' => 'Bad Request'], 400);
        }
    }

    /**
     * register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validation = $request->validate([
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
            'phone' => 'required|numeric',
            'type' => 'required|in:customer,worker',
            'full_name' => 'required',
            'city' => 'string',
            'country' => 'string',
            "device_token" => "nullable|string",
            "picture" => "file|sometimes",
        ]);
        
        if(isset($validation["password"])) {
            $validation["password"] = bcrypt($validation["password"]);
        }
        
        if($request->file('picture')){
            $file = $request->file('picture');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["picture"] = $file_name;
        } else {
            $customer = AppSetting::where("key", "=", "customer-image");
            $provider = AppSetting::where("key", "=", "provider-image");
            $customer_image = "";
            $provider_image = "";
            
            if($customer->count() > 0) {
                $customer_image = $customer->first()->value;
            }
            if($provider->count() > 0) {
                $provider_image = $provider->first()->value;
            }
            
            if($validation["type"] == "worker") {
                $validation["picture"] = $provider_image;
            } else {
                $validation["picture"] = $customer_image;
            }
        }

        if ($validation) {
            $user = User::create($validation);
            $token = $this->createOtpCode($user->email);
            $this->sendOtpCode($user->email, $token);
            return response()->json(['message' => 'Successfully created account, Otp code is sent to email']);
        } else {
            return response()->json(['error' => 'Bad Request'], 400);
        }
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $token = $this->createOtpCode($request->email);
        $sent_otp = $this->sendOtpCode($user->email, $token);

        return response()->json(['message' => 'Otp code sent via email']);
    }

    // Verify the reset code and reset the password
    public function verifyAccount(Request $request)
    {
        $request->validate([
            'phone' => 'required_if:email,null|numeric',
            'email' => 'required_if:phone,null',
            'otp' => 'required|numeric',
        ]);

        if($request->phone){
            $user = User::where('phone', $request->phone)->first();
        } else {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if token is valid and not expired
        if ($user->reset_token !== $request->otp || $user->reset_token_expiry < now()) {
            return response()->json(['message' => 'Invalid or expired reset token'], 400);
        }

        $user->update([
            'reset_token' => null,
            'reset_token_expiry' => null,
            'verified' => true
        ]);

        return $this->respondWithToken(auth("api")->login($user));
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $user = User::where("email", "=", $credentials["email"]);
        
        if($user->count() > 0 && $user->first()["blocked"] == true) {
            return response()->json(['error' => 'Account is blocked'], 403);
        }
        if($user->count() > 0 && $user->first()["verified"] == false) {
            return response()->json(['error' => 'Account is not verified'], 401);
        }

        if (!$token = auth("api")->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        if (request("device_token")) {
            $user->update(["device_token" => request("device_token")]);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth("api")->user();
        $_active = Service::where([["user_id", "=", $user["id"]]]);
        $active = false;
        
        if($_active->count() > 0) {
            $active = $_active->first()->active;
        }
        
        $user["active"] = $active;
        unset($user["verified_liscence"]);
        unset($user["location"]);
        
        return response()->json($user);
    }

    /**
     * Delete user account permanently with comprehensive cleanup
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete_account(Request $request)
    {
        // Validate the request
        $validation = $request->validate([
            'reason' => 'required|string|max:255',
            'additional_feedback' => 'nullable|string|max:1000',
        ]);

        $user = auth("api")->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'success' => false
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Log the account deletion for audit purposes
            Log::info('Account deletion requested', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_type' => $user->type,
                'reason' => $validation['reason'],
                'additional_feedback' => $validation['additional_feedback'] ?? null,
                'deleted_at' => now()
            ]);

            // Store deletion information before cleanup (optional - for compliance/audit)
            $this->storeDeletionRecord($user, $validation);

            // Store user info for email before deletion
            $userEmail = $user->email;
            $userFullName = $user->full_name;
            $userType = $user->type;

            // Comprehensive cleanup based on user type
            $this->cleanupUserData($user);

            // Delete the user account
            $user->delete();

            DB::commit();

            // Send account deletion confirmation email (after successful deletion)
            try {
                $this->sendAccountDeletionConfirmation($userEmail, $userFullName, $userType);
            } catch (\Exception $emailException) {
                // Log email error but don't fail the deletion
                Log::warning('Failed to send deletion confirmation email', [
                    'user_email' => $userEmail,
                    'error' => $emailException->getMessage()
                ]);
            }

            return response()->json([
                'message' => 'Account deleted successfully. We\'re sorry to see you go!',
                'success' => true,
                'deleted_at' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to delete account. Please try again later.',
                'success' => false
            ], 500);
        }
    }

    /**
     * Store deletion record for audit/compliance purposes
     */
    private function storeDeletionRecord($user, $validation)
    {
        try {
            // Create a deletion record table if needed for GDPR compliance
            DB::table('account_deletions')->insert([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_type' => $user->type,
                'deletion_reason' => $validation['reason'],
                'additional_feedback' => $validation['additional_feedback'] ?? null,
                'deleted_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Don't fail the deletion if audit logging fails
            Log::warning('Failed to store deletion record', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Comprehensive cleanup of user-related data
     */
    private function cleanupUserData($user)
    {
        try {
            // Delete user's profile picture if it exists and is not a default image
            if ($user->picture && !$this->isDefaultImage($user->picture)) {
                Storage::delete('public/images/' . $user->picture);
            }

            // Cleanup based on user type
            if ($user->type === 'worker') {
                $this->cleanupWorkerData($user);
            } else {
                $this->cleanupCustomerData($user);
            }

            // Delete general user-related data
            $this->cleanupGeneralUserData($user);

        } catch (\Exception $e) {
            Log::error('User data cleanup failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw to trigger rollback
        }
    }

/**
 * Cleanup worker-specific data
 */
private function cleanupWorkerData($user)
{
    // Get all service IDs for this worker
    $serviceIds = DB::table('services')->where('user_id', $user->id)->pluck('id')->toArray();
    
    if (!empty($serviceIds)) {
        // Delete service_earnings for orders placed on their services
        DB::statement("DELETE FROM service_earnings WHERE order_id IN (SELECT id FROM 'order' WHERE service_id IN (" . implode(',', $serviceIds) . "))");
        
        // Delete orders placed for their services
        DB::table('order')->whereIn('service_id', $serviceIds)->delete();
        
        // Delete ratings received on their services
        DB::table('rating')->whereIn('service_id', $serviceIds)->delete();
        
        // Delete service gallery images
        foreach ($serviceIds as $serviceId) {
            $galleries = ServiceGallary::where('service_id', $serviceId)->get();
            foreach ($galleries as $gallery) {
                if ($gallery->image) {
                    Storage::delete('public/images/' . $gallery->image);
                }
            }
            ServiceGallary::where('service_id', $serviceId)->delete();
        }
        
        // Finally delete the services themselves
        Service::where('user_id', $user->id)->delete();
    }
}

    /**
     * Cleanup customer-specific data
     */
    private function cleanupCustomerData($user)
    {
        // Delete customer-specific data if needed
        // Add your customer-specific cleanup logic here
    }

/**
 * Cleanup general user data (applies to both customer and worker)
 */
private function cleanupGeneralUserData($user)
{
    // Delete service_earnings records tied to this user's orders first
    DB::statement("DELETE FROM service_earnings WHERE order_id IN (SELECT id FROM 'order' WHERE user_id = ?)", [$user->id]);
    
    // Delete user's direct service_earnings (if any)
    DB::table('service_earnings')->where('user_id', $user->id)->delete();
    
    // Now we can safely delete the orders
    DB::table('order')->where('user_id', $user->id)->delete();
    
    // Delete user's ratings
    DB::table('rating')->where('user_id', $user->id)->delete();
    
    // Delete user's withdraw requests
    DB::table('withdraw_requests')->where('user_id', $user->id)->delete();
}


    /**
     * Check if the image is a default system image
     */
    private function isDefaultImage($imageName)
    {
        $defaultImages = [];
        
        // Get default customer image
        $customerImage = AppSetting::where("key", "=", "customer-image")->first();
        if ($customerImage) {
            $defaultImages[] = $customerImage->value;
        }
        
        // Get default provider image
        $providerImage = AppSetting::where("key", "=", "provider-image")->first();
        if ($providerImage) {
            $defaultImages[] = $providerImage->value;
        }
        
        return in_array($imageName, $defaultImages);
    }

    /**
     * Send account deletion confirmation email
     */
    private function sendAccountDeletionConfirmation($userEmail, $userFullName, $userType)
    {
        try {
            Mail::to($userEmail)->send(new AccountDeletionMail($userFullName, $userType));
            
            Log::info('Account deletion confirmation email sent', [
                'email' => $userEmail
            ]);
        } catch (\Exception $e) {
            // Log the error and re-throw it
            Log::warning('Failed to send deletion confirmation email', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth("api")->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth("api")->refresh());
    }

    protected function sendOtpCode($email, $token)
    {
        $this->sendEmail($email, "Your Otp code is: $token");
    }

    protected function createOtpCode($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return 404;
        }

        // Generate a random token for password reset
        $token = rand(100000, 999999);
        $user->update(['reset_token' => $token, 'reset_token_expiry' => now()->addMinutes(5)]);
        return $token;
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth("api")->factory()->getTTL() * 60
        ]);
    }

    // Helper function to send SMS using Twilio
    protected function sendSms($to, $message)
    {
        // SMS sending implementation
    }

    // Helper function to send email
    protected function sendEmail($to, $message)
    {
        Mail::to($to)->send(new OtpMail($message));
    }
}