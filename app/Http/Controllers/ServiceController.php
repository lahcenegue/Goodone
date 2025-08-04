<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\WithdrawRequest;
use App\Models\ServiceGallary;
use App\Models\RegionTax;
use App\Models\AppSetting;
use App\Models\Category;
use App\Models\ServiceEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    //



    /**
     * gallary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_to_gallary(Request $request)
    {
        $validation = $request->validate([
            "image" => "file|required",
            "service_id" => "required|exists:services,id"
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["image"] = $file_name;
        }


        if ($validation) {
            $gall = ServiceGallary::create($validation);
            return response()->json($gall);
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }

    /**
     * Get notifications with proper new/read tracking
     * Enhanced version with better error handling and consistent data structure
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_notifications(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $_notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->get();

            $notifications = [];

            foreach ($_notifications as $not) {
                if ($not->data_type == "order") {
                    $_order = Order::select("id", "total_hours", "start_at", "price", "location", "service_id", "status", "note")
                        ->with(['Service' => function ($query) {
                            $query->join('users', "users.id", "=", "services.user_id")
                                ->select('services.id', 'users.full_name', "users.picture", "services.service", "services.subcategory_id", "services.cost_per_hour");
                        }, 'Service.Subcategory' => function ($query) {
                            $query->select('id', 'name');
                        }])
                        ->where('id', $not->data)
                        ->first();

                    if ($_order && $_order->Service) {
                        $notifications[] = [
                            "id" => $not->id,
                            "text" => $not->text,
                            "user" => $_order->Service->full_name,
                            "picture" => $_order->Service->picture,
                            "order_id" => intval($not->data),
                            "created_at" => $not->created_at,
                            "is_new" => (bool) $not->is_new,
                            "is_read" => (bool) $not->is_read,
                            "seen_at" => $not->seen_at,
                            "read_at" => $not->read_at
                        ];
                    }
                }
            }

            // Log for debugging
            Log::info('Notifications fetched', [
                'user_id' => $user->id,
                'total_count' => count($notifications),
                'new_count' => collect($notifications)->where('is_new', true)->count()
            ]);

            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to fetch notifications',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Get count of new notifications (never seen before)
     * Enhanced with better error handling
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_new_notifications_count(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $count = Notification::where('user_id', $user->id)
                ->where('is_new', true)
                ->count();

            Log::info('New notifications count requested', [
                'user_id' => $user->id,
                'count' => $count
            ]);

            return response()->json(['new_notifications_count' => $count], 200);
        } catch (\Exception $e) {
            Log::error('Error getting new notifications count', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to get notification count',
                'new_notifications_count' => 0
            ], 500);
        }
    }

    /**
     * Get count of unread notifications (seen but not marked as read)
     * Enhanced with better error handling
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_unread_notifications_count(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $count = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json(['unread_notifications_count' => $count], 200);
        } catch (\Exception $e) {
            Log::error('Error getting unread notifications count', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to get unread notification count',
                'unread_notifications_count' => 0
            ], 500);
        }
    }

    /**
     * Mark all notifications as seen (when user enters notifications screen)
     * Enhanced with transaction safety and better logging
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mark_notifications_as_seen(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::beginTransaction();

            $updatedCount = Notification::where('user_id', $user->id)
                ->where('is_new', true)
                ->update([
                    'is_new' => false,
                    'seen_at' => now()
                ]);

            DB::commit();

            Log::info('Notifications marked as seen', [
                'user_id' => $user->id,
                'updated_count' => $updatedCount
            ]);

            return response()->json([
                'message' => 'Notifications marked as seen',
                'updated_count' => $updatedCount,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error marking notifications as seen', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to mark notifications as seen',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Mark specific notifications as read
     * Enhanced with validation and error handling
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mark_notifications_as_read(Request $request)
    {
        try {
            $validation = $request->validate([
                'notification_ids' => 'array|required',
                'notification_ids.*' => 'integer|exists:notifications,id'
            ]);

            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::beginTransaction();

            $updatedCount = Notification::where('user_id', $user->id)
                ->whereIn('id', $validation['notification_ids'])
                ->update([
                    'is_new' => false,
                    'is_read' => true,
                    'seen_at' => now(),
                    'read_at' => now()
                ]);

            DB::commit();

            Log::info('Specific notifications marked as read', [
                'user_id' => $user->id,
                'notification_ids' => $validation['notification_ids'],
                'updated_count' => $updatedCount
            ]);

            return response()->json([
                'message' => 'Notifications marked as read',
                'updated_count' => $updatedCount,
                'success' => true
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error marking specific notifications as read', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to mark notifications as read',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     * Enhanced with transaction safety and better logging
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mark_all_notifications_as_read(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::beginTransaction();

            $updatedCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_new' => false,
                    'is_read' => true,
                    'seen_at' => now(),
                    'read_at' => now()
                ]);

            DB::commit();

            Log::info('All notifications marked as read', [
                'user_id' => $user->id,
                'updated_count' => $updatedCount
            ]);

            return response()->json([
                'message' => 'All notifications marked as read',
                'updated_count' => $updatedCount,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error marking all notifications as read', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to mark all notifications as read',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Enhanced notification creation method
     * Call this when creating new notifications to ensure proper defaults
     */
    private function createNotification($userId, $text, $dataType = 'order', $data = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'text' => $text,
                'data_type' => $dataType,
                'data' => $data,
                'is_new' => true,      // Always start as new
                'is_read' => false,    // Always start as unread
                'seen_at' => null,     // Not seen yet
                'read_at' => null      // Not read yet
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'user_id' => $userId,
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get balance - Service provider gets full amount minus platform fees only
     */
    public function get_balance(Request $request)
    {
        $user = auth("api")->user();
        $user_id = $user->id;

        // Calculate balance from service earnings table (after platform fees, but NOT tax deduction)
        $total_earnings = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('net_earnings'); // Platform fees deducted, tax NOT deducted

        // Calculate withdrawn amount
        $withdrawn = WithdrawRequest::where([
            ["user_id", "=", $user_id],
            ['status', "<", 2]
        ])->sum('amount');

        $balance = $total_earnings - $withdrawn;

        // Breakdown for transparency
        $total_gross = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('gross_amount');

        $total_platform_fees = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('platform_fee_amount');

        $total_taxes_on_orders = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('tax_amount');

        return response()->json([
            "balance" => $balance,
            "total_earnings" => $total_earnings, // After platform fees only
            "withdrawn" => $withdrawn,
            "earnings_breakdown" => [
                "gross_earnings" => $total_gross, // Your quoted amounts
                "platform_fees_deducted" => $total_platform_fees, // You pay this
                "taxes_paid_by_customers" => $total_taxes_on_orders, // Customers pay this
                "net_earnings" => $total_earnings // Your earnings after platform fees
            ]
        ], 200);
    }


    /**
     * Check withdraw status - Shows earnings after platform fees only
     */
    public function check_withdraw_status(Request $request)
    {
        $user = auth("api")->user();
        $user_id = $user->id;

        // Get withdrawal requests
        $requests = WithdrawRequest::where([
            ["user_id", "=", $user_id],
            ['status', "<", 2]
        ])->select("transit", "institution", "account", "name", "account", "email", "amount", "created_at", "status")->get();

        // Get current available balance (after platform fees, but NOT tax deduction)
        $total_earnings = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('net_earnings'); // Platform fees deducted, tax NOT deducted

        $total_withdrawn = WithdrawRequest::where('user_id', $user_id)
            ->where('status', '<', 2)
            ->sum('amount');

        $available_balance = $total_earnings - $total_withdrawn;

        return response()->json([
            "requests" => $requests,
            "available_balance" => $available_balance,
            "total_earnings" => $total_earnings // After platform fees only
        ], 200);
    }

    /**
     * Withdraw balance - Based on earnings after platform fees only (tax not deducted)
     */
    public function withdraw_balance(Request $request)
    {
        $validation = $request->validate([
            "amount" => "required|numeric",
            "method" => "required|in:interac,bank",
            "name" => "required_if:method,bank",
            "transit" => "required_if:method,bank",
            "institution" => "required_if:method,bank",
            "account" => "required_if:method,bank",
            "email" => "required_if:method,interac",
        ]);

        $user = auth("api")->user();
        $user_id = $user->id;

        // Calculate balance (after platform fees, but NOT tax deduction)
        $total_earnings = ServiceEarning::where('user_id', $user_id)
            ->where('status', 'completed')
            ->sum('net_earnings'); // Platform fees deducted, tax NOT deducted

        // Calculate withdrawn amount
        $withdrawn = WithdrawRequest::where([
            ["user_id", "=", $user_id],
            ['status', "<", 2]
        ])->sum('amount');

        $balance = $total_earnings - $withdrawn;

        if ($validation["amount"] <= $balance) {
            $validation["user_id"] = $user_id;
            $request = WithdrawRequest::create($validation);
            return response()->json(["status" => "success", "data" => $request], 200);
        } else {
            return response()->json([
                "status" => "failed, not enough balance",
                "available_balance" => $balance,
                "requested_amount" => $validation["amount"]
            ], 402);
        }
    }

    /**
     * gallary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove_from_gallary(Request $request)
    {
        $validation = $request->validate([
            "filename" => "required",
        ]);

        if ($validation) {

            $del = ServiceGallary::Where("image", $validation["filename"])->delete();
            return response()->json($del);
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }

    /**
     * create_service - FIXED VERSION
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_service(Request $request)
    {
        $request->merge([
            'use_old_liscence' => $request->input('use_old_liscence', true), // default to true
        ]);

        $validation = $request->validate([
            'years_of_experience' => "numeric|required",
            'about' => 'string|required',
            'pricing_type' => 'required|in:hourly,daily,fixed',
            'cost_per_hour' => 'numeric|required_if:pricing_type,hourly',
            'cost_per_day' => 'numeric|required_if:pricing_type,daily',
            'fixed_price' => 'numeric|required_if:pricing_type,fixed',
            'service' => 'string|required',
            "license" => "file",
            "category_id" => "exists:categories,id|required",
            "subcategory_id" => "exists:subcategories,id|required",
            "use_old_liscence" => "boolean",
            "active" => "boolean"
        ]);

        if (isset($validation["password"])) $validation["password"] = bcrypt($validation["password"]);
        $validation["user_id"] = auth("api")->user()->id;
        $validation["country"] = auth("api")->user()->country;
        $validation["city"] = auth("api")->user()->city;

        // FIXED: Handle license file properly
        if ($validation["use_old_liscence"] == true) {
            if ($request->file('license')) {
                // FIX: Check if service exists before accessing its properties
                $existingService = Service::where([
                    ["user_id", "=", auth("api")->user()->id],
                    ["category_id", "=", $validation["category_id"]]
                ])->first();

                if ($existingService && $existingService->license) {
                    $validation["license"] = $existingService->license;
                } else {
                    // If no existing service with license found, upload the new one
                    $file = $request->file('license');
                    $temp = $file->store('public/images');
                    $_array = explode("/", $temp);
                    $file_name = $_array[sizeof($_array) - 1];
                    $validation["license"] = $file_name;
                }
            }
        } else {
            // Handle new license upload
            if ($request->file('license')) {
                $file = $request->file('license');
                $temp = $file->store('public/images');
                $_array = explode("/", $temp);
                $file_name = $_array[sizeof($_array) - 1];
                $validation["license"] = $file_name;
            }
        }

        try {
            // Create the service
            $service = Service::create($validation);

            // Get the created service with user data
            $serviceWithUser = Service::join('users', "users.id", "=", "services.user_id")
                ->where([["services.id", "=", $service["id"]]])
                ->select(
                    "services.id",
                    "users.city",
                    "users.country",
                    "users.email",
                    "users.phone",
                    "users.full_name",
                    "users.picture",
                    "users.location",
                    "services.cost_per_hour",
                    "services.cost_per_day",
                    "services.fixed_price",
                    "services.pricing_type",
                    "services.service",
                    "services.years_of_experience",
                    "services.about",
                    "users.security_check",
                    "services.verified_liscence",
                )->first();

            return response()->json($serviceWithUser);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Service creation failed', [
                'user_id' => auth("api")->user()->id,
                'error' => $e->getMessage(),
                'validation_data' => $validation
            ]);

            return response()->json([
                'error' => 'Failed to create service',
                'message' => 'Please try again later'
            ], 500);
        }
    }
    /**
     * edit_state
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_state(Request $request)
    {
        $validation = $request->validate([
            "active" => "boolean"
        ]);

        if ($validation) {
            $service = Service::Where([["user_id", "=", auth("api")->user()->id]]);
            $service->update($validation);
            return response()->json(["status" => "success"]);
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }

    /**
     * edit_service - IMPROVED VERSION
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit_service(Request $request)
    {
        try {
            $validation = $request->validate([
                'years_of_experience' => "numeric",
                'about' => 'string',
                'pricing_type' => 'in:hourly,daily,fixed',
                'cost_per_hour' => 'numeric|required_if:pricing_type,hourly',
                'cost_per_day' => 'numeric|required_if:pricing_type,daily',
                'fixed_price' => 'numeric|required_if:pricing_type,fixed',
                'service' => 'string',
                "license" => "file",
                "category_id" => "exists:categories,id",
                "subcategory_id" => "exists:subcategories,id",
                "service_id" => "exists:services,id|required", // Make required
                "active" => "boolean"
            ]);

            // Handle file upload with better error checking
            if ($request->file('license')) {
                try {
                    $file = $request->file('license');

                    // Validate file size and type
                    if ($file->getSize() > 5 * 1024 * 1024) { // 5MB limit
                        return response()->json([
                            'error' => 'File too large',
                            'message' => 'License file must be smaller than 5MB'
                        ], 400);
                    }

                    $temp = $file->store('public/images');
                    $_array = explode("/", $temp);
                    $file_name = $_array[sizeof($_array) - 1];
                    $validation["license"] = $file_name;
                } catch (\Exception $e) {
                    \Log::error('File upload failed in edit_service', [
                        'error' => $e->getMessage(),
                        'user_id' => auth("api")->user()->id
                    ]);

                    return response()->json([
                        'error' => 'File upload failed',
                        'message' => 'Please try again with a different file'
                    ], 500);
                }
            }

            $id = $validation["service_id"];
            unset($validation["service_id"]);

            // Verify service belongs to authenticated user
            $service = Service::where("id", $id)
                ->where("user_id", auth("api")->user()->id)
                ->first();

            if (!$service) {
                return response()->json([
                    'error' => 'Service not found or unauthorized',
                    'message' => 'You can only edit your own services'
                ], 404);
            }

            // Update the service
            $service->update($validation);

            // Get the updated service with user data
            $updatedService = Service::join('users', "users.id", "=", "services.user_id")
                ->where([["services.id", "=", $id]])
                ->select(
                    "services.id",
                    "users.city",
                    "users.country",
                    "users.email",
                    "users.phone",
                    "users.full_name",
                    "users.picture",
                    "users.location",
                    "services.cost_per_hour",
                    "services.cost_per_day",
                    "services.fixed_price",
                    "services.pricing_type",
                    "services.service",
                    "services.years_of_experience",
                    "services.about",
                    "services.active",
                    "users.security_check",
                    "services.verified_liscence",
                )->first();

            // Log successful update
            \Log::info('Service updated successfully', [
                'service_id' => $id,
                'user_id' => auth("api")->user()->id
            ]);

            return response()->json([
                'message' => 'Service updated successfully',
                'data' => $updatedService
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating service', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null,
                'service_id' => $request->input('service_id') ?? null
            ]);

            return response()->json([
                'error' => 'Failed to update service',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * get_services - Fixed syntax and sorted by orders/rating, limited to 30
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_services(Request $request)
    {
        // filtering method
        $query = "";
        $category = "";
        if ($request->has('query')) $query = $request->input('query');
        if ($request->has('category')) $category = $request->input('category');

        if ($request->has('filter')) {
            if ($category == "") {
                $services = Service::With(['Subcategory.Category' => function ($query) {
                    $query->select('id', 'name', "image");
                }, 'Subcategory' => function ($query) {
                    $query->select('id', 'name', "category_id");
                }])->join('users', "users.id", "=", "services.user_id")
                    ->select(
                        "services.id",
                        "users.id AS contractor_id",
                        "services.subcategory_id",
                        "users.city",
                        "users.country",
                        "users.email",
                        "users.phone",
                        "users.full_name",
                        "users.picture",
                        "users.location",
                        "services.cost_per_hour",
                        "services.cost_per_day",
                        "services.fixed_price",
                        "services.pricing_type",
                        "services.service",
                        "services.years_of_experience",
                        "services.about",
                        "users.security_check",
                        "services.verified_liscence",
                    )
                    ->Where([["services.active", "=", true], ["users.active", "=", true], ["services.about", "LIKE", "%$query%"]])
                    ->OrWhere([["services.active", "=", true], ["users.active", "=", true], ["users.full_name", "LIKE", "%$query%"]])
                    ->get();
            } else {
                $services = Service::With(['Subcategory.Category' => function ($query) {
                    $query->select('id', 'name', "image");
                }, 'Subcategory' => function ($query) {
                    $query->select('id', 'name', "category_id");
                }])->join('users', "users.id", "=", "services.user_id")
                    ->select(
                        "services.id",
                        "users.id AS contractor_id",
                        "services.subcategory_id",
                        "users.city",
                        "users.country",
                        "users.email",
                        "users.phone",
                        "users.full_name",
                        "users.picture",
                        "users.location",
                        "services.cost_per_hour",
                        "services.cost_per_day",
                        "services.fixed_price",
                        "services.pricing_type",
                        "services.service",
                        "services.years_of_experience",
                        "services.about",
                        "users.security_check",
                        "services.verified_liscence",
                    )
                    ->Where([["services.active", "=", true], ["users.active", "=", true], ["services.category_id", "=", $category], ["services.about", "LIKE", "%$query%"]])
                    ->OrWhere([["services.active", "=", true], ["users.active", "=", true], ["services.category_id", "=", $category], ["users.full_name", "LIKE", "%$query%"]])
                    ->get();
            }
        } else { // <-- This was missing the proper closing brace before
            if ($category == "") {
                $services = Service::With(['Subcategory.Category' => function ($query) {
                    $query->select('id', 'name', "image");
                }, 'Subcategory' => function ($query) {
                    $query->select('id', 'name', "category_id");
                }])->join('users', "users.id", "=", "services.user_id")->select(
                    "services.subcategory_id",
                    "services.id",
                    "users.city",
                    "users.country",
                    "users.id AS contractor_id",
                    "users.email",
                    "users.phone",
                    "users.full_name",
                    "users.picture",
                    "users.location",
                    "services.cost_per_hour",
                    "services.cost_per_day",
                    "services.fixed_price",
                    "services.pricing_type",
                    "services.service",
                    "services.years_of_experience",
                    "services.about",
                    "users.security_check",
                    "services.verified_liscence",
                )
                    ->Where([["services.active", "=", true], ["users.active", "=", true], ["services.about", "LIKE", "%$query%"]])
                    ->OrWhere([["services.active", "=", true], ["users.active", "=", true], ["users.full_name", "LIKE", "%$query%"]])
                    ->get();
            } else {
                $services = Service::With(['Subcategory.Category' => function ($query) {
                    $query->select('id', 'name', "image");
                }, 'Subcategory' => function ($query) {
                    $query->select('id', 'name', "category_id");
                }])->join('users', "users.id", "=", "services.user_id")->select(
                    "services.subcategory_id",
                    "services.id",
                    "users.city",
                    "users.country",
                    "users.id AS contractor_id",
                    "users.email",
                    "users.phone",
                    "users.full_name",
                    "users.picture",
                    "users.location",
                    "services.cost_per_hour",
                    "services.cost_per_day",
                    "services.fixed_price",
                    "services.pricing_type",
                    "services.service",
                    "services.years_of_experience",
                    "services.about",
                    "users.security_check",
                    "services.verified_liscence",
                )
                    ->Where([["services.active", "=", true], ["users.active", "=", true], ["services.category_id", "=", $category], ["services.about", "LIKE", "%$query%"]])
                    ->OrWhere([["services.active", "=", true], ["users.active", "=", true], ["services.category_id", "=", $category], ["users.full_name", "LIKE", "%$query%"]])
                    ->get();
            }
        }

        // Process each service to calculate ratings, orders, and display price
        foreach ($services as $key => $service) {
            $id = $service["id"];
            $category_id = Service::Where([["id", "=", $id]])->first()["category_id"];

            $orders = Order::Select("id", "total_hours", "start_at", "price")->Where([["service_id", "=", $id]])->count();
            $ratings = Rating::Select("message", "rate", "user_id", "created_at")->With(['User' => function ($query) {
                $query->select('id', 'full_name', "picture");
            }])->whereBelongsTo($service)->get();

            $total_ratings = 0;
            $times_rated = 0;
            foreach ($ratings as $key2 => $rating) {
                $times_rated++;
                $total_ratings += $rating["rate"];
            }
            $average_rating = $times_rated != 0 ? $total_ratings / $times_rated : 0;
            $ratings_object = ["rating" => $average_rating, "times_rated" => $times_rated];

            $services[$key]["rating"] = $ratings_object;
            $services[$key]["ratings"] = $ratings;
            $services[$key]["orders"] = $orders;
            $services[$key]["average_rating"] = $average_rating; // For sorting

            $gall = ServiceGallary::Select("image")->Where([["service_id", $service["id"]]])->pluck("image");
            $services[$key]["gallary"] = $gall;

            // Add pricing display logic
            $services[$key]["display_price"] = $this->getDisplayPrice($service);

            if (Category::Where([["id", "=", $category_id]])->count() == 0) {
                unset($services[$key]);
                continue;
            }
        }

        // Convert to array for sorting
        $all_services = [];
        foreach ($services as $service) {
            $all_services[] = $service;
        }

        // Sort by: 1) Number of orders (descending), 2) Average rating (descending)
        usort($all_services, function ($a, $b) {
            // Primary sort: by orders (most orders first)
            $orderComparison = $b["orders"] - $a["orders"];
            if ($orderComparison !== 0) {
                return $orderComparison;
            }

            // Secondary sort: by average rating (highest rating first)
            $ratingA = $a["average_rating"];
            $ratingB = $b["average_rating"];

            if ($ratingA == $ratingB) {
                return 0;
            }
            return ($ratingA < $ratingB) ? 1 : -1;
        });

        // Limit to maximum 30 services (only for get_services - popular services)
        $limited_services = array_slice($all_services, 0, 30);

        // Remove the temporary sorting field
        foreach ($limited_services as $key => $service) {
            unset($limited_services[$key]["average_rating"]);
        }

        return response()->json($limited_services);
    }

    /**
     * Get display price based on pricing type
     */
    private function getDisplayPrice($service)
    {
        switch ($service["pricing_type"]) {
            case 'hourly':
                return [
                    'price' => $service["cost_per_hour"],
                    'type' => 'hourly',
                    'display_text' => '$' . number_format($service["cost_per_hour"], 2) . '/hour'
                ];
            case 'daily':
                return [
                    'price' => $service["cost_per_day"],
                    'type' => 'daily',
                    'display_text' => '$' . number_format($service["cost_per_day"], 2) . '/day'
                ];
            case 'fixed':
                return [
                    'price' => $service["fixed_price"],
                    'type' => 'fixed',
                    'display_text' => '$' . number_format($service["fixed_price"], 2) . ' (Fixed)'
                ];
            default:
                // Fallback for backward compatibility
                if ($service["cost_per_hour"]) {
                    return [
                        'price' => $service["cost_per_hour"],
                        'type' => 'hourly',
                        'display_text' => '$' . number_format($service["cost_per_hour"], 2) . '/hour'
                    ];
                }
                return [
                    'price' => 0,
                    'type' => 'unknown',
                    'display_text' => 'Price on request'
                ];
        }
    }

    /**
     * gallary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_service(Request $request, $id)
    {
        // $service = User::Where("id", "=", $id)->first();
        // if($service){
        //     $orders = Order::Where([["service_id", "=", $id]])->count();
        //     $ratings = Rating::With(['User' => function ($query) {
        //         $query->select('id', 'full_name', "picture");
        //     }])->whereBelongsTo($service)->get();
        //     $service["ratings"] = $ratings;
        //     $service["orders"] = $orders;
        //     $gall = ServiceGallary::Where([["user_id", $service["id"]]])->get();
        //     // return response()->json($gall);
        //     $service["gallary"] = $gall;
        // }
        // return response()->json($service);
    }

    public function get_user(Request $request, $id)
    {
        $service = User::select("full_name", "picture")->Where("id", "=", $id)->first();
        return response()->json($service);
    }

    /**
     * Get category services - NO LIMIT, all services in category
     */
    public function get_category_services(Request $request, $category_id)
    {
        $services = Service::With(['Subcategory.Category' => function ($query) {
            $query->select('id', 'name', "image");
        }, 'Subcategory' => function ($query) {
            $query->select('id', 'name', "category_id");
        }])->join('users', "users.id", "=", "services.user_id")->select(
            "services.id",
            "services.subcategory_id",
            "users.city",
            "users.country",
            "users.id AS contractor_id",
            "users.email",
            "users.phone",
            "users.full_name",
            "users.picture",
            "users.location",
            "services.cost_per_hour",
            "services.cost_per_day",
            "services.fixed_price",
            "services.pricing_type",
            "services.service",
            "services.years_of_experience",
            "services.about",
            "users.security_check",
            "services.verified_liscence",
        )->Where([["services.active", "=", true], ["users.active", "=", true], ["services.category_id", "=", $category_id]])->get();

        // Process each service to calculate ratings, orders, and display price
        foreach ($services as $key => $service) {
            $id = $service["id"];

            $orders = Order::Select("total_hours", "start_at", "price")->Where([["service_id", "=", $id]])->count();
            $ratings = Rating::Select("message", "rate", "user_id", "created_at")->With(['User' => function ($query) {
                $query->select('id', 'full_name', "picture");
            }])->whereBelongsTo($service)->get();

            $total_ratings = 0;
            $times_rated = 0;
            foreach ($ratings as $key2 => $rating) {
                $times_rated++;
                $total_ratings += $rating["rate"];
            }
            $average_rating = $times_rated != 0 ? $total_ratings / $times_rated : 0;
            $ratings_object = ["rating" => $average_rating, "times_rated" => $times_rated];

            $services[$key]["rating"] = $ratings_object;
            $services[$key]["ratings"] = $ratings;
            $services[$key]["orders"] = $orders;

            $gall = ServiceGallary::Select("image")->Where([["service_id", $service["id"]]])->pluck("image");
            $services[$key]["gallary"] = $gall;

            // Add pricing display logic
            $services[$key]["display_price"] = $this->getDisplayPrice($service);
        }

        // NO SORTING OR LIMITING - return all services in original order
        // The mobile app will handle filtering and sorting
        return response()->json($services);
    }


    /**
     * gallary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_my_services(Request $request)
    {
        $services = Service::With(['Subcategory.Category' => function ($query) {
            $query->select('id', 'name', "image");
        }, 'Subcategory' => function ($query) {
            $query->select('id', 'name', "category_id");
        }])->join('users', "users.id", "=", "services.user_id")->select(
            "services.id",
            "services.subcategory_id",
            "services.cost_per_hour",
            "services.cost_per_day",
            "services.fixed_price",
            "services.pricing_type",
            "services.service",
            "services.years_of_experience",
            "services.about",
            "services.active",
            "services.verified_liscence",
        )->Where([["users.id", "=", auth("api")->user()->id]])->get();
        foreach ($services as $key => $service) {
            $id = $service["id"];

            $gall = ServiceGallary::Select("image")->Where([["service_id", $service["id"]]])->pluck("image");
            // return response()->json($gall);
            $services[$key]["gallary"] = $gall;
        }

        return response()->json($services);
    }


    /**
     * rate_service - FIXED VERSION
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rate_service(Request $request)
    {
        $validation = $request->validate([
            'rate' => 'integer|required',
            'message' => 'string|required',
            'service_id' => 'integer|required|exists:services,id', // FIXED: Changed from users,id to services,id
        ]);

        $user_id = auth("api")->user()->id;
        $validation["user_id"] = $user_id;

        if ($validation) {
            $rate = Rating::where([
                ["user_id", "=", $validation["user_id"]],
                ["service_id", "=", $validation["service_id"]]
            ]);

            if ($rate->count() > 0) {
                $rate->update($validation);
                $rating = $rate->first();
            } else {
                $rating = Rating::create($validation);
            }

            return response()->json(['message' => 'Success', 'data' => $rating], 200);
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }


    /**
     * order_service - FIXED: Only use existing order table columns
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_service(Request $request)
    {
        $validation = $request->validate([
            'duration_value' => 'numeric|required',
            'start_at' => 'integer|required',
            'note' => 'string',
            'coupon' => 'string|sometimes',
            'location' => 'string|required',
            'region' => 'string|required',
            'service_id' => 'integer|required|exists:services,id',
        ]);

        // Handle coupon logic (unchanged)
        if (isset($validation["coupon"])) {
            $coup = Coupon::Where("coupon", "=", $validation["coupon"]);
            if ($coup->count() > 0) {
                $coupon = $coup->first();
                if ($coupon["times_used"] < $coupon["max_usage"]) {
                    $validation["coupon_id"] = $coupon["id"];
                    $validation["coupon_percentage"] = $coupon["percentage"];
                    $coupon->update(["times_used" => $coupon["times_used"] + 1]);
                }
            }
        }

        // Get service details
        $service = Service::Where("id", "=", $validation["service_id"])->first();

        // Calculate base price based on pricing type
        $basePrice = 0;
        $totalHours = 1;

        switch ($service["pricing_type"]) {
            case 'hourly':
                $totalHours = (int) $validation["duration_value"];
                $basePrice = $service["cost_per_hour"] * $validation["duration_value"];
                break;
            case 'daily':
                $totalHours = (int) ($validation["duration_value"] * 8);
                $basePrice = $service["cost_per_day"] * $validation["duration_value"];
                break;
            case 'fixed':
                $totalHours = 1;
                $basePrice = $service["fixed_price"];
                break;
            default:
                return response()->json(['error' => 'Invalid pricing type'], 400);
        }

        $validation["price"] = $basePrice;
        $validation["total_hours"] = $totalHours;
        $validation["pricing_type"] = $service["pricing_type"];
        $validation["duration_value"] = $validation["duration_value"];

        // Apply coupon discount (unchanged logic)
        if (isset($validation["coupon_percentage"])) {
            $discounted_amount = $validation["price"] * ($validation["coupon_percentage"] / 100);
            $validation["price"] -= $discounted_amount;
            $validation["discounted_amount"] = $discounted_amount;
        }

        $validation["status"] = 1; //pending
        $validation["note"] = $validation["note"] ?? "";
        $user_id = auth("api")->user()->id;
        $validation["user_id"] = $user_id;

        if ($validation) {
            // FIXED: Use new combined platform fee system
            // Get customer platform fees (fixed + percentage)
            $customerFeeFixed = AppSetting::where('key', 'customer_platform_fee')->first();
            $customerFeePercentage = AppSetting::where('key', 'customer_platform_fee_percentage')->first();

            $customer_fee_fixed = $customerFeeFixed ? floatval($customerFeeFixed->value) : 0;
            $customer_fee_percentage = $customerFeePercentage ? floatval($customerFeePercentage->value) : 0;

            // Calculate COMBINED customer platform fee
            $customer_fee_from_percentage = ($validation["price"] * $customer_fee_percentage) / 100;
            $customer_platform_fee_total = $customer_fee_fixed + $customer_fee_from_percentage;

            // FIXED: Only use platform_fee_amount (the existing column)
            $validation["platform_fee_amount"] = $customer_platform_fee_total;
            $validation["price"] += $customer_platform_fee_total;

            // Get regional tax and apply
            $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($validation["region"])]);
            if ($tax->count() == 0) $tax = RegionTax::whereRaw('LOWER(region) = ?', ["international"]);

            if ($tax->count() > 0) {
                $region_tax = $tax->first()["percentage"];
                $taxed_amount = ($validation["price"] * $region_tax / 100);
                $validation["taxed_amount"] = $taxed_amount;
                $validation["price"] = $taxed_amount + $validation["price"];

                // Log what we're trying to insert for debugging
                Log::info('Creating order with correct data:', $validation);

                $order = Order::create($validation);

                // Create pending earnings record for this order
                $this->createPendingEarnings($order["id"]);

                Notification::create([
                    "user_id" => $service["user_id"],
                    "text" => "You have a new order on $service[service]",
                    "data_type" => "order",
                    "data" => $order["id"]
                ]);

                $this->notify_user($service["user_id"], "New Order", "You have a new order on $service[service]");
                return response()->json(['message' => 'Success', 'data' => $order], 200);
            } else {
                return response()->json(['message' => 'Failed, region not found'], 404);
            }
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }


    /**
     * get_orders - Updated to include pricing information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders(Request $request)
    {
        $user_id = auth("api")->user()->id;

        $orders = Order::Select(
            "id",
            "total_hours",
            "start_at",
            "price",
            "location",
            "service_id",
            "status",
            "note",
            "pricing_type",      // Add pricing fields from order table
            "duration_value"     // Add duration value from order table
        )->With(['Service' => function ($query) {
            $query->join('users', "users.id", "=", "services.user_id")
                ->select(
                    'services.id',
                    'users.full_name',
                    "users.picture",
                    "services.service",
                    "services.user_id",
                    "services.subcategory_id",
                    "services.cost_per_hour",
                    "services.cost_per_day",      // Add new pricing fields
                    "services.fixed_price",       // Add new pricing fields  
                    "services.pricing_type"       // Add new pricing fields
                );
        }, 'Service.Subcategory' => function ($query) {
            $query->select('id', 'name');
        }])->Where([["user_id", "=", $user_id], ["status", ">", "0"]])->get();

        $total_orders = [];
        foreach ($orders as $order) {
            if (is_null($order->service) == false && is_null($order->service->subcategory) == false) {
                $contractor = User::Where([["id", "=", $order->service->user_id]])
                    ->select(["full_name", "picture"])
                    ->first();
                $order["service"]["full_name"] = $contractor->full_name;
                $order["service"]["picture"] = $contractor->picture;

                // Add pricing display information
                $order["pricing_display"] = $this->formatOrderPricingDisplay($order);
                $order["duration_display"] = $this->formatOrderDurationDisplay($order);

                $total_orders[] = $order;
            }
        }

        return response()->json(['message' => 'Success', 'data' => $total_orders], 200);
    }

    /**
     * Format pricing display for orders
     */
    private function formatOrderPricingDisplay($order)
    {
        $service = $order->service;
        $pricing_type = $order->pricing_type ?? $service->pricing_type ?? 'hourly';

        switch ($pricing_type) {
            case 'hourly':
                return [
                    'type' => 'hourly',
                    'rate' => $service->cost_per_hour,
                    'display' => '$' . $service->cost_per_hour . '/hour'
                ];
            case 'daily':
                return [
                    'type' => 'daily',
                    'rate' => $service->cost_per_day,
                    'display' => '$' . $service->cost_per_day . '/day'
                ];
            case 'fixed':
                return [
                    'type' => 'fixed',
                    'rate' => $service->fixed_price,
                    'display' => '$' . $service->fixed_price . ' (Fixed)'
                ];
            default:
                return [
                    'type' => 'hourly',
                    'rate' => $service->cost_per_hour,
                    'display' => '$' . $service->cost_per_hour . '/hour'
                ];
        }
    }

    /**
     * Format duration display for orders
     */
    private function formatOrderDurationDisplay($order)
    {
        $pricing_type = $order->pricing_type ?? $order->service->pricing_type ?? 'hourly';
        $duration_value = $order->duration_value ?? $order->total_hours;

        switch ($pricing_type) {
            case 'hourly':
                $hours = $duration_value == floor($duration_value) ? (int)$duration_value : $duration_value;
                return $hours . ($hours == 1 ? ' hour' : ' hours');
            case 'daily':
                $days = $duration_value == floor($duration_value) ? (int)$duration_value : $duration_value;
                return $days . ($days == 1 ? ' day' : ' days');
            case 'fixed':
                return 'Fixed price service';
            default:
                return $order->total_hours . ($order->total_hours == 1 ? ' hour' : ' hours');
        }
    }

    /**
     * get_service_orders - FIXED VERSION with consistent response format
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_service_orders(Request $request)
    {
        try {
            $user_id = auth("api")->user()->id;

            $orders = Order::join('services', "services.id", "=", "order.service_id")
                ->join('subcategories', "subcategories.id", "=", "services.subcategory_id")
                ->select(
                    "order.created_at",
                    "order.id",
                    "order.note",
                    "services.service",
                    "services.id AS service_id",
                    "services.cost_per_hour",
                    "services.cost_per_day",
                    "services.fixed_price",
                    "services.pricing_type",
                    "order.total_hours",
                    "order.duration_value",
                    "order.start_at",
                    "order.location",
                    "order.user_id",
                    "order.status"
                )
                ->with(['User' => function ($query) {
                    $query->select('id', 'full_name', "picture");
                }])
                ->where([["services.user_id", "=", $user_id], ["order.status", ">", "0"]])
                ->get();

            // Always initialize as empty object, not array
            $orders_by_date = new \stdClass();

            if ($orders->count() > 0) {
                $orders_by_date = [];

                foreach ($orders as $order) {
                    // Calculate the service provider's price
                    $service_price = 0;
                    $duration_value = $order["duration_value"] ?? $order["total_hours"];

                    switch ($order["pricing_type"]) {
                        case 'hourly':
                            $service_price = $order["cost_per_hour"] * $duration_value;
                            break;
                        case 'daily':
                            $service_price = $order["cost_per_day"] * $duration_value;
                            break;
                        case 'fixed':
                            $service_price = $order["fixed_price"];
                            break;
                        default:
                            $service_price = $order["cost_per_hour"] * $order["total_hours"];
                    }

                    $order["total_price"] = $service_price;

                    // Group by date
                    $date_key = $order["created_at"]->format("Y-m-d");
                    if (isset($orders_by_date[$date_key])) {
                        $orders_by_date[$date_key][] = $order;
                    } else {
                        $orders_by_date[$date_key] = [$order];
                    }
                }
            }

            return response()->json([
                'message' => 'Success',
                'data' => $orders_by_date
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching service orders', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to fetch orders',
                'message' => 'Please try again later'
            ], 500);
        }
    }


    /**
     * update_order - Updated to handle new pricing system
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_order(Request $request)
    {
        $validation = $request->validate([
            'duration_value' => 'numeric', // Changed from total_hours
            'start_at' => 'integer',
            'location' => 'string',
            'order_id' => 'integer|required|exists:order,id',
            'note' => 'string',
        ]);

        if ($validation) {
            $order = Order::Where([["id", "=", $validation["order_id"]]])->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Get the service to determine pricing type
            $service = Service::Where("id", "=", $order["service_id"])->first();

            if (!$service) {
                return response()->json(['error' => 'Service not found'], 404);
            }

            // Calculate new price if duration_value is provided
            if (isset($validation["duration_value"])) {
                $basePrice = 0;
                $totalHours = 1; // Default for fixed pricing

                switch ($service["pricing_type"]) {
                    case 'hourly':
                        $totalHours = (int) $validation["duration_value"];
                        $basePrice = $service["cost_per_hour"] * $validation["duration_value"];
                        break;

                    case 'daily':
                        $totalHours = (int) ($validation["duration_value"] * 8); // Assuming 8 hours per day
                        $basePrice = $service["cost_per_day"] * $validation["duration_value"];
                        break;

                    case 'fixed':
                        $totalHours = 1; // Fixed pricing is always 1 "unit"
                        $basePrice = $service["fixed_price"];
                        break;

                    default:
                        return response()->json(['error' => 'Invalid pricing type'], 400);
                }

                $validation["price"] = $basePrice;
                $validation["total_hours"] = $totalHours;

                // Apply existing coupon discount if any
                if ($order["coupon_percentage"]) {
                    $discounted_amount = $validation["price"] * ($order["coupon_percentage"] / 100);
                    $validation["price"] -= $discounted_amount;
                    $validation["discounted_amount"] = $discounted_amount;
                }

                // Recalculate platform fees and taxes
                $platform_percentage = 0;
                $_platform_percentage = AppSetting::Where([["key", "=", "platform_fees_percentage"]]);
                if ($_platform_percentage->count() > 0) $platform_percentage = $_platform_percentage->first()["value"];
                $platform_fee = 0;
                $_platform_fee = AppSetting::Where([["key", "=", "platform_fees"]]);
                if ($_platform_fee->count() > 0) $platform_fee = $_platform_fee->first()["value"];

                // Add platform fees before the taxes
                $platform_fee_amount = ($validation["price"] * $platform_percentage / 100);
                $platform_fee_amount += $platform_fee;
                $validation["platform_fee_amount"] = $platform_fee_amount;
                $validation["price"] += $platform_fee_amount;

                // Add taxes (get region from existing order)
                $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($order["region"])]);
                if ($tax->count() == 0) $tax = RegionTax::whereRaw('LOWER(region) = ?', ["international"]);
                if ($tax->count() > 0) {
                    $region_tax = $tax->first()["percentage"];
                    $taxed_amount = ($validation["price"] * $region_tax / 100);
                    $validation["taxed_amount"] = $taxed_amount;
                    $validation["price"] = $taxed_amount + $validation["price"];
                }
            }

            // Remove order_id from validation before updating
            $order_id = $validation["order_id"];
            unset($validation["order_id"]);

            // Update the order
            $order->update($validation);

            // Update pending earnings if duration or pricing changed
            if (isset($validation["duration_value"]) || isset($validation["price"])) {
                $this->updatePendingEarnings($order["id"], $validation);
            }

            // Create notification
            Notification::create([
                "user_id" => $order["user_id"],
                "text" => "Your order has been updated",
                "data_type" => "order",
                "data" => $order["id"]
            ]);

            $this->notify_user($order["user_id"], "Order Updated", "Your order has been updated");

            return response()->json(['message' => 'Success', 'data' => $order], 200);
        } else {
            $errors = $validator->errors();
            return response()->json(['error' => 'Bad Request', 'details' => $errors], 400);
        }
    }

    public function complete_order(Request $request)
    {
        $validation = $request->validate([
            'order_id' => 'integer|required|exists:order,id',
        ]);

        $order = Order::with(['Service'])->where([["id", "=", $validation["order_id"]]])->first();

        // Calculate and store earnings when order is completed
        $this->calculateAndStoreEarnings($validation["order_id"]);

        // Create notification for service provider
        Notification::create([
            "user_id" => $order->Service->user_id,
            "text" => "Your order had been successfully completed",
            "data_type" => "order",
            "data" => $validation["order_id"]
        ]);

        $this->notify_user($order->Service->user_id, "Good one", "Your order had been completed");

        return $this->change_order_status_by_user($request, 2);
    }

    public function cancel_order(Request $request)
    {
        $validation = $request->validate([
            'reason' => 'string',
            'order_id' => 'integer|required|exists:order,id'
        ]);

        $order = Order::where([["id", "=", $validation["order_id"]]])->first();

        // Handle earnings for cancelled order
        $this->handleCancelledOrderEarnings($validation["order_id"]);

        // Create notification for customer
        Notification::create([
            "user_id" => $order["user_id"],
            "text" => "Your order had been canceled",
            "data_type" => "order",
            "data" => $validation["order_id"]
        ]);

        $this->notify_user($order["user_id"], "Order Cancelled", "Your order had been canceled");

        return $this->change_order_status_by_worker($request, 3, isset($validation["reason"]) ? $validation["reason"] : "");
    }

    /**
     * order_service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function change_order_status_by_user(Request $request, $status)
    {
        $validation = $request->validate([
            'order_id' => 'integer|required|exists:order,id',
        ]);
        $user_id = auth("api")->user()->id;

        $collection = Order::Where([["id", "=", $validation["order_id"]]]);
        if ($collection->count() > 0) {
            $order = $collection->first();
            $order->update(["status" => $status]);
            return response()->json(['message' => 'Success', 'data' => $order], 200);
        } else {
            // $errors = $validator->errors();
            return response()->json(['error' => 'Not Found'], 404);
        }
    }
    /**
     * order_service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function change_order_status_by_worker(Request $request, $status, $reason = "")
    {
        $validation = $request->validate([
            'order_id' => 'integer|required|exists:order,id',
        ]);
        $user_id = auth("api")->user()->id;

        $collection = Order::Where([["id", "=", $validation["order_id"]]]);
        if ($collection->count() > 0) {
            $order = $collection->first();
            if ($reason != "") {
                $order->update(["status" => $status, "note" => $reason]);
            } else {
                $order->update(["status" => $status]);
            }

            return response()->json(['message' => 'Success', 'data' => $order], 200);
        } else {
            // $errors = $validator->errors();
            return response()->json(['error' => 'Not Found'], 404);
        }
    }



    /**
     * order_service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check_coupon(Request $request)
    {
        $validation = $request->validate([
            'coupon' => 'required',
        ]);
        $coup = Coupon::Where("coupon", "=", $validation["coupon"]);
        if ($coup->count() > 0) {
            $coupon = $coup->first();
            if ($coupon["times_used"] < $coupon["max_usage"]) {
                return response()->json(['message' => 'Success', 'data' => $coup->select("percentage")->first()], 200);
            } else {
                return response()->json(['message' => 'Not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Not found'], 404);
        }
    }

    public function get_tax_regions(Request $request)
    {

        $taxes = RegionTax::select("region")->pluck("region");
        return response()->json($taxes, 200);
    }

    public function check_taxes(Request $request)
    {
        $validation = $request->validate([
            'region' => 'required',
        ]);

        // FIXED: Get customer platform fee settings (combined fixed + percentage)
        $customerFeeFixed = AppSetting::where('key', 'customer_platform_fee')->first();
        $customerFeePercentage = AppSetting::where('key', 'customer_platform_fee_percentage')->first();

        // Set defaults if not configured
        $platform_fees_fixed = $customerFeeFixed ? floatval($customerFeeFixed->value) : 0;
        $platform_fees_percentage = $customerFeePercentage ? floatval($customerFeePercentage->value) : 0;

        // Get regional tax
        $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($validation["region"])]);
        if ($tax->count() == 0) $tax = RegionTax::whereRaw('LOWER(region) = ?', ["international"]);

        if ($tax->count() > 0) {
            $region_tax = $tax->first()["percentage"];

            return response()->json([
                'message' => 'Success',
                'data' => [
                    "region_taxes" => $region_tax,
                    "platform_fees_percentage" => (string)$platform_fees_percentage, // Customer percentage fee
                    "platform_fees" => (string)$platform_fees_fixed, // Customer fixed fee
                    "platform_fees_combined" => true, // NEW: Indicate we use combined fees
                ]
            ], 200);
        } else {
            return response()->json(['message' => 'Not found'], 404);
        }
    }

    /**
     * Calculate and store platform earnings when order is completed 
     * FIXED: Works with existing order table structure
     */
    private function calculateAndStoreEarnings($order_id)
    {
        try {
            // Get the order with service details
            $order = Order::with('service')->where('id', $order_id)->first();

            if (!$order || !$order->service) {
                Log::error('Order or service not found for earnings calculation', ['order_id' => $order_id]);
                return false;
            }

            // Check if pending earnings record exists
            $pending_earning = ServiceEarning::where('order_id', $order_id)
                ->where('status', 'pending')
                ->first();

            // Get service provider
            $service_provider_id = $order->service->user_id;

            // Calculate base service amount (what service provider quoted)
            $base_service_amount = 0;
            $pricing_type = $order->pricing_type ?? $order->service->pricing_type ?? 'hourly';
            $duration_value = $order->duration_value ?? $order->total_hours;

            switch ($pricing_type) {
                case 'hourly':
                    $base_service_amount = $order->service->cost_per_hour * $duration_value;
                    break;
                case 'daily':
                    $base_service_amount = $order->service->cost_per_day * $duration_value;
                    break;
                case 'fixed':
                    $base_service_amount = $order->service->fixed_price;
                    $duration_value = 1;
                    break;
            }

            // FIXED: Get current platform fees settings (combined system)
            $customerFeeFixed = AppSetting::where('key', 'customer_platform_fee')->first();
            $customerFeePercentage = AppSetting::where('key', 'customer_platform_fee_percentage')->first();
            $providerFeeFixed = AppSetting::where('key', 'provider_platform_fee_fixed')->first();
            $providerFeePercentage = AppSetting::where('key', 'provider_platform_fee_percentage')->first();

            $customer_fee_fixed = $customerFeeFixed ? floatval($customerFeeFixed->value) : 0;
            $customer_fee_percentage = $customerFeePercentage ? floatval($customerFeePercentage->value) : 0;
            $provider_fee_fixed = $providerFeeFixed ? floatval($providerFeeFixed->value) : 0;
            $provider_fee_percentage = $providerFeePercentage ? floatval($providerFeePercentage->value) : 0;

            // CORRECT PLATFORM EARNINGS CALCULATION (Combined Fees)

            // 1. Customer Platform Fees (fixed + percentage) - from order record
            $customer_fee_from_percentage = ($base_service_amount * $customer_fee_percentage) / 100;
            $customer_platform_fee = $customer_fee_fixed + $customer_fee_from_percentage;

            // 2. Provider Platform Fees (fixed + percentage)  
            $provider_fee_from_percentage = ($base_service_amount * $provider_fee_percentage) / 100;
            $provider_platform_fee = $provider_fee_fixed + $provider_fee_from_percentage;

            // 3. Total Platform Earnings = Customer fees + Provider fees
            $platform_earnings_total = $customer_platform_fee + $provider_platform_fee;

            // Calculate what provider actually receives (base amount minus provider fees)
            $provider_net_earnings = $base_service_amount - $provider_platform_fee;

            // Tax amount (customer pays this separately - not deducted from provider)
            $region = $order->region ?? 'international';
            $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($region)])->first();
            if (!$tax) {
                $tax = RegionTax::whereRaw('LOWER(region) = ?', ['international'])->first();
            }

            $tax_amount = 0;
            if ($tax) {
                $tax_amount = $base_service_amount * ($tax->percentage / 100);
            }

            // Coupon discount (platform absorbs this)
            $coupon_discount = $order->discounted_amount ?? 0;

            if ($pending_earning) {
                // Update existing pending record to completed
                $pending_earning->update([
                    'gross_amount' => $base_service_amount,
                    'platform_fee_amount' => $provider_platform_fee, // What provider pays
                    'tax_amount' => $tax_amount,
                    'coupon_discount' => $coupon_discount,
                    'customer_platform_fee' => $customer_platform_fee,     // Customer fees
                    'provider_platform_fee' => $provider_platform_fee,     // Provider fees
                    'platform_earnings_total' => $platform_earnings_total, // Total platform earnings
                    'platform_fee_fixed' => $provider_fee_fixed,           // Snapshot
                    'platform_fee_percentage' => $provider_fee_percentage, // Snapshot
                    'net_earnings' => $provider_net_earnings,
                    'pricing_type' => $pricing_type,
                    'duration_value' => $duration_value,
                    'region' => $region,
                    'status' => 'completed',
                    'earned_at' => now()
                ]);
            } else {
                // Create new earnings record
                ServiceEarning::create([
                    'user_id' => $service_provider_id,
                    'order_id' => $order_id,
                    'service_id' => $order->service_id,
                    'gross_amount' => $base_service_amount,
                    'platform_fee_amount' => $provider_platform_fee, // What provider pays
                    'tax_amount' => $tax_amount,
                    'coupon_discount' => $coupon_discount,
                    'customer_platform_fee' => $customer_platform_fee,     // Customer fees
                    'provider_platform_fee' => $provider_platform_fee,     // Provider fees  
                    'platform_earnings_total' => $platform_earnings_total, // Total platform earnings
                    'platform_fee_fixed' => $provider_fee_fixed,           // Snapshot
                    'platform_fee_percentage' => $provider_fee_percentage, // Snapshot
                    'net_earnings' => $provider_net_earnings,
                    'pricing_type' => $pricing_type,
                    'duration_value' => $duration_value,
                    'region' => $region,
                    'status' => 'completed',
                    'earned_at' => now()
                ]);
            }

            Log::info('Platform earnings calculated correctly with existing table structure', [
                'order_id' => $order_id,
                'service_provider_id' => $service_provider_id,
                'base_service_amount' => $base_service_amount,
                'customer_platform_fee' => $customer_platform_fee,
                'provider_platform_fee' => $provider_platform_fee,
                'platform_earnings_total' => $platform_earnings_total,
                'provider_net_earnings' => $provider_net_earnings
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error calculating platform earnings', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Handle earnings when order is cancelled
     */
    private function handleCancelledOrderEarnings($order_id)
    {
        try {
            // Check if earnings record exists for this order
            $existing_earning = ServiceEarning::where('order_id', $order_id)->first();

            if ($existing_earning) {
                // Update status to cancelled - no earnings for cancelled orders
                $existing_earning->update(['status' => 'cancelled']);

                Log::info('Order earnings marked as cancelled', [
                    'order_id' => $order_id,
                    'earning_id' => $existing_earning->id
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error handling cancelled order earnings', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }


    /**
     * Create pending earnings record when order is created
     */
    private function createPendingEarnings($order_id)
    {
        try {
            // Get the order with service details
            $order = Order::with('service')->where('id', $order_id)->first();

            if (!$order || !$order->service) {
                Log::error('Order or service not found for pending earnings', ['order_id' => $order_id]);
                return false;
            }

            // Get service provider
            $service_provider_id = $order->service->user_id;

            // Calculate gross amount
            $gross_amount = 0;
            $pricing_type = $order->pricing_type ?? $order->service->pricing_type ?? 'hourly';
            $duration_value = $order->duration_value ?? $order->total_hours;

            switch ($pricing_type) {
                case 'hourly':
                    $gross_amount = $order->service->cost_per_hour * $duration_value;
                    break;
                case 'daily':
                    $gross_amount = $order->service->cost_per_day * $duration_value;
                    break;
                case 'fixed':
                    $gross_amount = $order->service->fixed_price;
                    $duration_value = 1;
                    break;
            }

            // Store pending earnings record
            ServiceEarning::create([
                'user_id' => $service_provider_id,
                'order_id' => $order_id,
                'service_id' => $order->service_id,
                'gross_amount' => $gross_amount,
                'platform_fee_amount' => 0, // Will be calculated when completed
                'tax_amount' => 0, // Will be calculated when completed
                'coupon_discount' => $order->discounted_amount ?? 0,
                'net_earnings' => 0, // Will be calculated when completed
                'pricing_type' => $pricing_type,
                'duration_value' => $duration_value,
                'region' => $order->region ?? 'international',
                'status' => 'pending',
                'earned_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error creating pending earnings', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Update pending earnings when order is modified
     * FIXED: Tax is not deducted from service provider
     */
    private function updatePendingEarnings($order_id, $updated_data)
    {
        try {
            // Find existing pending earnings record
            $pending_earning = ServiceEarning::where('order_id', $order_id)
                ->where('status', 'pending')
                ->first();

            if (!$pending_earning) {
                Log::warning('No pending earnings found to update', ['order_id' => $order_id]);
                return false;
            }

            // Get updated order with service details
            $order = Order::with('service')->where('id', $order_id)->first();

            if (!$order || !$order->service) {
                Log::error('Order or service not found for earnings update', ['order_id' => $order_id]);
                return false;
            }

            // Recalculate base service amount
            $base_service_amount = 0;
            $pricing_type = $order->pricing_type ?? $order->service->pricing_type ?? 'hourly';
            $duration_value = $order->duration_value ?? $order->total_hours;

            switch ($pricing_type) {
                case 'hourly':
                    $base_service_amount = $order->service->cost_per_hour * $duration_value;
                    break;
                case 'daily':
                    $base_service_amount = $order->service->cost_per_day * $duration_value;
                    break;
                case 'fixed':
                    $base_service_amount = $order->service->fixed_price;
                    $duration_value = 1;
                    break;
            }

            // Tax amount for record keeping (customer pays this)
            $region = $order->region ?? 'international';
            $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($region)])->first();
            if (!$tax) {
                $tax = RegionTax::whereRaw('LOWER(region) = ?', ['international'])->first();
            }

            $tax_amount = 0;
            if ($tax) {
                $tax_amount = $base_service_amount * ($tax->percentage / 100);
            }

            // Update the pending earnings record
            $pending_earning->update([
                'gross_amount' => $base_service_amount,
                'duration_value' => $duration_value,
                'pricing_type' => $pricing_type,
                'tax_amount' => $tax_amount, // For record keeping (customer pays this)
                'coupon_discount' => $order->discounted_amount ?? 0,
                'region' => $region
            ]);

            Log::info('Pending earnings updated', [
                'order_id' => $order_id,
                'new_gross_amount' => $base_service_amount
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating pending earnings', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get service provider's earnings history
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_earnings_history(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $earnings = ServiceEarning::with(['order', 'service'])
                ->where('user_id', $user->id)
                ->orderBy('earned_at', 'DESC')
                ->get();

            $earnings_data = $earnings->map(function ($earning) {
                return [
                    'id' => $earning->id,
                    'order_id' => $earning->order_id,
                    'service_name' => $earning->service->service ?? 'Service Deleted',
                    'gross_amount' => $earning->gross_amount,
                    'platform_fee_amount' => $earning->platform_fee_amount,
                    'tax_amount' => $earning->tax_amount,
                    'coupon_discount' => $earning->coupon_discount,
                    'net_earnings' => $earning->net_earnings,
                    'pricing_type' => $earning->pricing_type,
                    'duration_value' => $earning->duration_value,
                    'region' => $earning->region,
                    'status' => $earning->status,
                    'earned_at' => $earning->earned_at,
                    'created_at' => $earning->created_at
                ];
            });

            return response()->json([
                'message' => 'Success',
                'data' => $earnings_data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching earnings history', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to fetch earnings history',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    /**
     * Get service provider's earnings summary 
     * UPDATED: Shows that tax is paid by customer, not deducted from provider
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_earnings_summary(Request $request)
    {
        try {
            $user = auth("api")->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Total completed earnings (after platform fees, but NOT tax deduction)
            $total_earnings = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('net_earnings');

            // Total gross earnings (service provider's quoted amounts)
            $total_gross_earnings = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('gross_amount');

            // Pending earnings (from ongoing orders)
            $pending_earnings = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('gross_amount');

            // Total withdrawn
            $total_withdrawn = WithdrawRequest::where('user_id', $user->id)
                ->where('status', '<', 2)
                ->sum('amount');

            // Available balance (net earnings minus withdrawals)
            $available_balance = $total_earnings - $total_withdrawn;

            // Monthly earnings (current month) - net earnings
            $monthly_earnings = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('earned_at', now()->month)
                ->whereYear('earned_at', now()->year)
                ->sum('net_earnings');

            // Total platform fees paid (deducted from service provider)
            $total_platform_fees = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('platform_fee_amount');

            // Total taxes - for information only (customer paid these, not provider)
            $total_taxes_on_orders = ServiceEarning::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('tax_amount');

            return response()->json([
                'message' => 'Success',
                'data' => [
                    'total_gross_earnings' => $total_gross_earnings, // Your quoted service amounts
                    'total_earnings' => $total_earnings, // After platform fees only
                    'pending_earnings' => $pending_earnings,
                    'available_balance' => $available_balance,
                    'total_withdrawn' => $total_withdrawn,
                    'monthly_earnings' => $monthly_earnings,
                    'total_platform_fees_paid' => $total_platform_fees, // You paid this
                    'total_taxes_on_orders' => $total_taxes_on_orders, // Customer paid this (info only)
                    'note' => 'Platform fees are deducted from your earnings. Taxes are paid by customers.'
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching earnings summary', [
                'error' => $e->getMessage(),
                'user_id' => auth("api")->user()->id ?? null
            ]);

            return response()->json([
                'error' => 'Failed to fetch earnings summary',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    protected function notify_user($user_id, $title, $body)
    {
        $user_query = User::Where("id", "=", $user_id);
        if ($user_query->count() > 0) {
            $user = $user_query->first();
            $response = $this->sendNotification($user["device_token"], $title, $body);
            return response()->json($response, 200);
        } else {
            return response()->json([], 404);
        }
    }


    protected function sendNotification($deviceToken, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/goodone-73cff/messages:send';
        $accessToken =  $this->generateAccessToken('goodone-73cff-a404a8a9d747.json');
        if ($accessToken) {

            // Build the notification payload
            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ]
                ],
            ];
            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ];
            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            // Execute the request
            $response = curl_exec($ch);
            if ($response === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }

            curl_close($ch);
            return response()->json(["message" => "sent notification"], 200);
        } else {
            return response()->json(["message" => "couldn't send notification, info: $deviceToken, $title, $body "], 500);
        }
    }

    protected function generateAccessToken($serviceAccountPath)
    {
        // Read the service account JSON file
        // File path relative to the `storage/app` directory

        // Check if the file exists
        if (Storage::exists($serviceAccountPath)) {
            // Read the file contents
            $_serviceAccount = Storage::get($serviceAccountPath);
            $serviceAccount = json_decode($_serviceAccount, true);

            $header = json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]);

            $now = time();
            $claims = json_encode([
                'iss' => $serviceAccount['client_email'], // Issuer
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging', // Scope
                'aud' => 'https://oauth2.googleapis.com/token', // Audience
                'exp' => $now + 3600, // Expiry (1 hour)
                'iat' => $now, // Issued at
            ]);

            // Encode the header and claims
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlClaims = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claims));

            // Sign the JWT
            $signatureInput = $base64UrlHeader . '.' . $base64UrlClaims;
            $signature = '';
            openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            // Construct the JWT
            $jwt = $base64UrlHeader . '.' . $base64UrlClaims . '.' . $base64UrlSignature;

            // Exchange the JWT for an access token
            // $response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
            //     'http' => [
            //         'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            //         'method'  => 'POST',
            //         'content' => http_build_query([
            //             'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            //             'assertion' => $jwt,
            //         ]),
            //     ],
            // ]));

            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
            ];
            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]));

            // Execute the request
            $response = curl_exec($ch);

            $tokenInfo = json_decode($response, true);

            if (isset($tokenInfo['access_token'])) {
                return $tokenInfo['access_token'];
            } else {
                dd($response);
                throw new Exception('Failed to obtain access token: ' . $response);
            }
        } else {
            return false;
        }
    }
}
