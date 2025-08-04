@extends('admin.layouts')

@section('page-title', 'Platform Analytics')
@section('page-subtitle', 'Comprehensive insights into your platform\'s performance and growth.')

@section('content')
<style>
    /* Modern Analytics Styles - matches dashboard design */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .analytics-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .kpi-icon.primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .kpi-icon.success {
        background: linear-gradient(135deg, #4ade80, #22c55e);
    }

    .kpi-icon.warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }

    .kpi-icon.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .kpi-icon.info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }

    .kpi-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .kpi-trend {
        display: flex;
        align-items: center;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    .trend-up {
        color: #22c55e;
    }

    .trend-down {
        color: #ef4444;
    }

    .trend-neutral {
        color: #718096;
    }

    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .chart-subtitle {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .chart-canvas {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .insight-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f7fafc;
    }

    .insight-item:last-child {
        border-bottom: none;
    }

    .insight-label {
        color: #4a5568;
        font-weight: 500;
    }

    .insight-value {
        color: #2d3748;
        font-weight: 600;
    }

    /* Date range selector */
    .date-range-selector {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .date-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .date-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #4a5568;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .date-btn:hover {
        border-color: #667eea;
        background: #f8fafc;
        color: #667eea;
        text-decoration: none;
    }

    .date-btn.active {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .insights-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Analytics Content -->
<div class="analytics-container">
    <!-- Welcome Banner -->
    <div class="chart-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="bx bx-trending-up me-2"></i>
                    Platform Analytics Dashboard
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Comprehensive insights into your platform's performance and growth
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.875rem; opacity: 0.8;">
                    {{ isset($meta) ? $meta['start_date'] : 'N/A' }} to {{ isset($meta) ? $meta['end_date'] : 'N/A' }}
                </div>
                <div style="font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">
                    {{ isset($meta) ? $meta['last_updated'] : now()->format('M d, Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="date-range-selector">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="margin: 0; color: #2d3748; font-size: 1.1rem; font-weight: 600;">
                    Select Date Range
                </h3>
                <p style="margin: 0.25rem 0 0 0; color: #718096; font-size: 0.875rem;">
                    Choose a period to analyze your platform's performance
                </p>
            </div>
            <div class="date-buttons">
                <a href="?date_range=7" class="date-btn {{ request('date_range', 90) == 7 ? 'active' : '' }}">
                    Last 7 Days
                </a>
                <a href="?date_range=30" class="date-btn {{ request('date_range', 90) == 30 ? 'active' : '' }}">
                    Last 30 Days
                </a>
                <a href="?date_range=90" class="date-btn {{ request('date_range', 90) == 90 ? 'active' : '' }}">
                    Last 90 Days
                </a>
                <a href="?date_range=365" class="date-btn {{ request('date_range', 90) == 365 ? 'active' : '' }}">
                    Last Year
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    @if(isset($kpis) && !empty($kpis))
    <div class="analytics-grid">
        <!-- Total Orders -->
        @if(isset($kpis['total_orders']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">{{ number_format($kpis['total_orders']['value']) }}</div>
                    <div class="kpi-label">Total Orders</div>
                </div>
                <div class="kpi-icon {{ $kpis['total_orders']['color'] }}">
                    <i class="bx {{ $kpis['total_orders']['icon'] }}"></i>
                </div>
            </div>
            @if(isset($kpis['total_orders']['growth']))
            <div class="kpi-trend {{ $kpis['total_orders']['growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="bx {{ $kpis['total_orders']['growth'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                <span style="margin-left: 0.25rem;">
                    {{ abs($kpis['total_orders']['growth']) }}% vs previous period
                </span>
            </div>
            @endif
            <div style="color: #718096; font-size: 0.8rem; margin-top: 0.5rem;">
                {{ $kpis['total_orders']['period_value'] ?? 0 }} orders this period
            </div>
        </div>
        @endif

        <!-- Total Users -->
        @if(isset($kpis['total_users']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">{{ number_format($kpis['total_users']['value']) }}</div>
                    <div class="kpi-label">Total Users</div>
                </div>
                <div class="kpi-icon {{ $kpis['total_users']['color'] }}">
                    <i class="bx {{ $kpis['total_users']['icon'] }}"></i>
                </div>
            </div>
            @if(isset($kpis['total_users']['breakdown']))
            <div style="color: #718096; font-size: 0.8rem;">
                {{ number_format($kpis['total_users']['breakdown']['customers']) }} customers,
                {{ number_format($kpis['total_users']['breakdown']['providers']) }} providers
            </div>
            @endif
            @if(isset($kpis['total_users']['growth']))
            <div class="kpi-trend {{ $kpis['total_users']['growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="bx {{ $kpis['total_users']['growth'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                <span style="margin-left: 0.25rem;">
                    {{ abs($kpis['total_users']['growth']) }}% vs previous period
                </span>
            </div>
            @endif
        </div>
        @endif

        <!-- Total Services -->
        @if(isset($kpis['total_services']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">{{ number_format($kpis['total_services']['value']) }}</div>
                    <div class="kpi-label">Total Services</div>
                </div>
                <div class="kpi-icon {{ $kpis['total_services']['color'] }}">
                    <i class="bx {{ $kpis['total_services']['icon'] }}"></i>
                </div>
            </div>
            <div style="color: #22c55e; font-size: 0.8rem;">
                {{ $kpis['total_services']['utilization'] }}% active
                ({{ $kpis['total_services']['active'] }} of {{ $kpis['total_services']['value'] }})
            </div>
            @if(isset($kpis['total_services']['growth']))
            <div class="kpi-trend {{ $kpis['total_services']['growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="bx {{ $kpis['total_services']['growth'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                <span style="margin-left: 0.25rem;">
                    {{ abs($kpis['total_services']['growth']) }}% vs previous period
                </span>
            </div>
            @endif
        </div>
        @endif

        <!-- Total Revenue -->
        @if(isset($kpis['total_revenue']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">${{ number_format($kpis['total_revenue']['value'], 2) }}</div>
                    <div class="kpi-label">Total Revenue</div>
                </div>
                <div class="kpi-icon {{ $kpis['total_revenue']['color'] }}">
                    <i class="bx {{ $kpis['total_revenue']['icon'] }}"></i>
                </div>
            </div>
            <div style="color: #718096; font-size: 0.8rem;">
                Platform: ${{ number_format($kpis['total_revenue']['platform_revenue'], 2) }}
            </div>
            @if(isset($kpis['total_revenue']['growth']))
            <div class="kpi-trend {{ $kpis['total_revenue']['growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="bx {{ $kpis['total_revenue']['growth'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                <span style="margin-left: 0.25rem;">
                    {{ abs($kpis['total_revenue']['growth']) }}% vs previous period
                </span>
            </div>
            @endif
        </div>
        @endif

        <!-- Platform Earnings -->
        @if(isset($kpis['platform_earnings']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">${{ number_format($kpis['platform_earnings']['value'], 2) }}</div>
                    <div class="kpi-label">Platform Earnings</div>
                </div>
                <div class="kpi-icon {{ $kpis['platform_earnings']['color'] }}">
                    <i class="bx {{ $kpis['platform_earnings']['icon'] }}"></i>
                </div>
            </div>
            <div style="color: #22c55e; font-size: 0.8rem;">
                {{ $kpis['platform_earnings']['profit_margin'] }}% profit margin
            </div>
            @if(isset($kpis['platform_earnings']['growth']))
            <div class="kpi-trend {{ $kpis['platform_earnings']['growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="bx {{ $kpis['platform_earnings']['growth'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                <span style="margin-left: 0.25rem;">
                    {{ abs($kpis['platform_earnings']['growth']) }}% vs previous period
                </span>
            </div>
            @endif
        </div>
        @endif

        <!-- Pending Payouts -->
        @if(isset($kpis['pending_payouts']))
        <div class="analytics-card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value">${{ number_format($kpis['pending_payouts']['value'], 2) }}</div>
                    <div class="kpi-label">Pending Payouts</div>
                </div>
                <div class="kpi-icon {{ $kpis['pending_payouts']['color'] }}">
                    <i class="bx {{ $kpis['pending_payouts']['icon'] }}"></i>
                </div>
            </div>
            <div style="color: #718096; font-size: 0.8rem;">
                {{ $kpis['pending_payouts']['count'] }} withdrawal requests
            </div>
            @if($kpis['pending_payouts']['count'] > 0)
            <div style="margin-top: 0.5rem;">
                <a href="# onclick="alert('Withdrawal management coming soon!')" style="opacity: 0.6;"" style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem;">
                    Review Requests
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- Charts Section -->
    @if(isset($charts))
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Orders & Revenue Trends -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Orders & Revenue Trends</h3>
                    <p class="chart-subtitle">Track your platform's growth over time</p>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="ordersRevenueChart"></canvas>
            </div>
        </div>

        <!-- User Registrations -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">User Registrations</h3>
                    <p class="chart-subtitle">New customers and providers</p>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="userRegistrationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Charts -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Orders by Category -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Orders by Category</h3>
                    <p class="chart-subtitle">Popular service categories</p>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="ordersCategoryChart"></canvas>
            </div>
        </div>

        <!-- Providers by Region -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Providers by Region</h3>
                    <p class="chart-subtitle">Geographic distribution</p>
                </div>
            </div>
            <div class="chart-canvas">
                <canvas id="providersRegionChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Insights Section -->
    @if(isset($insights))
    <div class="insights-grid">
        <!-- Order Insights -->
        @if(isset($insights['order_insights']))
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Order Insights</h3>
                    <p class="chart-subtitle">Performance metrics</p>
                </div>
            </div>
            <div>
                @if(isset($insights['order_insights']['top_services']) && $insights['order_insights']['top_services']->count() > 0)
                <h4 style="color: #4a5568; font-size: 1rem; margin: 0 0 1rem 0;">Top Services</h4>
                @foreach($insights['order_insights']['top_services'] as $service)
                <div class="insight-item">
                    <span class="insight-label">{{ $service->service }}</span>
                    <span class="insight-value">{{ $service->order_count }} orders</span>
                </div>
                @endforeach
                @endif

                <div style="border-top: 1px solid #e2e8f0; margin-top: 1rem; padding-top: 1rem;">
                    <div class="insight-item">
                        <span class="insight-label">Average Order Value</span>
                        <span class="insight-value">${{ number_format($insights['order_insights']['avg_order_value'] ?? 0, 2) }}</span>
                    </div>
                    <div class="insight-item">
                        <span class="insight-label">Avg Completion Time</span>
                        <span class="insight-value">{{ $insights['order_insights']['avg_completion_time'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- User Insights -->
        @if(isset($insights['user_insights']))
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">User Insights</h3>
                    <p class="chart-subtitle">Registration trends</p>
                </div>
            </div>
            <div>
                @if(isset($insights['user_insights']['daily_registrations']) && $insights['user_insights']['daily_registrations']->count() > 0)
                <h4 style="color: #4a5568; font-size: 1rem; margin: 0 0 1rem 0;">Recent Registrations</h4>
                @foreach($insights['user_insights']['daily_registrations']->take(5) as $registration)
                <div class="insight-item">
                    <span class="insight-label">{{ \Carbon\Carbon::parse($registration->date)->format('M d') }}</span>
                    <span class="insight-value">{{ $registration->count }} users</span>
                </div>
                @endforeach
                @endif

                <div style="border-top: 1px solid #e2e8f0; margin-top: 1rem; padding-top: 1rem;">
                    <div class="insight-item">
                        <span class="insight-label">Active Users</span>
                        <span class="insight-value">{{ number_format($insights['user_insights']['active_users'] ?? 0) }}</span>
                    </div>
                    <div class="insight-item">
                        <span class="insight-label">Retention Rate</span>
                        <span class="insight-value">{{ $insights['user_insights']['retention_rate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Financial Overview -->
        @if(isset($insights['financial_overview']))
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Financial Overview</h3>
                    <p class="chart-subtitle">Revenue breakdown</p>
                </div>
            </div>
            <div>
                <div class="insight-item">
                    <span class="insight-label">Period Revenue</span>
                    <span class="insight-value">${{ number_format($insights['financial_overview']['period_revenue'] ?? 0, 2) }}</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Platform Profit</span>
                    <span class="insight-value">${{ number_format($insights['financial_overview']['platform_profit'] ?? 0, 2) }}</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Profit Margin</span>
                    <span class="insight-value">{{ $insights['financial_overview']['profit_margin'] ?? 0 }}%</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Est. Monthly Earnings</span>
                    <span class="insight-value">${{ number_format($insights['financial_overview']['estimated_monthly'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>

<!-- Chart.js and Custom Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Chart.js loaded
        console.log('Chart.js loaded:', typeof Chart !== 'undefined');

        if (typeof Chart === 'undefined') {
            console.error('Chart.js failed to load');
            return;
        }

        // Chart.js default configuration
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = 'rgba(0,0,0,0.04)';

        @if(isset($charts))
        // DEBUG: Log chart data
        console.log('Charts data available:', @json($charts));

        // Orders & Revenue Combined Chart
        const ordersRevenueCtx = document.getElementById('ordersRevenueChart');
        if (ordersRevenueCtx) {
            const ordersData = @json($charts['orders_trend'] ?? ['labels' => [], 'data' => []]);
            const revenueData = @json($charts['revenue_trend'] ?? ['labels' => [], 'data' => []]);

            console.log('Orders data:', ordersData);
            console.log('Revenue data:', revenueData);

            if (ordersData.data && ordersData.data.length > 0) {
                new Chart(ordersRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: ordersData.labels,
                        datasets: [{
                            label: 'Orders',
                            data: ordersData.data,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Revenue ($)',
                            data: revenueData.data,
                            borderColor: '#4ade80',
                            backgroundColor: 'rgba(74, 222, 128, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        }
                    }
                });
            } else {
                ordersRevenueCtx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #9ca3af;">No data available for selected period</div>';
            }
        }

        // User Registration Chart
        const userRegistrationCtx = document.getElementById('userRegistrationChart');
        if (userRegistrationCtx) {
            const usersData = @json($charts['users_trend'] ?? ['labels' => [], 'datasets' => []]);

            if (usersData.labels && usersData.labels.length > 0) {
                new Chart(userRegistrationCtx, {
                    type: 'line',
                    data: {
                        labels: usersData.labels,
                        datasets: usersData.datasets.map(dataset => ({
                            label: dataset.label,
                            data: dataset.data,
                            borderColor: dataset.color,
                            backgroundColor: dataset.color + '20',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                userRegistrationCtx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #9ca3af;">No user registration data available</div>';
            }
        }

        // Orders by Category Chart
        const ordersCategoryCtx = document.getElementById('ordersCategoryChart');
        if (ordersCategoryCtx) {
            const categoryData = @json($charts['orders_by_category'] ?? ['labels' => [], 'data' => []]);

            if (categoryData.labels && categoryData.labels.length > 0 && categoryData.labels[0] !== 'No Data') {
                new Chart(ordersCategoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.labels,
                        datasets: [{
                            data: categoryData.data.map(item => item.value),
                            backgroundColor: categoryData.data.map(item => item.color),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } else {
                ordersCategoryCtx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #9ca3af;">No category data available</div>';
            }
        }

        // Providers by Region Chart
        const providersRegionCtx = document.getElementById('providersRegionChart');
        if (providersRegionCtx) {
            const regionData = @json($charts['providers_by_region'] ?? ['labels' => [], 'data' => []]);

            if (regionData.labels && regionData.labels.length > 0 && regionData.labels[0] !== 'No Data') {
                new Chart(providersRegionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: regionData.labels,
                        datasets: [{
                            data: regionData.data.map(item => item.value),
                            backgroundColor: regionData.data.map(item => item.color),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } else {
                providersRegionCtx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 300px; color: #9ca3af;">No region data available</div>';
            }
        }

        @endif
    });
</script>

@endsection