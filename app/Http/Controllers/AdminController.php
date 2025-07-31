<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Subcategory;
use App\Models\WithdrawRequest;
use App\Models\AppSetting;
use App\Models\RegionTax;
use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Rating;
use App\Models\ServiceEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{


    public function admin_home(Request $request)
    {
        // Date ranges setup
        $start_year = new \DateTime('now');
        $start_year->modify('first day of this year');
        $end_year = new \DateTime('now');
        $end_year->modify('last day of this year');

        $start_month = new \DateTime('now');
        $start_month->modify('first day of this month');
        $end_month = new \DateTime('now');
        $end_month->modify('last day of this month');

        $start_past_month = new \DateTime('now');
        $start_past_month->modify('first day of last month');
        $end_past_month = new \DateTime('now');
        $end_past_month->modify('last day of last month');

        $start_today = new \DateTime('now');
        $start_today->modify('today 00:00:00');
        $end_today = new \DateTime('now');
        $end_today->modify('today 23:59:59');

        $start_yesterday = new \DateTime('-1 day');
        $start_yesterday->modify('today 00:00:00');
        $end_yesterday = new \DateTime('-1 day');
        $end_yesterday->modify('today 23:59:59');

        // Get statistics for all periods
        $stats_year = $this->aquire_stats($start_year, $end_year);
        $stats_month = $this->aquire_stats($start_month, $end_month);
        $stats_past_month = $this->aquire_stats($start_past_month, $end_past_month);
        $stats_day = $this->aquire_stats($start_today, $end_today);
        $stats_yesterday = $this->aquire_stats($start_yesterday, $end_yesterday);

        // Add completed orders count separately
        $stats_day['completed_orders'] = DB::table('order')
            ->where('status', '=', 2)
            ->whereBetween('created_at', [$start_today, $end_today])
            ->count();

        $stats_month['completed_orders'] = DB::table('order')
            ->where('status', '=', 2)
            ->whereBetween('created_at', [$start_month, $end_month])
            ->count();

        $stats_year['completed_orders'] = DB::table('order')
            ->where('status', '=', 2)
            ->whereBetween('created_at', [$start_year, $end_year])
            ->count();

        $stats_yesterday['completed_orders'] = DB::table('order')
            ->where('status', '=', 2)
            ->whereBetween('created_at', [$start_yesterday, $end_yesterday])
            ->count();

        // Calculate differences for daily stats
        if ($stats_yesterday["users"] > 0) {
            $stats_day["users_difference"] = (($stats_day["users"] / $stats_yesterday["users"]) - 1) * 100;
        } else {
            $stats_day["users_difference"] = $stats_day["users"] > 0 ? 100 : 0;
        }

        if ($stats_yesterday["services"] > 0) {
            $stats_day["services_difference"] = (($stats_day["services"] / $stats_yesterday["services"]) - 1) * 100;
        } else {
            $stats_day["services_difference"] = $stats_day["services"] > 0 ? 100 : 0;
        }

        if ($stats_yesterday["orders"] > 0) {
            $stats_day["orders_difference"] = (($stats_day["orders"] / $stats_yesterday["orders"]) - 1) * 100;
        } else {
            $stats_day["orders_difference"] = $stats_day["orders"] > 0 ? 100 : 0;
        }

        if ($stats_yesterday["revenue"] > 0) {
            $stats_day["revenue_difference"] = (($stats_day["revenue"] / $stats_yesterday["revenue"]) - 1) * 100;
        } else {
            $stats_day["revenue_difference"] = $stats_day["revenue"] > 0 ? 100 : 0;
        }

        if ($stats_yesterday["earnings"] > 0) {
            $stats_day["earnings_difference"] = (($stats_day["earnings"] / $stats_yesterday["earnings"]) - 1) * 100;
        } else {
            $stats_day["earnings_difference"] = $stats_day["earnings"] > 0 ? 100 : 0;
        }

        // Calculate differences for monthly stats
        if ($stats_past_month["users"] > 0) {
            $stats_month["users_difference"] = (($stats_month["users"] / $stats_past_month["users"]) - 1) * 100;
        } else {
            $stats_month["users_difference"] = $stats_month["users"] > 0 ? 100 : 0;
        }

        if ($stats_past_month["services"] > 0) {
            $stats_month["services_difference"] = (($stats_month["services"] / $stats_past_month["services"]) - 1) * 100;
        } else {
            $stats_month["services_difference"] = $stats_month["services"] > 0 ? 100 : 0;
        }

        if ($stats_past_month["orders"] > 0) {
            $stats_month["orders_difference"] = (($stats_month["orders"] / $stats_past_month["orders"]) - 1) * 100;
        } else {
            $stats_month["orders_difference"] = $stats_month["orders"] > 0 ? 100 : 0;
        }

        if ($stats_past_month["revenue"] > 0) {
            $stats_month["revenue_difference"] = (($stats_month["revenue"] / $stats_past_month["revenue"]) - 1) * 100;
        } else {
            $stats_month["revenue_difference"] = $stats_month["revenue"] > 0 ? 100 : 0;
        }

        if ($stats_past_month["earnings"] > 0) {
            $stats_month["earnings_difference"] = (($stats_month["earnings"] / $stats_past_month["earnings"]) - 1) * 100;
        } else {
            $stats_month["earnings_difference"] = $stats_month["earnings"] > 0 ? 100 : 0;
        }

        // Get recent orders data
        $recentOrders = DB::table('order')
            ->join('users', 'users.id', '=', 'order.user_id')
            ->select('order.*', 'users.full_name', 'users.email')
            ->orderBy('order.created_at', 'desc')
            ->take(5)
            ->get();

        // Get system overview data
        $totalCustomers = User::where('type', 'customer')->count();
        $totalProviders = User::where('type', 'worker')->count();
        $totalServices = Service::count();
        $activeServices = Service::where('active', true)->count();
        $pendingWithdrawals = WithdrawRequest::where('status', '<', 2)->count();

        return view("admin.modern_dashboard", [
            "month_stats" => $stats_month,
            "day_stats" => $stats_day,
            "stats_year" => $stats_year,
            "stats_yesterday" => $stats_yesterday,
            "stats_past_month" => $stats_past_month,
            "recentOrders" => $recentOrders,
            "totalCustomers" => $totalCustomers,
            "totalProviders" => $totalProviders,
            "totalServices" => $totalServices,
            "activeServices" => $activeServices,
            "pendingWithdrawals" => $pendingWithdrawals,
        ]);
    }

    public function aquire_stats($from, $to)
    {
        // Convert DateTime objects to Y-m-d format for SQLite compatibility
        $from_date = $from->format('Y-m-d');
        $to_date = $to->format('Y-m-d');

        // Count users created in date range
        $users = User::whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->count();

        // Count services created in date range  
        $services = Service::whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->count();

        // Get ALL orders in date range (for order count)
        $all_orders = DB::table('order')
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date);

        $orders = $all_orders->count();

        // FIXED: Calculate TRUE PLATFORM EARNINGS from stored records (SQLite compatible)
        $platform_earnings_query = ServiceEarning::where('status', 'completed')
            ->whereDate('earned_at', '>=', $from_date)
            ->whereDate('earned_at', '<=', $to_date);

        // Get total platform earnings (sum of all customer + provider fees)
        $true_platform_revenue = $platform_earnings_query->sum('platform_earnings_total');

        // Get breakdown for transparency
        $total_customer_fees = $platform_earnings_query->sum('customer_platform_fee');
        $total_provider_fees = $platform_earnings_query->sum('provider_platform_fee');
        $total_provider_earnings = $platform_earnings_query->sum('net_earnings');
        $total_gross_amount = $platform_earnings_query->sum('gross_amount');

        // Calculate total transaction volume (what customers actually paid)
        // This includes: service cost + customer platform fees + taxes
        $completed_orders = DB::table('order')
            ->where('status', '=', 2)
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->get();

        $total_customer_payments = 0;
        foreach ($completed_orders as $order) {
            $total_customer_payments += floatval($order->price); // This includes service cost + platform fees + taxes
        }

        Log::info('Platform earnings stats calculated', [
            'period' => [$from_date, $to_date],
            'platform_earnings_total' => $true_platform_revenue,
            'customer_fees' => $total_customer_fees,
            'provider_fees' => $total_provider_fees,
            'provider_earnings' => $total_provider_earnings,
            'customer_payments' => $total_customer_payments,
            'query_dates' => ['from' => $from_date, 'to' => $to_date]
        ]);

        return [
            "users" => $users,
            "services" => $services,
            "orders" => $orders,
            "revenue" => $true_platform_revenue,  // FIXED: True platform earnings
            "earnings" => $true_platform_revenue, // Same as revenue
            "total_customer_payments" => $total_customer_payments,
            "total_provider_earnings" => $total_provider_earnings,
            "customer_platform_fees" => $total_customer_fees,      // NEW: Customer fees breakdown
            "provider_platform_fees" => $total_provider_fees,      // NEW: Provider fees breakdown
            "gross_service_amount" => $total_gross_amount,          // NEW: Total service amounts
        ];
    }

    // users
    public function get_users(Request $request)
    {
        $users = User::Where([["type", "=", "customer"]])->get();
        foreach ($users as $user) {

            $user_id = $user->id;
            $total_orders = 0;
            $total_discounts = 0;
            $orders = Order::select("*")->Where([["order.user_id", "=", $user_id], ["order.status", "=", 2]])->get();
            foreach ($orders as $order) {
                $total_amount = $order->coupon_percentage == null ? $order->price : ($order->price / (100 - $order->coupon_percentage)) * 100;
                $total_discounts += ($order->price / (100 - $order->coupon_percentage)) * $order->coupon_percentage;
                $total_orders += $total_amount;
            }
            $user["total_orders"] = $total_orders;
            $user["total_discounts"] = $total_discounts;
        }
        return view("admin.users", ["users" => $users]);
    }

    public function get_service_ratings(Request $request, Service $service)
    {
        $ratings = Rating::With("user")->Where([["service_id", "=", $service->id]])->get();
        return view("admin.ratings", ["ratings" => $ratings]);
    }
    public function delete_rating(Request $request, Rating $rating)
    {
        $rating->delete();
        return redirect()->back();
    }

    public function get_services(Request $request)
    {
        if (isset($request->user_id)) {
            $services = Service::Where([["user_id", "=", $request->user_id]])->get();
        } else {
            $services = Service::all();
        }
        foreach ($services as $service) {

            $user = User::Where([["id", "=", $service->user_id]])->first();
            if ($user == null) {
                $user = (object)["full_name" => "Deleted User"];
            }
            $user_id = $service->user_id;
            $total_orders = 0;
            $total_discounts = 0;
            $orders = Order::select("*")->Where([["order.service_id", "=", $service->id], ["order.status", "=", 2]])->get();
            foreach ($orders as $order) {
                $total_amount = $order->coupon_percentage == null ? $order->price : ($order->price / (100 - $order->coupon_percentage)) * 100;
                $total_discounts += ($order->price / (100 - $order->coupon_percentage)) * $order->coupon_percentage;
                $total_orders += $total_amount;
            }
            $service["total_orders"] = $total_orders;
            $service["total_discounts"] = $total_discounts;
            $service["user"] = $user;
        }
        return view("admin.services", ["services" => $services]);
    }

    public function toggle_service_activation(Request $request, Service $service)
    {
        if ($service->active) {
            $service->update(["active" => false]);
        } else {
            $service->update(["active" => true]);
        }

        return redirect()->back();
    }


    public function get_service_providers(Request $request)
    {
        $users = User::Where([["type", "=", "worker"]])->get();
        foreach ($users as $user) {

            $user_id = $user->id;
            $balance = 0;
            $withdrawn = 0;
            $total_orders = 0;
            $requests = WithdrawRequest::Where([
                ["user_id", "=", $user_id],
                ['status', "<", 2]
            ])->get();
            foreach ($requests as $request) {
                $withdrawn += $request["amount"];
            }
            $orders = Order::join('services', "services.id", "=", "order.service_id")->select("services.*", "order.*")->Where([["services.user_id", "=", $user_id], ["order.status", "=", 2]])->get();
            foreach ($orders as $order) {
                $balance += $order["total_hours"] * $order["cost_per_hour"];
                $total_orders += $order["total_hours"] * $order["cost_per_hour"];
            }
            $balance -= $withdrawn;
            $user["balance"] = $balance;
            $user["total_orders"] = $total_orders;
        }
        return view("admin.service_providers", ["users" => $users]);
    }

    // user
    public function get_user(Request $request, User $user)
    {
        return view("admin.user", ["user" => $user]);
    }

    public function edit_user(Request $request, User $user)
    {
        // $user = User::Where("id", "=", $id);
        if ($user->count() > 0) {
            $validation = $request->validate([
                'full_name' => 'sometimes|string',
                'verified_liscence' => 'sometimes|boolean',
                'security_check' => 'sometimes|boolean',
                // 'password' => 'sometimes|string'
            ]);


            if (isset($validation["password"])) $validation["password"] = bcrypt($validation["password"]);
            if (isset($validation["verified_liscence"])) $validation["verified_liscence"] = $validation["verified_liscence"] == 1 ? true : false;
            if (isset($validation["security_check"])) $validation["security_check"] = $validation["security_check"] == 1 ? true : false;

            // if($request->file('image')){
            //     $file = $request->file('image');
            //     $temp = $file->store('public/images');
            //     $_array = explode("/", $temp);
            //     $file_name = $_array[ sizeof($_array) -1 ];
            //     $validation["image"] = $file_name;
            // }
            $user->update($validation);
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }


    public function activate_user(Request $request, User $user)
    {
        $user->update(["active" => true]);
        return redirect()->back();
    }

    public function deactivate_user(Request $request, User $user)
    {
        $user->update(["active" => false]);
        return redirect()->back();
    }


    public function block_user(Request $request, User $user)
    {
        $user->update(["blocked" => true]);
        return redirect()->back();
    }

    public function unblock_user(Request $request, User $user)
    {
        $user->update(["blocked" => false]);
        return redirect()->back();
    }


    public function get_orders(Request $request)
    {
        if (isset($request->user_id)) {
            $user = User::Where([["id", "=", $request->user_id]])->first();
            if (is_null($user) == false) {
                if ($user->type == "customer") {
                    $orders = Order::Where([["user_id", "=", $request->user_id]])->get();
                } else {
                    $services = Service::Where([["user_id", "=", $request->user_id]])->get();
                    foreach ($service as $service) {
                        $service_orders = Order::Where([["service_id", "=", $service->id]])->get();
                        if (isset($orders)) {
                            $orders->merge($service_orders);
                        } else {
                            $orders = $service_orders;
                        }
                    }
                }
            } else {
                $orders = [];
            }
        } elseif (isset($request->service_id)) {
            $orders = Order::Where([["service_id", "=", $request->service_id]])->get();
        } else {
            $orders = Order::all();
        }
        foreach ($orders as $order) {
            $service = Service::Where([["id", "=", $order->service_id]])->first();
            $user = User::Where([["id", "=", $order->user_id]])->first();
            $order["user"] = $user;
            $order["service"] = $service;
        }
        return view("admin.orders", ["orders" => $orders]);
    }

    public function get_transactions(Request $request, User $user)
    {
        $total_transactions = [];
        if ($user->type == "customer") {
            $orders = Order::Where([["user_id", "=", $user->id], ["status", ">", 0]])->orderBy('updated_at', 'DESC')->get();
            foreach ($orders as $order) $total_transactions[] = [
                "type" => "order",
                "values" => $order
            ];
        } else {
            $orders = Order::join('services', "services.id", "=", "order.service_id")->select("services.*", "order.*")->Where([["services.user_id", "=", $user->id], ["order.status", ">", 0]])->orderBy('order.updated_at', 'DESC')->get();
            $withdrawals = WithdrawRequest::Where([["status", "<", "2"]])->orderBy('updated_at', 'DESC')->get();
            $merged_dates_array = [];
            foreach ($orders as $order) $merged_dates_array[] = ["type" => "order", "values" => $order, "date" => $order->updated_at];
            foreach ($withdrawals as $withdrawal) $merged_dates_array[] = ["type" => "withdrawal", "values" => $withdrawal, "date" => $withdrawal->updated_at];
            usort($merged_dates_array, fn($a, $b) => $a['date'] <=> $b['date']);
            foreach ($merged_dates_array as $item) {
                $total_transactions[] = [
                    "type" => $item["type"],
                    "values" => $item["values"]
                ];
            }

            // $orders = Order::Where([["user_id", "=", $user->id], ["status", ">", 0]])->orderBy('updated_at','DESC')->get();
        }
        return view("admin.transactions", ["transactions" => $total_transactions]);
    }


    public function edit_setting($key, $value)
    {

        $setting = AppSetting::Where("key", "=", $key);
        if ($setting->count() > 0) {
            $setting->update(["value" => $value]);
        } else {
            AppSetting::create(["key" => $key, "value" =>  $value]);
        }
    }

    public function get_default_images($type = "customer")
    {

        $customer = AppSetting::Where("key", "=", "customer-image");
        $provider = AppSetting::Where("key", "=", "provider-image");
        $customer_image = "";
        $provider_image = "";
        $current_customer_image = "";
        $current_provider_image = "";
        if ($customer->count() > 0) {
            $current_customer_image = $customer->first()->value;
        }
        if ($provider->count() > 0) {
            $current_provider_image = $provider->first()->value;
        }
        return view("admin.default_images", ["customer_image" => $customer_image, "provider_image" => $provider_image, "current_provider_image" => $current_provider_image, "current_customer_image" => $current_customer_image]);
    }

    public function edit_default_images(Request $request)
    {

        // $validation = $request->validate([
        //     "customer_image" => "file",
        //     "provider_image" => "file",
        // ]);
        $validation = [];
        if ($request->file('customer_image')) {
            $file = $request->file('customer_image');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["customer_image"] = $file_name;
        }
        if ($request->file('provider_image')) {
            $file = $request->file('provider_image');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["provider_image"] = $file_name;
        }

        if (isset($validation["customer_image"])) $this->edit_setting("customer-image", $validation["customer_image"]);
        if (isset($validation["provider_image"])) $this->edit_setting("provider-image", $validation["provider_image"]);

        return redirect()->back();
    }

    public function get_app_settings(Request $request)
    {
        $_settings = AppSetting::all();
        $settings = [];
        foreach ($_settings as $setting) {
            $settings[$setting->key] = $setting->value;
        }
        return view("admin.app_settings", ["settings" => $settings]);
    }

    public function edit_app_settings(Request $request)
    {

        if (isset($request->platform_fees))  $this->edit_setting("platform_fees", $request->platform_fees);
        if (isset($request->platform_fees_percentage))  $this->edit_setting("platform_fees_percentage", $request->platform_fees_percentage);
        // if(isset( $request->platform_fees ))  $this->edit_setting("platform_fees", $request->platform_fees);
        return redirect()->back();
    }




    public function create_coupon()
    {
        return view('admin.coupons', ["coupons" => Coupon::all()]);
    }

    public function withdraw_requests()
    {
        return view('admin.withdrawals', ["requests" => WithdrawRequest::Where([["status", "<", "2"]])->get()]);
    }

    public function accept_withdraw_request(Request $request, WithdrawRequest $withdraw_request)
    {
        $withdraw_request->update(["status" => "1"]);
        return redirect()->back();
    }

    public function reject_withdraw_request(Request $request, WithdrawRequest $withdraw_request)
    {
        $withdraw_request->update(["status" => "2"]);
        return redirect()->back();
    }

    public function store_coupon(Request $request)
    {
        $validation = $request->validate([
            'coupon' => 'required|unique:coupons,coupon',
            "max_usage" => "required",
            "percentage" => "required",
        ]);


        $category = Coupon::create($validation);
        return redirect()->back();
        // return response()->json($category);

    }

    public function delete_coupon(Request $request)
    {
        $validation = $request->validate([
            'id' => 'required|exists:coupons,id',
        ]);
        Coupon::find($validation["id"])->delete();
        return redirect()->back();
    }

    public function create_region_tax()
    {
        return view('admin.region_taxes', ["regions" => RegionTax::all()]);
    }


    public function store_region_tax(Request $request)
    {
        $validation = $request->validate([
            'region' => 'required',
            'percentage' => 'required',
        ]);

        $category = RegionTax::create($validation);
        return redirect()->back();
        // return response()->json($category);

    }

    public function edit_region_tax(Request $request, RegionTax $region)
    {

        $update = [];

        if (isset($request->region)) $update["region"] = $request->region;
        if (isset($request->percentage)) $update["percentage"] = $request->percentage;
        $region->update($update);
        return redirect()->back();
    }



    public function delete_region_tax(Request $request)
    {
        $validation = $request->validate([
            'id' => 'required',
        ]);
        RegionTax::find($validation["id"])->delete();
        return redirect()->back();
    }

    public function create_category()
    {
        return view('admin.category', ["categories" => Category::all()]);
    }


    public function edit_category(Request $request, Category $category)
    {

        return view('admin.edit_category', ["category" => $category]);
    }

    public function update_category(Request $request, Category $category)
    {

        $update = [];

        if (isset($request->category_name)) $update["name"] = $request->category_name;

        if ($request->file('image')) {
            $file = $request->file('image');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $update["image"] = $file_name;
        }
        $category->update($update);
        return redirect(route("admin_create_category"));
    }


    public function store_category(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|unique:categories,name',
            "image" => "file|required",
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $temp = $file->store('public/images');
            $_array = explode("/", $temp);
            $file_name = $_array[sizeof($_array) - 1];
            $validation["image"] = $file_name;
        }

        $category = Category::create($validation);
        return redirect()->back();
        // return response()->json($category);

    }


    public function delete_category(Request $request, Category $category)
    {
        Schema::disableForeignKeyConstraints();
        DB::delete('delete from subcategories where category_id = ?', [$category["id"]]);
        DB::delete('delete from categories where id = ?', [$category["id"]]);
        Schema::enableForeignKeyConstraints();
        // Category::find($validation["id"])->delete();
        return redirect()->back();
    }

    public function create_subcategory()
    {
        return view('admin.subcategory', ["categories" => Category::get(["id as value", "name"]), "subcategories" => Subcategory::all()]);
    }



    public function edit_subcategory(Request $request, Subcategory $subcategory)
    {

        return view('admin.edit_subcategory', ["subcategory" => $subcategory]);
    }

    public function update_subcategory(Request $request,  Subcategory $subcategory)
    {

        $update = [];

        if (isset($request->subcategory_name)) $update["name"] = $request->subcategory_name;

        $subcategory->update($update);
        return redirect(route("admin_create_subcategory"));
    }


    public function store_subcategory(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|unique:subcategories,name',
            "category_id" => "required|exists:categories,id",
        ]);

        $category = Subcategory::create($validation);
        return redirect()->back();
        // return response()->json($category);

    }


    public function delete_subcategory(Request $request, Subcategory $subcategory)
    {

        Schema::disableForeignKeyConstraints();
        DB::delete('delete from subcategories where id = ?', [$subcategory->id]);
        Schema::enableForeignKeyConstraints();
        return redirect()->back();
    }

    public function delete_service(Request $request, Service $service)
    {

        Schema::disableForeignKeyConstraints();
        DB::delete('delete from services where id = ?', [$service->id]);
        Schema::enableForeignKeyConstraints();
        return redirect()->back();
    }


    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin_home');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Add rate limiting for security (prevents brute force attacks)
        $key = 'admin_login_attempts:' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (cache()->has($key) && cache()->get($key) >= $maxAttempts) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . $decayMinutes . ' minutes.'
            ])->withInput($request->only('email'));
        }

        // Try to authenticate using admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $admin = Auth::guard('admin')->user();

            // Check if admin is active
            if (!$admin->active) {
                Auth::guard('admin')->logout();
                $this->incrementLoginAttempts($key);

                return back()->withErrors([
                    'email' => 'Admin account is deactivated.'
                ])->withInput($request->only('email'));
            }

            // Clear login attempts on successful login
            cache()->forget($key);

            // Regenerate session for security
            $request->session()->regenerate();

            // Update last login time
            $admin->update(['last_login_at' => now()]);

            return redirect()->intended(route('admin_home'));
        }

        // Failed login - increment attempts
        $this->incrementLoginAttempts($key);

        return back()->withErrors([
            'email' => 'Invalid email or password.'
        ])->withInput($request->only('email'));
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form')
            ->with('message', 'Successfully logged out.');
    }

    /**
     * Helper method to increment login attempts (prevents brute force)
     */
    private function incrementLoginAttempts($key)
    {
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, now()->addMinutes(15));
    }


    /**
     * Enhanced platform statistics with comprehensive KPIs, charts, and insights
     */
    public function platform_statistics(Request $request)
    {
        try {
            // Get date range from request (default 30 days)
            $dateRange = (int) $request->get('date_range', 30);
            $endDate = now();
            $startDate = now()->subDays($dateRange);

            // Calculate previous period for comparison
            $previousStartDate = $startDate->copy()->subDays($dateRange);
            $previousEndDate = $startDate->copy();

            // Calculate KPIs
            $kpis = $this->calculateKPIs($startDate, $endDate, $previousStartDate, $previousEndDate);

            // Calculate chart data
            $charts = $this->calculateChartsData($startDate, $endDate, $dateRange);

            // Calculate insights
            $insights = $this->calculateDetailedInsights($startDate, $endDate);

            // Metadata
            $meta = [
                'date_range' => $dateRange,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'last_updated' => now()->format('M d, Y H:i'),
                'status' => 'success'
            ];

            return view('admin.platform_statistics', compact('kpis', 'charts', 'insights', 'meta'));
        } catch (\Exception $e) {
            Log::error('Platform statistics error: ' . $e->getMessage());

            return view('admin.platform_statistics', [
                'kpis' => $this->getEmptyKPIs(),
                'charts' => $this->getEmptyCharts(),
                'insights' => $this->getEmptyInsights(),
                'meta' => [
                    'date_range' => $dateRange ?? 30,
                    'start_date' => now()->subDays(30)->format('Y-m-d'),
                    'end_date' => now()->format('Y-m-d'),
                    'last_updated' => now()->format('M d, Y H:i'),
                    'status' => 'error',
                    'error_message' => 'Unable to load statistics data'
                ]
            ]);
        }
    }

    /**
     * Calculate chart data for visualizations
     */
    private function calculateChartsData($startDate, $endDate, $dateRange)
    {
        return [
            'orders_trend' => $this->getOrdersTrendData($startDate, $endDate, $dateRange),
            'revenue_trend' => $this->getRevenueTrendData($startDate, $endDate, $dateRange),
            'users_trend' => $this->getUsersTrendData($startDate, $endDate, $dateRange),
            'orders_by_category' => $this->getOrdersByCategoryData($startDate, $endDate),
            'providers_by_region' => $this->getProvidersByRegionData()
        ];
    }

    /**
     * Get orders trend data for charts
     */
    private function getOrdersTrendData($startDate, $endDate, $dateRange)
    {
        // Simple daily grouping for all date ranges
        $orders = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        foreach ($orders as $order) {
            $labels[] = \Carbon\Carbon::parse($order->date)->format('M d');
            $data[] = (int) $order->count;
        }

        // Ensure we have at least some data points
        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get revenue trend data for charts
     */
    private function getRevenueTrendData($startDate, $endDate, $dateRange)
    {
        $revenue = Order::selectRaw('DATE(created_at) as date, SUM(price) as total')
            ->where('status', 2) // completed orders only
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        foreach ($revenue as $rev) {
            $labels[] = \Carbon\Carbon::parse($rev->date)->format('M d');
            $data[] = round((float) ($rev->total ?? 0), 2);
        }

        // Ensure we have at least some data points
        if (empty($labels)) {
            $labels = ['No Data'];
            $data = [0];
        }

        return ['labels' => $labels, 'data' => $data];
    }


    /**
     * Calculate high-level KPIs with growth percentages
     */
    private function calculateKPIs($startDate, $endDate, $previousStartDate, $previousEndDate)
    {
        // Current period data
        $currentOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $currentUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $currentServices = Service::whereBetween('created_at', [$startDate, $endDate])->count();

        // Current period revenue (from completed orders)
        $currentRevenue = Order::where('status', 2)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price') ?? 0;

        // Previous period data
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $previousUsers = User::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $previousServices = Service::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $previousRevenue = Order::where('status', 2)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('price') ?? 0;

        // Calculate growth percentages
        $ordersGrowth = $this->calculateGrowth($currentOrders, $previousOrders);
        $usersGrowth = $this->calculateGrowth($currentUsers, $previousUsers);
        $servicesGrowth = $this->calculateGrowth($currentServices, $previousServices);
        $revenueGrowth = $this->calculateGrowth($currentRevenue, $previousRevenue);

        // Total stats
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalCustomers = User::where('type', 'customer')->count();
        $totalProviders = User::where('type', 'worker')->count();
        $totalServices = Service::count();
        $activeServices = Service::where('active', true)->count();
        $totalRevenue = Order::where('status', 2)->sum('price') ?? 0;
        $pendingPayouts = WithdrawRequest::where('status', 0)->sum('amount') ?? 0;
        $pendingPayoutCount = WithdrawRequest::where('status', 0)->count();

        return [
            'total_orders' => [
                'value' => $totalOrders,
                'period_value' => $currentOrders,
                'growth' => $ordersGrowth,
                'icon' => 'bx-shopping-bag',
                'color' => 'primary'
            ],
            'total_users' => [
                'value' => $totalUsers,
                'period_value' => $currentUsers,
                'growth' => $usersGrowth,
                'breakdown' => [
                    'customers' => $totalCustomers,
                    'providers' => $totalProviders
                ],
                'icon' => 'bx-user',
                'color' => 'success'
            ],
            'total_services' => [
                'value' => $totalServices,
                'active' => $activeServices,
                'pending' => $totalServices - $activeServices,
                'period_value' => $currentServices,
                'growth' => $servicesGrowth,
                'utilization' => $totalServices > 0 ? round(($activeServices / $totalServices) * 100, 1) : 0,
                'icon' => 'bx-grid-alt',
                'color' => 'info'
            ],
            'total_revenue' => [
                'value' => $totalRevenue,
                'period_value' => $currentRevenue,
                'growth' => $revenueGrowth,
                'platform_revenue' => $totalRevenue * 0.1, // Assuming 10% platform fee
                'icon' => 'bx-dollar-circle',
                'color' => 'warning'
            ],
            'pending_payouts' => [
                'value' => $pendingPayouts,
                'count' => $pendingPayoutCount,
                'icon' => 'bx-money-withdraw',
                'color' => 'danger'
            ]
        ];
    }


    /**
     * Get users registration trend data
     */
    private function getUsersTrendData($startDate, $endDate, $dateRange)
    {
        $users = User::selectRaw("DATE(created_at) as date, 
                          SUM(CASE WHEN type = 'customer' THEN 1 ELSE 0 END) as customers,
                          SUM(CASE WHEN type = 'worker' THEN 1 ELSE 0 END) as providers")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $customers = [];
        $providers = [];

        foreach ($users as $user) {
            $labels[] = \Carbon\Carbon::parse($user->date)->format('M d');
            $customers[] = (int) $user->customers;
            $providers[] = (int) $user->providers;
        }

        // Ensure we have at least some data points
        if (empty($labels)) {
            $labels = ['No Data'];
            $customers = [0];
            $providers = [0];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $customers,
                    'color' => '#4e73df'
                ],
                [
                    'label' => 'Providers',
                    'data' => $providers,
                    'color' => '#1cc88a'
                ]
            ]
        ];
    }

    /**
     * Calculate detailed insights
     */
    private function calculateDetailedInsights($startDate, $endDate)
    {
        return [
            'order_insights' => $this->getOrderInsights($startDate, $endDate),
            'user_insights' => $this->getUserInsights($startDate, $endDate),
            'service_insights' => $this->getServiceInsights($startDate, $endDate),
            'financial_overview' => $this->getFinancialOverview($startDate, $endDate)
        ];
    }

    /**
     * Helper method to calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }


    /**
     * Get empty data structures for error states
     */
    private function getEmptyKPIs()
    {
        return [
            'total_orders' => ['value' => 0, 'period_value' => 0, 'growth' => 0, 'icon' => 'bx-shopping-bag', 'color' => 'primary'],
            'total_users' => ['value' => 0, 'period_value' => 0, 'growth' => 0, 'breakdown' => ['customers' => 0, 'providers' => 0], 'icon' => 'bx-user', 'color' => 'success'],
            'total_services' => ['value' => 0, 'active' => 0, 'pending' => 0, 'utilization' => 0, 'icon' => 'bx-grid-alt', 'color' => 'info'],
            'total_revenue' => ['value' => 0, 'period_value' => 0, 'growth' => 0, 'platform_revenue' => 0, 'icon' => 'bx-dollar-circle', 'color' => 'warning'],
            'pending_payouts' => ['value' => 0, 'count' => 0, 'icon' => 'bx-money-withdraw', 'color' => 'danger']
        ];
    }


    private function getEmptyCharts()
    {
        return [
            'orders_trend' => ['labels' => ['No Data'], 'data' => [0]],
            'revenue_trend' => ['labels' => ['No Data'], 'data' => [0]],
            'users_trend' => ['labels' => ['No Data'], 'datasets' => [['label' => 'Customers', 'data' => [0], 'color' => '#4e73df'], ['label' => 'Providers', 'data' => [0], 'color' => '#1cc88a']]],
            'orders_by_category' => ['labels' => ['No Data'], 'data' => [['value' => 0, 'color' => '#e2e8f0']]],
            'providers_by_region' => ['labels' => ['No Data'], 'data' => [['value' => 0, 'color' => '#e2e8f0']]]
        ];
    }

    private function getEmptyInsights()
    {
        return [
            'order_insights' => ['top_services' => collect([]), 'avg_order_value' => 0, 'avg_completion_time' => 'N/A'],
            'user_insights' => ['daily_registrations' => collect([]), 'active_users' => 0, 'retention_rate' => 0],
            'service_insights' => ['services_added' => 0, 'services_pending' => 0, 'top_rated_services' => collect([])],
            'financial_overview' => ['period_revenue' => 0, 'platform_profit' => 0, 'estimated_monthly' => 0, 'profit_margin' => 0]
        ];
    }

    /**
     * Get orders by category data
     */
    private function getOrdersByCategoryData($startDate, $endDate)
    {
        $orders = Order::join('services', 'services.id', '=', 'order.service_id')
            ->join('categories', 'categories.id', '=', 'services.category_id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->whereBetween('order.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'DESC')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'];

        if ($orders->isEmpty()) {
            return ['labels' => ['No Data'], 'data' => [['value' => 0, 'color' => '#e2e8f0']]];
        }

        foreach ($orders as $index => $order) {
            $labels[] = $order->name;
            $data[] = [
                'value' => (int) $order->count,
                'color' => $colors[$index % count($colors)]
            ];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get providers by region data (Canadian provinces stored in city column)
     */
    private function getProvidersByRegionData()
    {
        // List of valid Canadian provinces/territories
        $validProvinces = [
            'Alberta',
            'British Columbia',
            'Manitoba',
            'New Brunswick',
            'Newfoundland and Labrador',
            'Northwest Territories',
            'Nova Scotia',
            'Nunavut',
            'Ontario',
            'Prince Edward Island',
            'Québec',
            'Saskatchewan',
            'Yukon'
        ];

        // Get providers by province (stored in city column)
        $providers = User::selectRaw('COALESCE(NULLIF(city, ""), "Unknown") as province_name, COUNT(*) as count')
            ->where('type', 'worker')
            ->where('country', 'Canada')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereIn('city', $validProvinces) // Only include valid Canadian provinces
            ->groupBy('province_name')
            ->orderBy('count', 'DESC')
            ->get();

        // Add users with unknown/invalid province data
        $unknownCount = User::where('type', 'worker')
            ->where(function ($query) use ($validProvinces) {
                $query->whereNull('city')
                    ->orWhere('city', '')
                    ->orWhereNull('country')
                    ->orWhere('country', '!=', 'Canada')
                    ->orWhereNotIn('city', $validProvinces);
            })
            ->count();

        $labels = [];
        $data = [];
        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#17a2b8'];

        if ($providers->isEmpty() && $unknownCount == 0) {
            return ['labels' => ['No Data'], 'data' => [['value' => 0, 'color' => '#e2e8f0']]];
        }

        $index = 0;

        // Add valid provinces
        foreach ($providers as $provider) {
            $labels[] = $provider->province_name;
            $data[] = [
                'value' => (int) $provider->count,
                'color' => $colors[$index % count($colors)]
            ];
            $index++;
        }

        // Add unknown data if exists
        if ($unknownCount > 0) {
            $labels[] = 'Unknown/Other';
            $data[] = [
                'value' => $unknownCount,
                'color' => $colors[$index % count($colors)]
            ];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get order insights
     */
    private function getOrderInsights($startDate, $endDate)
    {
        $topServices = Order::join('services', 'services.id', '=', 'order.service_id')
            ->selectRaw('services.service, services.id, COUNT(*) as order_count')
            ->whereBetween('order.created_at', [$startDate, $endDate])
            ->groupBy('services.id', 'services.service')
            ->orderBy('order_count', 'DESC')
            ->limit(5)
            ->get();

        $avgOrderValue = Order::whereBetween('created_at', [$startDate, $endDate])->avg('price') ?? 0;

        return [
            'top_services' => $topServices,
            'avg_order_value' => round($avgOrderValue, 2),
            'avg_completion_time' => '2.5 days' // You can calculate this based on your order status changes
        ];
    }

    /**
     * Get user insights
     */
    private function getUserInsights($startDate, $endDate)
    {
        $dailyRegistrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->limit(7)
            ->get();

        $activeUsers = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        return [
            'daily_registrations' => $dailyRegistrations,
            'active_users' => $activeUsers,
            'retention_rate' => 75 // You can calculate this based on repeat orders
        ];
    }

    /**
     * Get service insights
     */
    private function getServiceInsights($startDate, $endDate)
    {
        $servicesAdded = Service::whereBetween('created_at', [$startDate, $endDate])->count();
        $servicesPending = Service::where('active', false)->count();

        return [
            'services_added' => $servicesAdded,
            'services_pending' => $servicesPending,
            'top_rated_services' => collect([]) // You can implement this based on your rating system
        ];
    }

    /**
     * Get financial overview
     */
    private function getFinancialOverview($startDate, $endDate)
    {
        $periodRevenue = Order::where('status', 2)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price') ?? 0;

        // Assuming 10% platform fee
        $platformProfit = $periodRevenue * 0.1;

        $daysInPeriod = $endDate->diffInDays($startDate);
        $dailyAverage = $daysInPeriod > 0 ? $platformProfit / $daysInPeriod : 0;
        $estimatedMonthly = $dailyAverage * 30;

        return [
            'period_revenue' => $periodRevenue,
            'platform_profit' => $platformProfit,
            'revenue_by_category' => collect([]),
            'estimated_monthly' => round($estimatedMonthly, 2),
            'profit_margin' => $periodRevenue > 0 ? round(($platformProfit / $periodRevenue) * 100, 1) : 0
        ];
    }
}
