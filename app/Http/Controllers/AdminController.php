<?php

namespace App\Http\Controllers;


use App\Models\WithdrawRequest;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Rating;
use App\Models\ServiceEarning;
use App\Models\CustomerTransaction;
use App\Models\AdminActivityLog;
use App\Models\CustomerSession;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Subcategory;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use App\Models\RegionTax;

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

        // FIXED: Calculate TRUE PLATFORM EARNINGS using combined fee system
        $platform_earnings_query = ServiceEarning::where('status', 'completed')
            ->whereDate('earned_at', '>=', $from_date)
            ->whereDate('earned_at', '<=', $to_date);

        // Get total platform earnings (customer fees + provider fees)
        $true_platform_revenue = $platform_earnings_query->sum('platform_earnings_total');

        // Get breakdown for transparency
        $total_customer_fees = $platform_earnings_query->sum('customer_platform_fee');
        $total_provider_fees = $platform_earnings_query->sum('provider_platform_fee');
        $total_provider_earnings = $platform_earnings_query->sum('net_earnings');
        $total_gross_amount = $platform_earnings_query->sum('gross_amount');

        // Calculate total transaction volume (what customers actually paid)
        $completed_orders = DB::table('order')
            ->where('status', '=', 2)
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->get();

        $total_customer_payments = 0;
        foreach ($completed_orders as $order) {
            $total_customer_payments += floatval($order->price);
        }

        Log::info('Platform earnings stats calculated with combined fees', [
            'period' => [$from_date, $to_date],
            'platform_earnings_total' => $true_platform_revenue,
            'customer_fees' => $total_customer_fees,
            'provider_fees' => $total_provider_fees,
            'provider_earnings' => $total_provider_earnings,
            'customer_payments' => $total_customer_payments,
        ]);

        return [
            "users" => $users,
            "services" => $services,
            "orders" => $orders,
            "revenue" => $true_platform_revenue,  // FIXED: Combined platform earnings
            "earnings" => $true_platform_revenue, // Same as revenue
            "total_customer_payments" => $total_customer_payments,
            "total_provider_earnings" => $total_provider_earnings,
            "customer_platform_fees" => $total_customer_fees,
            "provider_platform_fees" => $total_provider_fees,
            "gross_service_amount" => $total_gross_amount,
        ];
    }

    public function edit_setting($key, $value)
    {
        try {
            $setting = AppSetting::where("key", "=", $key)->first();

            if ($setting) {
                $setting->update(["value" => $value]);
            } else {
                AppSetting::create([
                    "key" => $key,
                    "value" => $value,
                    "created_at" => now(),
                    "updated_at" => now()
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating app setting: ' . $e->getMessage());
            return false;
        }
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
            'Qu�bec',
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

    /**
     * Enhanced customer listing with advanced filtering and search
     */
    public function get_customers(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->get('search');
            $status = $request->get('status', 'all'); // all, active, blocked, verified
            $sort = $request->get('sort', 'created_at');
            $direction = $request->get('direction', 'desc');
            $per_page = $request->get('per_page', 12);

            // Base query for customers only
            $query = User::where('type', 'customer');

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%")
                        ->orWhere('country', 'LIKE', "%{$search}%");
                });
            }

            // Status filtering
            switch ($status) {
                case 'active':
                    $query->where('active', true)->where('blocked', false);
                    break;
                case 'blocked':
                    $query->where('blocked', true);
                    break;
                case 'inactive':
                    $query->where('active', false);
                    break;
                case 'verified':
                    $query->where('verified', true);
                    break;
                case 'unverified':
                    $query->where('verified', false);
                    break;
            }

            // Sorting
            $allowedSorts = ['created_at', 'full_name', 'email', 'total_orders', 'total_spent'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'total_orders' || $sort === 'total_spent') {
                    // For computed fields, we'll sort after loading
                    $customers = $query->orderBy('created_at', $direction)->get();
                } else {
                    $customers = $query->orderBy($sort, $direction)->paginate($per_page);
                }
            } else {
                $customers = $query->orderBy('created_at', $direction)->paginate($per_page);
            }

            // If not paginated yet, convert to collection and add computed fields
            if (!$customers instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $customers = $query->orderBy('created_at', $direction)->get();
            }

            // Add computed fields for each customer
            foreach ($customers as $customer) {
                $this->addCustomerComputedFields($customer);
            }

            // If we need to sort by computed fields and haven't paginated yet
            if (($sort === 'total_orders' || $sort === 'total_spent') &&
                !$customers instanceof \Illuminate\Pagination\LengthAwarePaginator
            ) {

                $customers = $customers->sortBy($sort, SORT_REGULAR, $direction === 'desc');

                // Manual pagination for computed field sorting
                $currentPage = $request->get('page', 1);
                $customers = new \Illuminate\Pagination\LengthAwarePaginator(
                    $customers->forPage($currentPage, $per_page),
                    $customers->count(),
                    $per_page,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }

            // Get customer statistics
            $stats = $this->getCustomerStatistics();

            return view('admin.customers.index', compact('customers', 'stats', 'search', 'status', 'sort', 'direction'));
        } catch (\Exception $e) {
            Log::error('Error fetching customers: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to load customers. Please try again.']);
        }
    }

    /**
     * Show detailed customer profile - FIXED VERSION
     */
    public function show_customer(Request $request, User $customer)
    {
        try {
            // Ensure this is a customer
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            // Add computed fields
            $this->addCustomerComputedFields($customer);

            // Get customer's orders with detailed information
            $orders = Order::where('user_id', $customer->id)
                ->with(['service', 'service.user', 'service.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'orders_page');

            // Get customer's ratings/reviews
            $ratings = Rating::where('user_id', $customer->id)
                ->with(['service', 'service.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'ratings_page');

            // Get customer's messages/communications (handle if Message model doesn't exist)
            try {
                $messages = Message::where(function ($query) use ($customer) {
                    $query->where('from', $customer->id)
                        ->orWhere('to', $customer->id);
                })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'messages_page');
            } catch (\Exception $e) {
                $messages = $this->createEmptyPaginatedResult('messages');
            }

            // Get customer's transaction history
            try {
                $transactions = CustomerTransaction::where('customer_id', $customer->id)
                    ->with('order')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'transactions_page');
            } catch (\Exception $e) {
                $transactions = $this->createEmptyPaginatedResult('transactions');
            }

            // Get customer's notifications (handle if table doesn't exist)
            try {
                $notifications = Notification::where('user_id', $customer->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'notifications_page');
            } catch (\Exception $e) {
                $notifications = $this->createEmptyPaginatedResult('notifications');
            }

            // Get admin activity logs for this customer
            try {
                $activity_logs = AdminActivityLog::where('customer_id', $customer->id)
                    ->with('admin')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10, ['*'], 'logs_page');
            } catch (\Exception $e) {
                $activity_logs = $this->createEmptyPaginatedResult('logs');
            }

            // Get customer login sessions (handle if table doesn't exist)
            try {
                $sessions = CustomerSession::where('customer_id', $customer->id)
                    ->orderBy('login_at', 'desc')
                    ->paginate(10, ['*'], 'sessions_page');
            } catch (\Exception $e) {
                $sessions = $this->createEmptyPaginatedResult('sessions');
            }

            // Calculate additional analytics
            $analytics = $this->getCustomerAnalytics($customer);

            return view('admin.customers.show', compact(
                'customer',
                'orders',
                'ratings',
                'messages',
                'transactions',
                'notifications',
                'activity_logs',
                'sessions',
                'analytics'
            ));
        } catch (\Exception $e) {
            Log::error('Error showing customer details: ' . $e->getMessage());
            return redirect()->route('admin_get_customers')
                ->withErrors(['error' => 'Unable to load customer details.']);
        }
    }

    /**
     * Show customer edit form
     */
    public function edit_customer_form(User $customer)
    {
        if ($customer->type !== 'customer') {
            return redirect()->route('admin_get_customers')
                ->withErrors(['error' => 'User is not a customer.']);
        }

        // Ensure customer has default image if no image set
        $customer = $this->ensureCustomerHasImage($customer);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update customer information - FIXED CHECKBOX VERSION
     */
    public function update_customer(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            // Simple validation
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $customer->id,
                'phone' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // FIXED: Handle checkboxes properly - only true if checkbox is checked
            $validated['verified'] = $request->has('verified') && $request->verified == '1';
            $validated['blocked'] = $request->has('blocked') && $request->blocked == '1';
            $validated['active'] = $request->has('active') && $request->active == '1';

            // IMPORTANT: If active checkbox is not checked, keep customer active (don't block them)
            if (!$request->has('active')) {
                $validated['active'] = true; // Keep customer active by default
            }

            // Handle password
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Handle image upload
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in the same location as your app uses
                $file->storeAs('public/images', $filename);
                $validated['picture'] = $filename;
            }

            // Update customer
            $customer->update($validated);

            return redirect()->route('admin_show_customer', $customer)
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update customer.'])
                ->withInput();
        }
    }

    /**
     * Block/Unblock customer
     */
    public function toggle_customer_block(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            $oldStatus = $customer->blocked;
            $newStatus = !$oldStatus;
            $customer->update(['blocked' => $newStatus]);

            // Log admin activity
            $action = $newStatus ? 'blocked' : 'unblocked';
            $this->logAdminActivity(
                Auth::guard('admin')->id(),
                $customer->id,
                $action,
                "Customer {$action} by admin",
                ['blocked' => $oldStatus],
                ['blocked' => $newStatus],
                $request->ip(),
                $request->userAgent()
            );

            $message = $newStatus ? 'Customer blocked successfully.' : 'Customer unblocked successfully.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling customer block status: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to update customer status.']);
        }
    }

    /**
     * Activate/Deactivate customer
     */
    public function toggle_customer_activation(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            $oldStatus = $customer->active;
            $newStatus = !$oldStatus;
            $customer->update(['active' => $newStatus]);

            // Log admin activity
            $action = $newStatus ? 'activated' : 'deactivated';
            $this->logAdminActivity(
                Auth::guard('admin')->id(),
                $customer->id,
                $action,
                "Customer {$action} by admin",
                ['active' => $oldStatus],
                ['active' => $newStatus],
                $request->ip(),
                $request->userAgent()
            );

            $message = $newStatus ? 'Customer activated successfully.' : 'Customer deactivated successfully.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling customer activation: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to update customer status.']);
        }
    }

    /**
     * Verify/Unverify customer
     */
    public function toggle_customer_verification(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            $oldStatus = $customer->verified;
            $newStatus = !$oldStatus;
            $customer->update(['verified' => $newStatus]);

            // Log admin activity
            $action = $newStatus ? 'verified' : 'unverified';
            $this->logAdminActivity(
                Auth::guard('admin')->id(),
                $customer->id,
                $action,
                "Customer {$action} by admin",
                ['verified' => $oldStatus],
                ['verified' => $newStatus],
                $request->ip(),
                $request->userAgent()
            );

            $message = $newStatus ? 'Customer verified successfully.' : 'Customer verification removed.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling customer verification: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to update customer verification.']);
        }
    }

    /**
     * Delete customer (with confirmation) - FIXED VERSION with Foreign Key Handling
     */
    public function delete_customer(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            // Check if customer has active orders (status 0 = unprocessed, 1 = pending)
            $activeOrders = Order::where('user_id', $customer->id)
                ->whereIn('status', [0, 1]) // only unprocessed and pending
                ->count();

            if ($activeOrders > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "Cannot delete customer with {$activeOrders} active orders. Please complete or cancel orders first."]);
            }

            // Store customer data for logging
            $customerData = $customer->toArray();

            // Log admin activity before deletion
            $this->logAdminActivity(
                Auth::guard('admin')->id(),
                $customer->id,
                'deleted',
                'Customer account deleted by admin',
                $customerData,
                [],
                $request->ip(),
                $request->userAgent()
            );

            // Delete profile image if exists
            if ($customer->picture) {
                $imagePaths = [
                    storage_path('app/public/customer_images/' . $customer->picture),
                    storage_path('app/public/images/' . $customer->picture),
                    public_path('storage/images/' . $customer->picture)
                ];

                foreach ($imagePaths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                        break;
                    }
                }
            }

            // Delete related records to avoid foreign key constraint violations
            DB::transaction(function () use ($customer) {
                // Delete customer transactions
                CustomerTransaction::where('customer_id', $customer->id)->delete();

                // Delete admin activity logs
                AdminActivityLog::where('customer_id', $customer->id)->delete();

                // Delete customer sessions
                CustomerSession::where('customer_id', $customer->id)->delete();

                // Delete ratings by this customer
                Rating::where('user_id', $customer->id)->delete();

                // Delete notifications for this customer
                try {
                    Notification::where('user_id', $customer->id)->delete();
                } catch (\Exception $e) {
                    // Ignore if notifications table doesn't exist
                }

                // Delete messages involving this customer
                try {
                    Message::where('from', $customer->id)->orWhere('to', $customer->id)->delete();
                } catch (\Exception $e) {
                    // Ignore if messages table doesn't exist
                }

                // Update or delete orders (set user_id to null or delete)
                // Option 1: Set user_id to null (keep order history)
                Order::where('user_id', $customer->id)->update(['user_id' => null]);

                // Option 2: Delete orders (uncomment if you prefer to delete orders)
                // Order::where('user_id', $customer->id)->delete();

                // Finally delete the customer
                $customer->delete();
            });

            return redirect()->route('admin_get_customers')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to delete customer: ' . $e->getMessage()]);
        }
    }

    /**
     * Add transaction for customer (manual adjustment)
     */
    public function add_customer_transaction(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return redirect()->route('admin_get_customers')
                    ->withErrors(['error' => 'User is not a customer.']);
            }

            $validated = $request->validate([
                'type' => 'required|in:credit,debit,refund',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:500',
                'payment_method' => 'sometimes|string|max:100'
            ]);

            $transaction = CustomerTransaction::create([
                'customer_id' => $customer->id,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'currency' => 'CAD',
                'payment_method' => $validated['payment_method'] ?? 'manual_adjustment',
                'status' => 'completed',
                'description' => $validated['description'],
                'metadata' => ['added_by_admin' => Auth::guard('admin')->id()],
                'processed_at' => now()
            ]);

            // Log admin activity
            $this->logAdminActivity(
                Auth::guard('admin')->id(),
                $customer->id,
                'transaction_added',
                "Manual transaction added: {$validated['type']} of {$validated['amount']}",
                [],
                $validated,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Transaction added successfully.');
        } catch (\Exception $e) {
            Log::error('Error adding customer transaction: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to add transaction.']);
        }
    }

    /**
     * Add computed fields to customer model
     */
    private function addCustomerComputedFields(User $customer)
    {
        // Calculate total orders
        $customer->total_orders = Order::where('user_id', $customer->id)->count();

        // Calculate completed orders
        $customer->completed_orders = Order::where('user_id', $customer->id)
            ->where('status', 2)
            ->count();

        // Calculate total spent (from order prices)
        $customer->total_spent = Order::where('user_id', $customer->id)
            ->where('status', 2)
            ->sum('price') ?? 0;

        // Calculate total savings from coupons
        $customer->total_savings = Order::where('user_id', $customer->id)
            ->where('status', 2)
            ->sum('discounted_amount') ?? 0;

        // Get average rating given by customer
        $customer->average_rating = Rating::where('user_id', $customer->id)
            ->avg('rate') ?? 0;

        // Count unread messages
        $customer->unread_messages = Message::where('to', $customer->id)
            ->where('seen_by_to', false)
            ->count();

        // Last order date
        $lastOrder = Order::where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->first();
        $customer->last_order_date = $lastOrder ? $lastOrder->created_at : null;

        // Account age in days
        $customer->account_age_days = $customer->created_at->diffInDays(now());

        // Status badge info
        $customer->status_badge = $this->getCustomerStatusBadge($customer);
    }

    /**
     * Get customer status badge information - FIXED VERSION
     */
    private function getCustomerStatusBadge(User $customer): array
    {
        // Priority order: blocked > inactive > verified > active
        if ($customer->blocked) {
            return ['text' => 'Blocked', 'class' => 'danger'];
        }

        if (!$customer->active) {
            return ['text' => 'Inactive', 'class' => 'warning'];
        }

        // Email verified customers (this is your main verification)
        if ($customer->email_verified_at) {
            return ['text' => 'Email Verified', 'class' => 'success'];
        }

        // Manual admin verification (secondary verification)
        if ($customer->verified) {
            return ['text' => 'Admin Verified', 'class' => 'info'];
        }

        return ['text' => 'Unverified Email', 'class' => 'warning'];
    }

    /**
     * Get customer statistics for dashboard
     */
    private function getCustomerStatistics(): array
    {
        $total = User::where('type', 'customer')->count();
        $active = User::where('type', 'customer')->where('active', true)->where('blocked', false)->count();
        $blocked = User::where('type', 'customer')->where('blocked', true)->count();
        $verified = User::where('type', 'customer')->where('verified', true)->count();
        $new_this_month = User::where('type', 'customer')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Calculate total spent by all customers
        $total_spent = Order::where('status', 2)->sum('price') ?? 0;

        // Calculate average order value
        $avg_order_value = Order::where('status', 2)->avg('price') ?? 0;

        return compact(
            'total',
            'active',
            'blocked',
            'verified',
            'new_this_month',
            'total_spent',
            'avg_order_value'
        );
    }

    /**
     * Get detailed analytics for specific customer
     */
    private function getCustomerAnalytics(User $customer): array
    {
        $analytics = [];

        // Order analytics
        $analytics['orders'] = [
            'total' => Order::where('user_id', $customer->id)->count(),
            'completed' => Order::where('user_id', $customer->id)->where('status', 2)->count(),
            'pending' => Order::where('user_id', $customer->id)->where('status', 1)->count(),
            'cancelled' => Order::where('user_id', $customer->id)->where('status', 3)->count(),
        ];

        // Financial analytics
        $analytics['financial'] = [
            'total_spent' => Order::where('user_id', $customer->id)->where('status', 2)->sum('price') ?? 0,
            'total_savings' => Order::where('user_id', $customer->id)->where('status', 2)->sum('discounted_amount') ?? 0,
            'avg_order_value' => Order::where('user_id', $customer->id)->where('status', 2)->avg('price') ?? 0,
        ];

        // Activity analytics  
        $analytics['activity'] = [
            'ratings_given' => Rating::where('user_id', $customer->id)->count(),
            'messages_sent' => Message::where('from', $customer->id)->count(),
            'last_login' => $customer->updated_at, // Approximate
        ];

        // Monthly spending trend (last 6 months)
        $analytics['monthly_spending'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $spending = Order::where('user_id', $customer->id)
                ->where('status', 2)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('price') ?? 0;

            $analytics['monthly_spending'][] = [
                'month' => $date->format('M Y'),
                'amount' => $spending
            ];
        }

        return $analytics;
    }

    /**
     * Delete customer image and set to default
     */
    public function delete_customer_image(Request $request, User $customer)
    {
        try {
            if ($customer->type !== 'customer') {
                return response()->json(['success' => false, 'message' => 'User is not a customer.'], 400);
            }

            // Get default image from app settings
            $customerImageSetting = AppSetting::where("key", "=", "customer-image")->first();
            $defaultImageName = $customerImageSetting ? $customerImageSetting->value : '';

            // Check if current image is already default
            if ($customer->picture === $defaultImageName) {
                return response()->json(['success' => false, 'message' => 'Already using default image.'], 400);
            }

            $oldPicture = $customer->picture;

            // Delete the uploaded file if it exists
            if ($customer->picture && $customer->picture !== $defaultImageName) {
                $imagePath = storage_path('app/public/images/' . $customer->picture);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted custom image: ' . $imagePath);
                }
            }

            // Set to default image
            $customer->update(['picture' => $defaultImageName]);

            // Log the activity
            Log::info('Customer image deleted and set to default', [
                'customer_id' => $customer->id,
                'old_picture' => $oldPicture,
                'new_picture' => $defaultImageName,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully. Now using default image.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting customer image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer with default image if needed
     */
    private function ensureCustomerHasImage(User $customer)
    {
        if (!$customer->picture || $customer->picture === '') {
            // Get default image from settings
            $customerImageSetting = AppSetting::where("key", "=", "customer-image")->first();
            if ($customerImageSetting && $customerImageSetting->value) {
                $customer->update(['picture' => $customerImageSetting->value]);
            }
        }
        return $customer;
    }


    /**
     * Create dummy data for missing models to prevent errors
     */
    private function createEmptyPaginatedResult($name = 'sessions')
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect([]), // Empty collection
            0, // Total items
            10, // Items per page
            1, // Current page
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $name . '_page'
            ]
        );
    }

    /**
     * Log admin activity
     */
    private function logAdminActivity($adminId, $customerId, $action, $description, $oldValues = [], $newValues = [], $ipAddress = null, $userAgent = null)
    {
        try {
            AdminActivityLog::create([
                'admin_id' => $adminId,
                'customer_id' => $customerId,
                'action' => $action,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging admin activity: ' . $e->getMessage());
        }
    }


    // ===============================
    // CATEGORIES MANAGEMENT METHODS
    // ===============================

    /**
     * Get categories with search and pagination
     */
    public function get_categories(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->get('search');
            $per_page = $request->get('per_page', 12);

            // Base query
            $query = Category::query();

            // Search functionality
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            // Get categories with subcategory count
            $categories = $query->withCount('subcategories')
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);

            // Add additional data for each category
            foreach ($categories as $category) {
                // Count services using this category
                $category->services_count = Service::where('category_id', $category->id)->count();
                $category->active_services_count = Service::where('category_id', $category->id)
                    ->where('active', true)->count();
            }

            // Get statistics
            $stats = [
                'total_categories' => Category::count(),
                'total_subcategories' => Subcategory::count(),
                'categories_with_services' => Category::whereHas('services')->count(),
                'most_used_category' => Category::withCount('services')
                    ->orderBy('services_count', 'desc')
                    ->first()?->name ?? 'None'
            ];

            return view('admin.app-config.categories.index', compact('categories', 'stats', 'search'));
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to load categories. Please try again.']);
        }
    }

    /**
     * Show create category form
     */
    public function create_category_form()
    {
        return view('admin.app-config.categories.create');
    }

    /**
     * Store new category
     */
    public function store_category(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $temp = $file->store('public/images');
                $file_array = explode("/", $temp);
                $file_name = $file_array[sizeof($file_array) - 1];
                $validated['image'] = $file_name;
            }

            $category = Category::create($validated);

            return redirect()->route('admin_get_categories')
                ->with('success', 'Category created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to create category. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show edit category form
     */
    public function edit_category_form(Category $category)
    {
        // Get subcategories for this category
        $subcategories = Subcategory::where('category_id', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.app-config.categories.edit', compact('category', 'subcategories'));
    }

    /**
     * Update category
     */
    public function update_category(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image && file_exists(storage_path('app/public/images/' . $category->image))) {
                    unlink(storage_path('app/public/images/' . $category->image));
                }

                $file = $request->file('image');
                $temp = $file->store('public/images');
                $file_array = explode("/", $temp);
                $file_name = $file_array[sizeof($file_array) - 1];
                $validated['image'] = $file_name;
            }

            $category->update($validated);

            return redirect()->route('admin_get_categories')
                ->with('success', 'Category updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update category. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Delete category
     */
    public function delete_category(Request $request, Category $category)
    {
        try {
            // Check if category has services
            $servicesCount = Service::where('category_id', $category->id)->count();
            if ($servicesCount > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "Cannot delete category with {$servicesCount} services. Please reassign or delete services first."]);
            }

            // Delete category image if exists
            if ($category->image && file_exists(storage_path('app/public/images/' . $category->image))) {
                unlink(storage_path('app/public/images/' . $category->image));
            }

            // Delete subcategories first
            Subcategory::where('category_id', $category->id)->delete();

            // Delete category
            $category->delete();

            return redirect()->route('admin_get_categories')
                ->with('success', 'Category and its subcategories deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to delete category. Please try again.']);
        }
    }

    /**
     * Get subcategories for a specific category
     */
    public function get_subcategories(Request $request, Category $category)
    {
        try {
            $search = $request->get('search');
            $per_page = $request->get('per_page', 10);

            $query = Subcategory::where('category_id', $category->id);

            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            $subcategories = $query->orderBy('name')
                ->paginate($per_page);

            // Add service count for each subcategory
            foreach ($subcategories as $subcategory) {
                $subcategory->services_count = Service::where('subcategory_id', $subcategory->id)->count();
            }

            return view('admin.app-config.categories.edit', compact('category', 'subcategories', 'search'));
        } catch (\Exception $e) {
            Log::error('Error fetching subcategories: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to load subcategories.']);
        }
    }

    /**
     * Store new subcategory
     */
    public function store_subcategory(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:subcategories,name'
            ]);

            $validated['category_id'] = $category->id;

            Subcategory::create($validated);

            return redirect()->back()
                ->with('success', 'Subcategory added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating subcategory: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to create subcategory.'])
                ->withInput();
        }
    }

    /**
     * Update subcategory
     */
    public function update_subcategory(Request $request, Subcategory $subcategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:subcategories,name,' . $subcategory->id
            ]);

            $subcategory->update($validated);

            return redirect()->back()
                ->with('success', 'Subcategory updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Error updating subcategory: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update subcategory.']);
        }
    }

    /**
     * Delete subcategory
     */
    public function delete_subcategory(Request $request, Subcategory $subcategory)
    {
        try {
            // Check if subcategory has services
            $servicesCount = Service::where('subcategory_id', $subcategory->id)->count();
            if ($servicesCount > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "Cannot delete subcategory with {$servicesCount} services. Please reassign or delete services first."]);
            }

            $subcategory->delete();

            return redirect()->back()
                ->with('success', 'Subcategory deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting subcategory: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to delete subcategory.']);
        }
    }

    // ===============================
    // COUPONS MANAGEMENT METHODS
    // ===============================

    /**
     * Get coupons with search, pagination and usage analytics
     */
    public function get_coupons(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->get('search');
            $status = $request->get('status', 'all'); // all, active, expired, unlimited
            $sort = $request->get('sort', 'created_at');
            $direction = $request->get('direction', 'desc');
            $per_page = $request->get('per_page', 12);

            // Base query
            $query = Coupon::query();

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('coupon', 'LIKE', "%{$search}%")
                        ->orWhere('percentage', 'LIKE', "%{$search}%");
                });
            }

            // Status filtering
            switch ($status) {
                case 'active':
                    $query->whereRaw('times_used < max_usage');
                    break;
                case 'expired':
                    $query->whereRaw('times_used >= max_usage');
                    break;
                case 'unlimited':
                    $query->where('max_usage', 0);
                    break;
            }

            // Sorting
            $allowedSorts = ['created_at', 'coupon', 'percentage', 'times_used', 'max_usage'];
            if (in_array($sort, $allowedSorts)) {
                $coupons = $query->orderBy($sort, $direction)->paginate($per_page);
            } else {
                $coupons = $query->orderBy('created_at', $direction)->paginate($per_page);
            }

            // Add computed fields for each coupon
            foreach ($coupons as $coupon) {
                $this->addCouponComputedFields($coupon);
            }

            // Get statistics
            $stats = [
                'total_coupons' => Coupon::count(),
                'active_coupons' => Coupon::whereRaw('times_used < max_usage OR max_usage = 0')->count(),
                'expired_coupons' => Coupon::whereRaw('times_used >= max_usage AND max_usage > 0')->count(),
                'total_usage' => Coupon::sum('times_used'),
                'total_savings' => Order::where('status', 2)->sum('discounted_amount') ?? 0,
                'avg_discount' => Coupon::avg('percentage') ?? 0
            ];

            return view('admin.app-config.coupons.index', compact('coupons', 'stats', 'search', 'status', 'sort', 'direction'));
        } catch (\Exception $e) {
            Log::error('Error fetching coupons: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to load coupons. Please try again.']);
        }
    }

    /**
     * Show create coupon form
     */
    public function create_coupon_form()
    {
        return view('admin.app-config.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function store_coupon(Request $request)
    {
        try {
            $validated = $request->validate([
                'coupon' => 'required|string|max:50|unique:coupons,coupon|regex:/^[A-Z0-9]+$/',
                'percentage' => 'required|numeric|min:1|max:100',
                'max_usage' => 'required|integer|min:0',
                'description' => 'nullable|string|max:500'
            ], [
                'coupon.regex' => 'Coupon code must contain only uppercase letters and numbers.',
                'coupon.unique' => 'This coupon code already exists.',
                'percentage.min' => 'Discount percentage must be at least 1%.',
                'percentage.max' => 'Discount percentage cannot exceed 100%.',
                'max_usage.min' => 'Maximum usage must be 0 or greater (0 = unlimited).'
            ]);

            // Convert coupon to uppercase
            $validated['coupon'] = strtoupper($validated['coupon']);
            $validated['times_used'] = 0;

            $coupon = Coupon::create($validated);

            return redirect()->route('admin_get_coupons')
                ->with('success', 'Coupon created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating coupon: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to create coupon. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show edit coupon form
     */
    public function edit_coupon_form(Coupon $coupon)
    {
        // Get usage analytics
        $analytics = $this->getCouponAnalytics($coupon);

        return view('admin.app-config.coupons.edit', compact('coupon', 'analytics'));
    }

    /**
     * Update coupon
     */
    public function update_coupon(Request $request, Coupon $coupon)
    {
        try {
            $validated = $request->validate([
                'coupon' => 'required|string|max:50|unique:coupons,coupon,' . $coupon->id . '|regex:/^[A-Z0-9]+$/',
                'percentage' => 'required|numeric|min:1|max:100',
                'max_usage' => 'required|integer|min:0',
                'description' => 'nullable|string|max:500'
            ], [
                'coupon.regex' => 'Coupon code must contain only uppercase letters and numbers.',
                'coupon.unique' => 'This coupon code already exists.',
                'percentage.min' => 'Discount percentage must be at least 1%.',
                'percentage.max' => 'Discount percentage cannot exceed 100%.',
                'max_usage.min' => 'Maximum usage must be 0 or greater (0 = unlimited).'
            ]);

            // Convert coupon to uppercase
            $validated['coupon'] = strtoupper($validated['coupon']);

            // Prevent reducing max_usage below current times_used
            if ($validated['max_usage'] > 0 && $validated['max_usage'] < $coupon->times_used) {
                return redirect()->back()
                    ->withErrors(['max_usage' => "Maximum usage cannot be less than current usage ({$coupon->times_used})."])
                    ->withInput();
            }

            $coupon->update($validated);

            return redirect()->route('admin_get_coupons')
                ->with('success', 'Coupon updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating coupon: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update coupon. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Toggle coupon status (reset usage)
     */
    public function toggle_coupon_status(Request $request, Coupon $coupon)
    {
        try {
            $action = $request->get('action', 'reset');

            if ($action === 'reset') {
                $coupon->update(['times_used' => 0]);
                $message = 'Coupon usage reset successfully.';
            } else {
                return redirect()->back()->withErrors(['error' => 'Invalid action.']);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling coupon status: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to update coupon status.']);
        }
    }

    /**
     * Delete coupon
     */
    public function delete_coupon(Request $request, Coupon $coupon)
    {
        try {
            // Check if coupon has been used
            if ($coupon->times_used > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "Cannot delete coupon that has been used {$coupon->times_used} times. This would affect order history."]);
            }

            $coupon->delete();

            return redirect()->route('admin_get_coupons')
                ->with('success', 'Coupon deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting coupon: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to delete coupon. Please try again.']);
        }
    }

    /**
     * Add computed fields to coupon model
     */
    private function addCouponComputedFields(Coupon $coupon)
    {
        // Calculate status
        if ($coupon->max_usage == 0) {
            $coupon->status = 'unlimited';
            $coupon->status_text = 'Unlimited';
            $coupon->status_class = 'info';
        } elseif ($coupon->times_used >= $coupon->max_usage) {
            $coupon->status = 'expired';
            $coupon->status_text = 'Expired';
            $coupon->status_class = 'danger';
        } else {
            $coupon->status = 'active';
            $coupon->status_text = 'Active';
            $coupon->status_class = 'success';
        }

        // Calculate usage percentage
        if ($coupon->max_usage > 0) {
            $coupon->usage_percentage = min(100, ($coupon->times_used / $coupon->max_usage) * 100);
        } else {
            $coupon->usage_percentage = 0;
        }

        // Calculate remaining uses
        if ($coupon->max_usage > 0) {
            $coupon->remaining_uses = max(0, $coupon->max_usage - $coupon->times_used);
        } else {
            $coupon->remaining_uses = '∞';
        }

        // Calculate total savings from this coupon
        $coupon->total_savings = Order::where('coupon_id', $coupon->id)
            ->where('status', 2)
            ->sum('discounted_amount') ?? 0;

        // Get recent usage
        $coupon->recent_orders = Order::where('coupon_id', $coupon->id)
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get detailed analytics for specific coupon
     */
    private function getCouponAnalytics(Coupon $coupon)
    {
        $analytics = [];

        // Usage analytics
        $analytics['usage'] = [
            'total_uses' => $coupon->times_used,
            'remaining_uses' => $coupon->max_usage > 0 ? max(0, $coupon->max_usage - $coupon->times_used) : '∞',
            'usage_rate' => $coupon->max_usage > 0 ? round(($coupon->times_used / $coupon->max_usage) * 100, 1) : 0
        ];

        // Financial analytics
        $totalSavings = Order::where('coupon_id', $coupon->id)
            ->where('status', 2)
            ->sum('discounted_amount') ?? 0;

        $totalOrders = Order::where('coupon_id', $coupon->id)
            ->where('status', 2)
            ->count();

        $avgSavingsPerOrder = $totalOrders > 0 ? $totalSavings / $totalOrders : 0;

        $analytics['financial'] = [
            'total_savings' => $totalSavings,
            'total_orders' => $totalOrders,
            'avg_savings_per_order' => $avgSavingsPerOrder
        ];

        // Monthly usage trend (last 6 months)
        $analytics['monthly_usage'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $usage = Order::where('coupon_id', $coupon->id)
                ->where('status', 2)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $analytics['monthly_usage'][] = [
                'month' => $date->format('M Y'),
                'usage' => $usage
            ];
        }

        // Recent orders using this coupon
        $analytics['recent_orders'] = Order::where('coupon_id', $coupon->id)
            ->with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $analytics;
    }

    // ===============================
    // REGIONAL TAXES MANAGEMENT METHODS
    // ===============================

    /**
     * Get regional taxes with search and filtering
     */
    public function get_regional_taxes(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->get('search');
            $sort = $request->get('sort', 'region');
            $direction = $request->get('direction', 'asc');
            $per_page = $request->get('per_page', 15);

            // Base query
            $query = RegionTax::query();

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('region', 'LIKE', "%{$search}%")
                        ->orWhere('percentage', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $allowedSorts = ['region', 'percentage', 'created_at'];
            if (in_array($sort, $allowedSorts)) {
                $regionalTaxes = $query->orderBy($sort, $direction)->paginate($per_page);
            } else {
                $regionalTaxes = $query->orderBy('region', 'asc')->paginate($per_page);
            }

            // Add computed fields for each tax rate
            foreach ($regionalTaxes as $tax) {
                $this->addRegionalTaxComputedFields($tax);
            }

            // Get statistics
            $stats = [
                'total_regions' => RegionTax::count(),
                'avg_tax_rate' => RegionTax::avg('percentage') ?? 0,
                'highest_tax' => RegionTax::max('percentage') ?? 0,
                'lowest_tax' => RegionTax::min('percentage') ?? 0,
                'total_orders_with_tax' => Order::whereNotNull('taxed_amount')->where('status', 2)->count(),
                'total_tax_collected' => Order::where('status', 2)->sum('taxed_amount') ?? 0
            ];

            // Get Canadian provinces for easy setup
            $canadianProvinces = $this->getCanadianProvinces();

            return view('admin.app-config.regional-taxes.index', compact(
                'regionalTaxes',
                'stats',
                'search',
                'sort',
                'direction',
                'canadianProvinces'
            ));
        } catch (\Exception $e) {
            Log::error('Error fetching regional taxes: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Unable to load regional taxes. Please try again.']);
        }
    }

    /**
     * Show create regional tax form
     */
    public function create_regional_tax_form()
    {
        $canadianProvinces = $this->getCanadianProvinces();
        $existingRegions = RegionTax::pluck('region')->toArray();

        return view('admin.app-config.regional-taxes.create', compact('canadianProvinces', 'existingRegions'));
    }

    /**
     * Store new regional tax
     */
    public function store_regional_tax(Request $request)
    {
        try {
            $validated = $request->validate([
                'region' => 'required|string|max:255|unique:region_taxes,region',
                'percentage' => 'required|numeric|min:0|max:50',
                'description' => 'nullable|string|max:500'
            ], [
                'region.unique' => 'Tax rate for this region already exists.',
                'percentage.min' => 'Tax percentage cannot be negative.',
                'percentage.max' => 'Tax percentage cannot exceed 50%.',
            ]);

            // Format region name consistently
            $validated['region'] = trim($validated['region']);

            $regionalTax = RegionTax::create($validated);

            return redirect()->route('admin_get_regional_taxes')
                ->with('success', 'Regional tax rate created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating regional tax: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to create regional tax. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show edit regional tax form
     */
    public function edit_regional_tax_form(RegionTax $regionTax)
    {
        // Get usage analytics
        $analytics = $this->getRegionalTaxAnalytics($regionTax);
        $canadianProvinces = $this->getCanadianProvinces();

        return view('admin.app-config.regional-taxes.edit', compact('regionTax', 'analytics', 'canadianProvinces'));
    }

    /**
     * Update regional tax
     */
    public function update_regional_tax(Request $request, RegionTax $regionTax)
    {
        try {
            $validated = $request->validate([
                'region' => 'required|string|max:255|unique:region_taxes,region,' . $regionTax->id,
                'percentage' => 'required|numeric|min:0|max:50',
                'description' => 'nullable|string|max:500'
            ], [
                'region.unique' => 'Tax rate for this region already exists.',
                'percentage.min' => 'Tax percentage cannot be negative.',
                'percentage.max' => 'Tax percentage cannot exceed 50%.',
            ]);

            // Format region name consistently
            $validated['region'] = trim($validated['region']);

            $regionTax->update($validated);

            return redirect()->route('admin_get_regional_taxes')
                ->with('success', 'Regional tax rate updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating regional tax: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update regional tax. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Delete regional tax
     */
    public function delete_regional_tax(Request $request, RegionTax $regionTax)
    {
        try {
            // Check if this tax rate has been used in orders
            $ordersCount = Order::where('region', $regionTax->region)
                ->where('status', 2)
                ->count();

            if ($ordersCount > 0) {
                return redirect()->back()
                    ->withErrors(['error' => "Cannot delete tax rate used in {$ordersCount} completed orders. This would affect order history."]);
            }

            $regionTax->delete();

            return redirect()->route('admin_get_regional_taxes')
                ->with('success', 'Regional tax rate deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting regional tax: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to delete regional tax. Please try again.']);
        }
    }

    /**
     * Tax calculator tool
     */
    public function tax_calculator(Request $request)
    {
        $regionalTaxes = RegionTax::orderBy('region')->get();
        $calculations = [];

        if ($request->has('amount') && is_numeric($request->amount)) {
            $baseAmount = floatval($request->amount);

            foreach ($regionalTaxes as $tax) {
                $taxAmount = ($baseAmount * $tax->percentage) / 100;
                $totalAmount = $baseAmount + $taxAmount;

                $calculations[] = [
                    'region' => $tax->region,
                    'tax_rate' => $tax->percentage,
                    'base_amount' => $baseAmount,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount
                ];
            }
        }

        return view('admin.app-config.regional-taxes.calculator', compact('regionalTaxes', 'calculations'));
    }

    /**
     * Add computed fields to regional tax model
     */
    private function addRegionalTaxComputedFields(RegionTax $regionTax)
    {
        // Count orders using this tax rate
        $regionTax->orders_count = Order::where('region', $regionTax->region)
            ->where('status', 2)
            ->count();

        // Calculate total tax collected
        $regionTax->total_tax_collected = Order::where('region', $regionTax->region)
            ->where('status', 2)
            ->sum('taxed_amount') ?? 0;

        // Calculate average tax per order
        if ($regionTax->orders_count > 0) {
            $regionTax->avg_tax_per_order = $regionTax->total_tax_collected / $regionTax->orders_count;
        } else {
            $regionTax->avg_tax_per_order = 0;
        }

        // Get recent orders
        $regionTax->recent_orders = Order::where('region', $regionTax->region)
            ->with(['user', 'service'])
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Determine if this is a Canadian province
        $regionTax->is_canadian_province = in_array($regionTax->region, array_keys($this->getCanadianProvinces()));
    }

    /**
     * Get detailed analytics for specific regional tax
     */
    private function getRegionalTaxAnalytics(RegionTax $regionTax)
    {
        $analytics = [];

        // Usage analytics
        $totalOrders = Order::where('region', $regionTax->region)
            ->where('status', 2)
            ->count();

        $totalTaxCollected = Order::where('region', $regionTax->region)
            ->where('status', 2)
            ->sum('taxed_amount') ?? 0;

        $analytics['usage'] = [
            'total_orders' => $totalOrders,
            'total_tax_collected' => $totalTaxCollected,
            'avg_tax_per_order' => $totalOrders > 0 ? $totalTaxCollected / $totalOrders : 0
        ];

        // Monthly tax collection (last 6 months)
        $analytics['monthly_collection'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $collection = Order::where('region', $regionTax->region)
                ->where('status', 2)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('taxed_amount') ?? 0;

            $analytics['monthly_collection'][] = [
                'month' => $date->format('M Y'),
                'amount' => $collection
            ];
        }

        // Recent orders
        $analytics['recent_orders'] = Order::where('region', $regionTax->region)
            ->with(['user', 'service'])
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $analytics;
    }

    /**
     * Get Canadian provinces with their standard tax rates
     */
    private function getCanadianProvinces()
    {
        return [
            'Alberta' => 5.0,
            'British Columbia' => 12.0,
            'Manitoba' => 12.0,
            'New Brunswick' => 15.0,
            'Newfoundland and Labrador' => 15.0,
            'Northwest Territories' => 5.0,
            'Nova Scotia' => 15.0,
            'Nunavut' => 5.0,
            'Ontario' => 13.0,
            'Prince Edward Island' => 15.0,
            'Quebec' => 14.975,
            'Saskatchewan' => 11.0,
            'Yukon' => 5.0
        ];
    }

    // ===============================
    // PLATFORM FEES MANAGEMENT METHODS
    // ===============================

    /**
     * Enhanced get platform fees configuration page
     */
    public function get_platform_fees(Request $request)
    {
        try {
            // Get current platform fee settings with defaults
            $customerFeeFixed = AppSetting::where('key', 'customer_platform_fee')->first();
            $customerFeePercentage = AppSetting::where('key', 'customer_platform_fee_percentage')->first();
            $providerFeeFixed = AppSetting::where('key', 'provider_platform_fee_fixed')->first();
            $providerFeePercentage = AppSetting::where('key', 'provider_platform_fee_percentage')->first();

            // Set defaults if not configured
            $settings = [
                // Customer settings (combined fixed + percentage)
                'customer_platform_fee' => $customerFeeFixed ? $customerFeeFixed->value : '0',
                'customer_platform_fee_percentage' => $customerFeePercentage ? $customerFeePercentage->value : '2.5',
                'customer_platform_fee_type' => 'combined', // For compatibility

                // Provider settings (combined fixed + percentage)
                'provider_platform_fee_fixed' => $providerFeeFixed ? $providerFeeFixed->value : '0',
                'provider_platform_fee_percentage' => $providerFeePercentage ? $providerFeePercentage->value : '5.0',

                // COMPATIBILITY: Add old keys that the view still expects
                'provider_platform_fee' => $providerFeeFixed ? $providerFeeFixed->value : '0', // Maps to fixed amount
                'provider_platform_fee_type' => 'combined' // For compatibility
            ];

            // Calculate revenue analytics
            $analytics = $this->getPlatformFeeAnalytics($settings);

            return view('admin.app-config.platform-fees.index', compact('settings', 'analytics'));
        } catch (\Exception $e) {
            Log::error('Error fetching platform fees: ' . $e->getMessage());

            return view('admin.app-config.platform-fees.index', [
                'settings' => [
                    'customer_platform_fee' => '0',
                    'customer_platform_fee_percentage' => '2.5',
                    'customer_platform_fee_type' => 'combined',
                    'provider_platform_fee_fixed' => '0',
                    'provider_platform_fee_percentage' => '5.0',
                    'provider_platform_fee' => '0', // COMPATIBILITY
                    'provider_platform_fee_type' => 'combined'
                ],
                'analytics' => [],
                'error' => 'Unable to load platform fees configuration. Using defaults.'
            ]);
        }
    }

    /**
     * Enhanced update platform fees for combined customer fees
     */
    public function update_platform_fees(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_platform_fee' => 'required|numeric|min:0|max:50', // Fixed amount
                'customer_platform_fee_percentage' => 'required|numeric|min:0|max:50', // Percentage
                'provider_platform_fee_fixed' => 'required|numeric|min:0|max:50', // NEW: Provider fixed
                'provider_platform_fee_percentage' => 'required|numeric|min:0|max:50', // NEW: Provider percentage
                'reason' => 'nullable|string|max:500'
            ], [
                'customer_platform_fee.max' => 'Customer fixed fee cannot exceed $50.',
                'customer_platform_fee_percentage.max' => 'Customer percentage fee cannot exceed 50%.',
                'provider_platform_fee_fixed.max' => 'Provider fixed fee cannot exceed $50.',
                'provider_platform_fee_percentage.max' => 'Provider percentage fee cannot exceed 50%.',
            ]);

            // Get current settings for logging
            $oldSettings = [
                'customer_platform_fee' => AppSetting::where('key', 'customer_platform_fee')->first()?->value ?? '0',
                'customer_platform_fee_percentage' => AppSetting::where('key', 'customer_platform_fee_percentage')->first()?->value ?? '2.5',
                'provider_platform_fee_fixed' => AppSetting::where('key', 'provider_platform_fee_fixed')->first()?->value ?? '0',
                'provider_platform_fee_percentage' => AppSetting::where('key', 'provider_platform_fee_percentage')->first()?->value ?? '5.0'
            ];

            // Update settings
            $this->edit_setting('customer_platform_fee', $validated['customer_platform_fee']);
            $this->edit_setting('customer_platform_fee_percentage', $validated['customer_platform_fee_percentage']);
            $this->edit_setting('provider_platform_fee_fixed', $validated['provider_platform_fee_fixed']); // NEW
            $this->edit_setting('provider_platform_fee_percentage', $validated['provider_platform_fee_percentage']); // NEW

            // Log the change for audit trail
            $this->logPlatformFeeChange($oldSettings, $validated, $validated['reason'] ?? null);

            return redirect()->route('admin_get_platform_fees')
                ->with('success', 'Platform fees updated successfully. Changes will apply to new orders.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating platform fees: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update platform fees. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Platform fees calculator tool
     */
    public function fees_calculator(Request $request)
    {
        try {
            // Get current fee settings (combined system)
            $customerFeeFixed = AppSetting::where('key', 'customer_platform_fee')->first();
            $customerFeePercentage = AppSetting::where('key', 'customer_platform_fee_percentage')->first();
            $providerFeeFixed = AppSetting::where('key', 'provider_platform_fee_fixed')->first();
            $providerFeePercentage = AppSetting::where('key', 'provider_platform_fee_percentage')->first();

            $settings = [
                'customer_platform_fee' => $customerFeeFixed ? floatval($customerFeeFixed->value) : 0,
                'customer_platform_fee_percentage' => $customerFeePercentage ? floatval($customerFeePercentage->value) : 2.5,
                'provider_platform_fee_fixed' => $providerFeeFixed ? floatval($providerFeeFixed->value) : 0,
                'provider_platform_fee_percentage' => $providerFeePercentage ? floatval($providerFeePercentage->value) : 5.0,
            ];

            $calculations = [];

            if ($request->has('service_cost') && is_numeric($request->service_cost)) {
                $serviceCost = floatval($request->service_cost);

                // Calculate different scenarios
                $scenarios = [
                    ['name' => 'Current Settings', 'settings' => $settings],
                    ['name' => 'Lower Customer Fees (-$1 & -0.5%)', 'settings' => [
                        'customer_platform_fee' => max(0, $settings['customer_platform_fee'] - 1),
                        'customer_platform_fee_percentage' => max(0, $settings['customer_platform_fee_percentage'] - 0.5),
                        'provider_platform_fee_fixed' => $settings['provider_platform_fee_fixed'],
                        'provider_platform_fee_percentage' => $settings['provider_platform_fee_percentage'],
                    ]],
                    ['name' => 'Lower Provider Fees (-$1 & -0.5%)', 'settings' => [
                        'customer_platform_fee' => $settings['customer_platform_fee'],
                        'customer_platform_fee_percentage' => $settings['customer_platform_fee_percentage'],
                        'provider_platform_fee_fixed' => max(0, $settings['provider_platform_fee_fixed'] - 1),
                        'provider_platform_fee_percentage' => max(0, $settings['provider_platform_fee_percentage'] - 0.5),
                    ]],
                    ['name' => 'Higher Combined Fees (+$1 & +0.5%)', 'settings' => [
                        'customer_platform_fee' => min(50, $settings['customer_platform_fee'] + 1),
                        'customer_platform_fee_percentage' => min(50, $settings['customer_platform_fee_percentage'] + 0.5),
                        'provider_platform_fee_fixed' => min(50, $settings['provider_platform_fee_fixed'] + 1),
                        'provider_platform_fee_percentage' => min(50, $settings['provider_platform_fee_percentage'] + 0.5),
                    ]]
                ];

                foreach ($scenarios as $scenario) {
                    $calc = $this->calculateCombinedFeeBreakdown($serviceCost, $scenario['settings']);
                    $calc['scenario_name'] = $scenario['name'];
                    $calculations[] = $calc;
                }
            }

            return view('admin.app-config.platform-fees.calculator', compact('settings', 'calculations'));
        } catch (\Exception $e) {
            Log::error('Error in fees calculator: ' . $e->getMessage());

            return view('admin.app-config.platform-fees.calculator', [
                'settings' => [
                    'customer_platform_fee' => 0,
                    'customer_platform_fee_percentage' => 2.5,
                    'provider_platform_fee_fixed' => 0,
                    'provider_platform_fee_percentage' => 5.0,
                ],
                'calculations' => [],
                'error' => 'Unable to load calculator. Please try again.'
            ]);
        }
    }

    /**
     * Calculate combined fee breakdown (fixed + percentage for both customer and provider)
     */
    private function calculateCombinedFeeBreakdown($serviceCost, $settings)
    {
        try {
            $breakdown = [
                'service_cost' => (float) $serviceCost,
                'customer_fee' => 0,
                'provider_fee' => 0,
                'customer_pays' => 0,
                'provider_receives' => 0,
                'platform_revenue' => 0
            ];

            // Validate inputs
            if ($serviceCost <= 0) {
                return $breakdown;
            }

            // Calculate COMBINED customer fee (fixed + percentage)
            $customerFeeFixed = (float) ($settings['customer_platform_fee'] ?? 0);
            $customerFeePercentage = (float) ($settings['customer_platform_fee_percentage'] ?? 0);
            $customerFeeFromPercentage = ($serviceCost * $customerFeePercentage) / 100;
            $breakdown['customer_fee'] = $customerFeeFixed + $customerFeeFromPercentage;

            // Calculate COMBINED provider fee (fixed + percentage)
            $providerFeeFixed = (float) ($settings['provider_platform_fee_fixed'] ?? 0);
            $providerFeePercentage = (float) ($settings['provider_platform_fee_percentage'] ?? 0);
            $providerFeeFromPercentage = ($serviceCost * $providerFeePercentage) / 100;
            $breakdown['provider_fee'] = $providerFeeFixed + $providerFeeFromPercentage;

            // Calculate totals
            $breakdown['customer_pays'] = $serviceCost + $breakdown['customer_fee'];
            $breakdown['provider_receives'] = max(0, $serviceCost - $breakdown['provider_fee']); // Ensure non-negative
            $breakdown['platform_revenue'] = $breakdown['customer_fee'] + $breakdown['provider_fee'];

            // Calculate percentages
            $breakdown['customer_fee_percentage'] = $serviceCost > 0 ? ($breakdown['customer_fee'] / $serviceCost) * 100 : 0;
            $breakdown['provider_fee_percentage'] = $serviceCost > 0 ? ($breakdown['provider_fee'] / $serviceCost) * 100 : 0;
            $breakdown['platform_margin'] = $breakdown['customer_pays'] > 0 ? ($breakdown['platform_revenue'] / $breakdown['customer_pays']) * 100 : 0;

            return $breakdown;
        } catch (\Exception $e) {
            Log::error('Error calculating combined fee breakdown: ' . $e->getMessage());

            // Return safe defaults
            return [
                'service_cost' => (float) $serviceCost,
                'customer_fee' => 0,
                'provider_fee' => 0,
                'customer_pays' => (float) $serviceCost,
                'provider_receives' => (float) $serviceCost,
                'platform_revenue' => 0,
                'customer_fee_percentage' => 0,
                'provider_fee_percentage' => 0,
                'platform_margin' => 0
            ];
        }
    }

    /**
     * Reset platform fees to defaults
     */
    public function reset_platform_fees(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500'
            ]);

            // Get current settings for logging
            $oldSettings = [
                'customer_platform_fee' => AppSetting::where('key', 'customer_platform_fee')->first()?->value ?? '0',
                'customer_platform_fee_type' => AppSetting::where('key', 'customer_platform_fee_type')->first()?->value ?? 'percentage',
                'provider_platform_fee' => AppSetting::where('key', 'provider_platform_fee')->first()?->value ?? '0',
                'provider_platform_fee_type' => AppSetting::where('key', 'provider_platform_fee_type')->first()?->value ?? 'percentage'
            ];

            // Default settings
            $defaultSettings = [
                'customer_platform_fee' => '5.0',
                'customer_platform_fee_type' => 'percentage',
                'provider_platform_fee' => '5.0',
                'provider_platform_fee_type' => 'percentage'
            ];

            // Update to defaults
            $this->edit_setting('customer_platform_fee', $defaultSettings['customer_platform_fee']);
            $this->edit_setting('customer_platform_fee_type', $defaultSettings['customer_platform_fee_type']);
            $this->edit_setting('provider_platform_fee', $defaultSettings['provider_platform_fee']);
            $this->edit_setting('provider_platform_fee_type', $defaultSettings['provider_platform_fee_type']);

            // Log the reset
            $this->logPlatformFeeChange($oldSettings, $defaultSettings, $validated['reason'] ?? 'Reset to defaults');

            return redirect()->route('admin_get_platform_fees')
                ->with('success', 'Platform fees reset to default values successfully.');
        } catch (\Exception $e) {
            Log::error('Error resetting platform fees: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to reset platform fees. Please try again.']);
        }
    }

    /**
     * Calculate fee breakdown for given service cost and settings
     */
    private function calculateFeeBreakdown($serviceCost, $settings)
    {
        try {
            $breakdown = [
                'service_cost' => (float) $serviceCost,
                'customer_fee' => 0,
                'provider_fee' => 0,
                'customer_pays' => 0,
                'provider_receives' => 0,
                'platform_revenue' => 0
            ];

            // Validate inputs
            if ($serviceCost <= 0) {
                return $breakdown;
            }

            // UPDATED: Calculate COMBINED customer fee (fixed + percentage)
            if (isset($settings['customer_platform_fee']) && isset($settings['customer_platform_fee_percentage'])) {
                $customerFeeFixed = (float) $settings['customer_platform_fee'];
                $customerFeePercentage = (float) $settings['customer_platform_fee_percentage'];
                $customerFeeFromPercentage = ($serviceCost * $customerFeePercentage) / 100;
                $breakdown['customer_fee'] = $customerFeeFixed + $customerFeeFromPercentage;
            }

            // UPDATED: Calculate COMBINED provider fee (fixed + percentage)
            if (isset($settings['provider_platform_fee_fixed']) && isset($settings['provider_platform_fee_percentage'])) {
                $providerFeeFixed = (float) $settings['provider_platform_fee_fixed'];
                $providerFeePercentage = (float) $settings['provider_platform_fee_percentage'];
                $providerFeeFromPercentage = ($serviceCost * $providerFeePercentage) / 100;
                $breakdown['provider_fee'] = $providerFeeFixed + $providerFeeFromPercentage;
            } else {
                // FALLBACK: Support old single provider fee system for backward compatibility
                $providerFeeValue = (float) ($settings['provider_platform_fee'] ?? 0);
                if (($settings['provider_platform_fee_type'] ?? 'percentage') === 'percentage') {
                    $breakdown['provider_fee'] = ($serviceCost * $providerFeeValue) / 100;
                } else {
                    $breakdown['provider_fee'] = $providerFeeValue;
                }
            }

            // Calculate totals
            $breakdown['customer_pays'] = $serviceCost + $breakdown['customer_fee'];
            $breakdown['provider_receives'] = max(0, $serviceCost - $breakdown['provider_fee']); // Ensure non-negative
            $breakdown['platform_revenue'] = $breakdown['customer_fee'] + $breakdown['provider_fee'];

            // Calculate percentages
            $breakdown['customer_fee_percentage'] = $serviceCost > 0 ? ($breakdown['customer_fee'] / $serviceCost) * 100 : 0;
            $breakdown['provider_fee_percentage'] = $serviceCost > 0 ? ($breakdown['provider_fee'] / $serviceCost) * 100 : 0;
            $breakdown['platform_margin'] = $breakdown['customer_pays'] > 0 ? ($breakdown['platform_revenue'] / $breakdown['customer_pays']) * 100 : 0;

            return $breakdown;
        } catch (\Exception $e) {
            Log::error('Error calculating fee breakdown: ' . $e->getMessage());

            // Return safe defaults
            return [
                'service_cost' => (float) $serviceCost,
                'customer_fee' => 0,
                'provider_fee' => 0,
                'customer_pays' => (float) $serviceCost,
                'provider_receives' => (float) $serviceCost,
                'platform_revenue' => 0,
                'customer_fee_percentage' => 0,
                'provider_fee_percentage' => 0,
                'platform_margin' => 0
            ];
        }
    }

    /**
     * Enhanced Platform Fee Analytics with better error handling
     */
    private function getPlatformFeeAnalytics($settings)
    {
        $analytics = [];

        try {
            // Calculate total revenue from platform fees
            $totalOrders = Order::where('status', 2)->count();
            $totalRevenue = Order::where('status', 2)->sum('price') ?? 0;
            $totalPlatformFees = Order::where('status', 2)->sum('platform_fee_amount') ?? 0;

            // Handle division by zero
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 100;
            $avgPlatformFee = $totalOrders > 0 ? $totalPlatformFees / $totalOrders : 0;

            // Estimate revenue with current settings
            $estimatedFeeBreakdown = $this->calculateFeeBreakdown($avgOrderValue, $settings);

            $analytics['current'] = [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'total_platform_fees' => $totalPlatformFees,
                'avg_order_value' => $avgOrderValue,
                'avg_platform_fee' => $avgPlatformFee
            ];

            $analytics['projections'] = [
                'estimated_customer_fee' => $estimatedFeeBreakdown['customer_fee'],
                'estimated_provider_fee' => $estimatedFeeBreakdown['provider_fee'],
                'estimated_platform_revenue' => $estimatedFeeBreakdown['platform_revenue'],
                'estimated_monthly_revenue' => $estimatedFeeBreakdown['platform_revenue'] * 30 // Rough estimate
            ];

            // Monthly trend (last 6 months)
            $analytics['monthly_trend'] = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthlyFees = Order::where('status', 2)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('platform_fee_amount') ?? 0;

                $analytics['monthly_trend'][] = [
                    'month' => $date->format('M Y'),
                    'fees' => $monthlyFees
                ];
            }

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Error calculating platform fee analytics: ' . $e->getMessage());

            // Return safe defaults
            return [
                'current' => [
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'total_platform_fees' => 0,
                    'avg_order_value' => 100,
                    'avg_platform_fee' => 0
                ],
                'projections' => [
                    'estimated_customer_fee' => 0,
                    'estimated_provider_fee' => 0,
                    'estimated_platform_revenue' => 0,
                    'estimated_monthly_revenue' => 0
                ],
                'monthly_trend' => []
            ];
        }
    }

    /**
     * Get platform fee change history
     */
    private function getPlatformFeeHistory()
    {
        try {
            // This would ideally come from a dedicated fee_changes table
            // For now, we'll return a basic structure based on recent changes
            $history = [];

            // You could extend this to read from actual database logs
            $history[] = [
                'date' => now()->subDays(30),
                'admin' => 'System',
                'action' => 'Initial Setup',
                'old_customer_fee' => '0%',
                'new_customer_fee' => '2.5%',
                'old_provider_fee' => '0%',
                'new_provider_fee' => '5.0%',
                'reason' => 'Platform launch configuration'
            ];

            return $history;
        } catch (\Exception $e) {
            Log::error('Error getting platform fee history: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Log platform fee changes for audit trail
     */
    private function logPlatformFeeChange($oldSettings, $newSettings, $reason = null)
    {
        try {
            $logData = [
                'admin_id' => Auth::guard('admin')->id(),
                'old_settings' => $oldSettings,
                'new_settings' => $newSettings,
                'reason' => $reason,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ];

            // Calculate impact
            $oldBreakdown = $this->calculateFeeBreakdown(100, $oldSettings);
            $newBreakdown = $this->calculateFeeBreakdown(100, $newSettings);

            $logData['impact_analysis'] = [
                'old_platform_revenue_per_100' => $oldBreakdown['platform_revenue'],
                'new_platform_revenue_per_100' => $newBreakdown['platform_revenue'],
                'revenue_change_percentage' => $oldBreakdown['platform_revenue'] > 0
                    ? (($newBreakdown['platform_revenue'] - $oldBreakdown['platform_revenue']) / $oldBreakdown['platform_revenue']) * 100
                    : 0
            ];

            Log::info('Platform fees updated', $logData);

            // Store in database if you have an admin_activity_logs table
            // AdminActivityLog::create($logData);

        } catch (\Exception $e) {
            Log::error('Error logging platform fee change: ' . $e->getMessage());
        }
    }

    // ===============================
    // DEFAULT IMAGES MANAGEMENT METHODS
    // ===============================

    /**
     * Get default images management page
     */
    public function get_default_images(Request $request)
    {
        try {
            // Get current default images settings
            $customerImage = AppSetting::where('key', 'customer-image')->first();
            $providerImage = AppSetting::where('key', 'provider-image')->first();
            $customerImageUpdated = AppSetting::where('key', 'customer-image-updated')->first();
            $providerImageUpdated = AppSetting::where('key', 'provider-image-updated')->first();

            $settings = [
                'customer_image' => $customerImage ? $customerImage->value : 'default-customer.png',
                'provider_image' => $providerImage ? $providerImage->value : 'default-provider.png',
                'customer_image_updated' => $customerImageUpdated ? $customerImageUpdated->value : null,
                'provider_image_updated' => $providerImageUpdated ? $providerImageUpdated->value : null,
            ];

            // Get image statistics
            $analytics = $this->getDefaultImageAnalytics($settings);

            // Get image usage history
            $history = $this->getImageChangeHistory();

            return view('admin.app-config.default-images.index', compact('settings', 'analytics', 'history'));
        } catch (\Exception $e) {
            Log::error('Error fetching default images: ' . $e->getMessage());

            return view('admin.app-config.default-images.index', [
                'settings' => [
                    'customer_image' => 'default-customer.png',
                    'provider_image' => 'default-provider.png',
                    'customer_image_updated' => null,
                    'provider_image_updated' => null,
                ],
                'analytics' => $this->getDefaultImageAnalytics([]),
                'history' => [],
                'error' => 'Unable to load default images settings.'
            ]);
        }
    }

    /**
     * Update default images
     */
    public function update_default_images(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'provider_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'reason' => 'nullable|string|max:500'
            ], [
                'customer_image.image' => 'Customer image must be a valid image file.',
                'provider_image.image' => 'Provider image must be a valid image file.',
                'customer_image.max' => 'Customer image must not exceed 2MB.',
                'provider_image.max' => 'Provider image must not exceed 2MB.',
                'customer_image.mimes' => 'Customer image must be: jpeg, png, jpg, gif, or webp.',
                'provider_image.mimes' => 'Provider image must be: jpeg, png, jpg, gif, or webp.',
            ]);

            $changes = [];
            $oldSettings = [];

            // Handle customer image upload
            if ($request->hasFile('customer_image')) {
                $oldCustomerImage = AppSetting::where('key', 'customer-image')->first();
                $oldSettings['customer_image'] = $oldCustomerImage ? $oldCustomerImage->value : null;

                $file = $request->file('customer_image');
                $filename = 'customer_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in public/storage/images directory
                $file->storeAs('public/images', $filename);

                // Update setting
                $this->edit_setting('customer-image', $filename);
                $this->edit_setting('customer-image-updated', now()->toDateTimeString());

                $changes['customer_image'] = $filename;

                // Delete old image if it's not a default
                if (
                    $oldSettings['customer_image'] &&
                    !in_array($oldSettings['customer_image'], ['default-customer.png', 'customer-default.png'])
                ) {
                    $this->deleteOldImage($oldSettings['customer_image']);
                }
            }

            // Handle provider image upload
            if ($request->hasFile('provider_image')) {
                $oldProviderImage = AppSetting::where('key', 'provider-image')->first();
                $oldSettings['provider_image'] = $oldProviderImage ? $oldProviderImage->value : null;

                $file = $request->file('provider_image');
                $filename = 'provider_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store in public/storage/images directory
                $file->storeAs('public/images', $filename);

                // Update setting
                $this->edit_setting('provider-image', $filename);
                $this->edit_setting('provider-image-updated', now()->toDateTimeString());

                $changes['provider_image'] = $filename;

                // Delete old image if it's not a default
                if (
                    $oldSettings['provider_image'] &&
                    !in_array($oldSettings['provider_image'], ['default-provider.png', 'provider-default.png'])
                ) {
                    $this->deleteOldImage($oldSettings['provider_image']);
                }
            }

            if (empty($changes)) {
                return redirect()->back()
                    ->withErrors(['error' => 'No images were uploaded. Please select at least one image to update.'])
                    ->withInput();
            }

            // Log the change
            $this->logImageChange($oldSettings, $changes, $validated['reason'] ?? null);

            $message = 'Default images updated successfully. ';
            if (isset($changes['customer_image'])) $message .= 'Customer image updated. ';
            if (isset($changes['provider_image'])) $message .= 'Provider image updated. ';
            $message .= 'Changes will apply to new users immediately.';

            return redirect()->route('admin_get_default_images')
                ->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating default images: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to update default images. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Reset default images to system defaults
     */
    public function reset_default_images(Request $request)
    {
        try {
            $validated = $request->validate([
                'image_type' => 'required|in:customer,provider,both',
                'reason' => 'nullable|string|max:500'
            ]);

            $oldSettings = [];
            $changes = [];

            if ($validated['image_type'] === 'customer' || $validated['image_type'] === 'both') {
                $oldCustomerImage = AppSetting::where('key', 'customer-image')->first();
                $oldSettings['customer_image'] = $oldCustomerImage ? $oldCustomerImage->value : null;

                $this->edit_setting('customer-image', 'default-customer.png');
                $this->edit_setting('customer-image-updated', now()->toDateTimeString());
                $changes['customer_image'] = 'default-customer.png';

                // Delete old custom image
                if (
                    $oldSettings['customer_image'] &&
                    !in_array($oldSettings['customer_image'], ['default-customer.png', 'customer-default.png'])
                ) {
                    $this->deleteOldImage($oldSettings['customer_image']);
                }
            }

            if ($validated['image_type'] === 'provider' || $validated['image_type'] === 'both') {
                $oldProviderImage = AppSetting::where('key', 'provider-image')->first();
                $oldSettings['provider_image'] = $oldProviderImage ? $oldProviderImage->value : null;

                $this->edit_setting('provider-image', 'default-provider.png');
                $this->edit_setting('provider-image-updated', now()->toDateTimeString());
                $changes['provider_image'] = 'default-provider.png';

                // Delete old custom image
                if (
                    $oldSettings['provider_image'] &&
                    !in_array($oldSettings['provider_image'], ['default-provider.png', 'provider-default.png'])
                ) {
                    $this->deleteOldImage($oldSettings['provider_image']);
                }
            }

            // Log the reset
            $this->logImageChange($oldSettings, $changes, $validated['reason'] ?? 'Reset to system defaults');

            $message = 'Default images reset successfully. ';
            if ($validated['image_type'] === 'both') {
                $message .= 'Both customer and provider images reset to system defaults.';
            } else {
                $message .= ucfirst($validated['image_type']) . ' image reset to system default.';
            }

            return redirect()->route('admin_get_default_images')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error resetting default images: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Unable to reset default images. Please try again.']);
        }
    }

    /**
     * Get image preview for upload validation
     */
    public function preview_image(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'type' => 'required|in:customer,provider'
            ]);

            $file = $request->file('image');
            $filename = 'preview_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Store temporarily
            $path = $file->storeAs('public/temp', $filename);

            return response()->json([
                'success' => true,
                'preview_url' => Storage::url('temp/' . $filename),
                'filename' => $filename,
                'size' => $this->formatFileSize($file->getSize()),
                'dimensions' => $this->getImageDimensions($file)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to process image: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get default image analytics
     */
    private function getDefaultImageAnalytics($settings)
    {
        try {
            $analytics = [];

            // Count users using default images
            $totalCustomers = User::where('type', 'customer')->count();
            $totalProviders = User::where('type', 'worker')->count();

            // Count users with custom images
            $customersWithCustomImages = User::where('type', 'customer')
                ->whereNotNull('picture')
                ->where('picture', '!=', '')
                ->where('picture', '!=', $settings['customer_image'] ?? 'default-customer.png')
                ->count();

            $providersWithCustomImages = User::where('type', 'worker')
                ->whereNotNull('picture')
                ->where('picture', '!=', '')
                ->where('picture', '!=', $settings['provider_image'] ?? 'default-provider.png')
                ->count();

            $analytics['usage'] = [
                'total_customers' => $totalCustomers,
                'total_providers' => $totalProviders,
                'customers_with_custom_images' => $customersWithCustomImages,
                'providers_with_custom_images' => $providersWithCustomImages,
                'customers_using_default' => $totalCustomers - $customersWithCustomImages,
                'providers_using_default' => $totalProviders - $providersWithCustomImages,
                'customer_custom_percentage' => $totalCustomers > 0 ? ($customersWithCustomImages / $totalCustomers) * 100 : 0,
                'provider_custom_percentage' => $totalProviders > 0 ? ($providersWithCustomImages / $totalProviders) * 100 : 0,
            ];

            // Recent registrations using default images
            $analytics['recent_usage'] = [
                'customers_last_30_days' => User::where('type', 'customer')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'providers_last_30_days' => User::where('type', 'worker')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
            ];

            // Image file information
            $analytics['file_info'] = [
                'customer_image_exists' => $this->imageExists($settings['customer_image'] ?? ''),
                'provider_image_exists' => $this->imageExists($settings['provider_image'] ?? ''),
                'customer_image_size' => $this->getImageFileSize($settings['customer_image'] ?? ''),
                'provider_image_size' => $this->getImageFileSize($settings['provider_image'] ?? ''),
            ];

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Error calculating image analytics: ' . $e->getMessage());

            return [
                'usage' => [
                    'total_customers' => 0,
                    'total_providers' => 0,
                    'customers_with_custom_images' => 0,
                    'providers_with_custom_images' => 0,
                    'customers_using_default' => 0,
                    'providers_using_default' => 0,
                    'customer_custom_percentage' => 0,
                    'provider_custom_percentage' => 0,
                ],
                'recent_usage' => [
                    'customers_last_30_days' => 0,
                    'providers_last_30_days' => 0,
                ],
                'file_info' => [
                    'customer_image_exists' => false,
                    'provider_image_exists' => false,
                    'customer_image_size' => 0,
                    'provider_image_size' => 0,
                ]
            ];
        }
    }

    /**
     * Helper method to delete old images
     */
    private function deleteOldImage($filename)
    {
        try {
            $imagePaths = [
                storage_path('app/public/images/' . $filename),
                public_path('storage/images/' . $filename)
            ];

            foreach ($imagePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                    Log::info('Deleted old image: ' . $path);
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error deleting old image: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to check if image exists
     */
    private function imageExists($filename)
    {
        if (!$filename) return false;

        $paths = [
            storage_path('app/public/images/' . $filename),
            public_path('storage/images/' . $filename),
            public_path('images/' . $filename)
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper method to get image file size
     */
    private function getImageFileSize($filename)
    {
        if (!$filename) return 0;

        $paths = [
            storage_path('app/public/images/' . $filename),
            public_path('storage/images/' . $filename),
            public_path('images/' . $filename)
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return filesize($path);
            }
        }

        return 0;
    }

    /**
     * Helper method to format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Helper method to get image dimensions
     */
    private function getImageDimensions($file)
    {
        try {
            $imageInfo = getimagesize($file->getPathname());
            return [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'ratio' => round($imageInfo[0] / $imageInfo[1], 2)
            ];
        } catch (\Exception $e) {
            return ['width' => 0, 'height' => 0, 'ratio' => 0];
        }
    }

    /**
     * Log image changes for audit trail
     */
    private function logImageChange($oldSettings, $newSettings, $reason = null)
    {
        try {
            Log::info('Default images updated', [
                'admin_id' => Auth::guard('admin')->id(),
                'old_settings' => $oldSettings,
                'new_settings' => $newSettings,
                'reason' => $reason,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging image change: ' . $e->getMessage());
        }
    }

    /**
     * Get image change history
     */
    private function getImageChangeHistory()
    {
        // This would ideally come from a dedicated image_changes table
        // For now, return basic structure
        return [
            [
                'date' => now()->subDays(7),
                'admin' => 'System',
                'action' => 'Initial Setup',
                'changes' => 'Set default images for customer and provider profiles',
                'reason' => 'Platform initialization'
            ]
        ];
    }
}
