@extends('admin.layouts')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back! Here\'s what\'s happening with your Goodone platform today.')

@section('content')
<style>
    /* Modern Dashboard Styles */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stats-icon.primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .stats-icon.success {
        background: linear-gradient(135deg, #4ade80, #22c55e);
    }

    .stats-icon.warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }

    .stats-icon.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stats-icon.info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }

    .stats-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stats-trend {
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

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .chart-header {
        display: flex;
        justify-content: between;
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

    /* Recent Activity */
    .activity-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #f7fafc;
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background-color: #f8fafc;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1rem;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #4a5568;
        transition: all 0.3s ease;
        text-align: center;
    }

    .action-btn:hover {
        border-color: #667eea;
        background: #f8fafc;
        transform: translateY(-1px);
        text-decoration: none;
        color: #667eea;
    }

    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f7fafc, #edf2f7);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        color: #667eea;
    }

    .action-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .action-subtitle {
        font-size: 0.875rem;
        color: #718096;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Status badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-success {
        background: #dcfce7;
        color: #166534;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-info {
        background: #dbeafe;
        color: #1e40af;
    }
</style>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="chart-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    Welcome back, Administrator! ??
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Here's what's happening with your Goodone platform today.
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.875rem; opacity: 0.8;">{{ now()->format('l, F j, Y') }}</div>
                <div style="font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">{{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <!-- Users Today -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <div class="stats-value">{{ $day_stats['users'] ?? 0 }}</div>
                    <div class="stats-label">New Customers Today</div>
                </div>
                <div class="stats-icon primary">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            @if(isset($day_stats['users_difference']))
            <div class="stats-trend {{ $day_stats['users_difference'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas {{ $day_stats['users_difference'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                <span style="margin-left: 0.25rem;">{{ number_format(abs($day_stats['users_difference']), 1) }}% vs yesterday</span>
            </div>
            @endif
        </div>

        <!-- Services Today -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <div class="stats-value">{{ $day_stats['services'] ?? 0 }}</div>
                    <div class="stats-label">New Services Today</div>
                </div>
                <div class="stats-icon success">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            @if(isset($day_stats['services_difference']))
            <div class="stats-trend {{ $day_stats['services_difference'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas {{ $day_stats['services_difference'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                <span style="margin-left: 0.25rem;">{{ number_format(abs($day_stats['services_difference']), 1) }}% vs yesterday</span>
            </div>
            @endif
        </div>

        <!-- Orders Today -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <div class="stats-value">{{ $day_stats['completed_orders'] ?? 0 }}</div>
                    <div class="stats-label">Completed Orders Today</div>
                </div>
                <div class="stats-icon info">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stats-trend trend-neutral">
                <i class="fas fa-shopping-cart"></i>
                <span style="margin-left: 0.25rem;">{{ $day_stats['orders'] ?? 0 }} total orders today</span>
            </div>
        </div>

        <!-- Revenue Today -->
        <div class="stats-card">
            <div class="stats-header">
                <div>
                    <div class="stats-value">${{ number_format($day_stats['revenue'] ?? 0, 2) }}</div>
                    <div class="stats-label">Platform Earnings Today</div>
                </div>
                <div class="stats-icon warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            @if(isset($day_stats['revenue_difference']))
            <div class="stats-trend {{ $day_stats['revenue_difference'] >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas {{ $day_stats['revenue_difference'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                <span style="margin-left: 0.25rem;">{{ number_format(abs($day_stats['revenue_difference']), 1) }}% vs yesterday</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h3 class="chart-title">Quick Actions</h3>
                <p class="chart-subtitle">Manage your platform efficiently</p>
            </div>
        </div>
        <div class="quick-actions">
            <a href="{{ route('admin_get_customers') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-title">Manage Customers</div>
                <div class="action-subtitle">View and manage all customers</div>
            </a>
            <a href="{{ route('admin_platform_statistics') }}" class="action-btn">
                <div class="action-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-title">Analytics</div>
                <div class="action-subtitle">View platform statistics</div>
            </a>
            <a href="#" class="action-btn" onclick="alert('Service management coming soon!')" style="opacity: 0.6;">
                <div class="action-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="action-title">All Services</div>
                <div class="action-subtitle">Coming soon</div>
            </a>
            <a href="#" class="action-btn" onclick="alert('Order management coming soon!')" style="opacity: 0.6;">
                <div class="action-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="action-title">Orders</div>
                <div class="action-subtitle">Coming soon</div>
            </a>
            <a href="#" class="action-btn" onclick="alert('Withdrawal management coming soon!')" style="opacity: 0.6;">
                <div class="action-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="action-title">Withdrawals</div>
                <div class="action-subtitle">Coming soon</div>
            </a>
            <a href="#" class="action-btn" onclick="alert('Settings coming soon!')" style="opacity: 0.6;">
                <div class="action-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="action-title">Settings</div>
                <div class="action-subtitle">Coming soon</div>
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Recent Orders -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Recent Orders</h3>
                    <p class="chart-subtitle">Latest customer orders</p>
                </div>
                <a href="#" onclick="alert('Order management coming soon!')" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 0.875rem; opacity: 0.6;">
                    Coming Soon ?
                </a>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                @if($recentOrders->count() > 0)
                @foreach($recentOrders as $order)
                <div class="activity-item">
                    <div class="activity-icon success">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Order #{{ $order->id }}</div>
                        <div style="color: #718096; font-size: 0.875rem; margin-bottom: 0.25rem;">
                            {{ $order->full_name ?? 'Customer' }}
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="status-badge status-{{ $order->status == 2 ? 'success' : ($order->status == 1 ? 'warning' : 'danger') }}">
                                @if($order->status == 2) Completed @elseif($order->status == 1) Pending @else Cancelled @endif
                            </span>
                            <span style="font-weight: 600; color: #2d3748;">${{ number_format($order->price, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div style="text-align: center; padding: 2rem; color: #718096;">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No orders yet</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Platform Overview -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Platform Overview</h3>
                    <p class="chart-subtitle">Key platform metrics</p>
                </div>
            </div>
            <div style="space-y: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f7fafc;">
                    <div>
                        <div style="font-weight: 600; color: #2d3748;">Total Customers</div>
                        <div style="color: #718096; font-size: 0.875rem;">All registered customers</div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">
                        {{ number_format($totalCustomers) }}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f7fafc;">
                    <div>
                        <div style="font-weight: 600; color: #2d3748;">Service Providers</div>
                        <div style="color: #718096; font-size: 0.875rem;">Active service providers</div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #22c55e;">
                        {{ number_format($totalProviders) }}
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #f7fafc;">
                    <div>
                        <div style="font-weight: 600; color: #2d3748;">Active Services</div>
                        <div style="color: #718096; font-size: 0.875rem;">{{ number_format($totalServices) }} total services</div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">
                        {{ number_format($activeServices) }}
                    </div>
                </div>
                @if($pendingWithdrawals > 0)
                <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; color: #92400e;">Pending Withdrawals</div>
                            <div style="color: #92400e; font-size: 0.875rem;">{{ $pendingWithdrawals }} requests waiting</div>
                        </div>
                        <a href="#" onclick="alert('Withdrawal management coming soon!')" style="background: #f59e0b; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.875rem; opacity: 0.6;">
                            Coming Soon
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h3 class="chart-title">Monthly Performance</h3>
                <p class="chart-subtitle">Current month statistics</p>
            </div>
        </div>
        <div class="dashboard-grid">
            <div style="text-align: center; padding: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #667eea; margin-bottom: 0.5rem;">
                    {{ $month_stats['users'] ?? 0 }}
                </div>
                <div style="color: #718096;">New Customers</div>
            </div>
            <div style="text-align: center; padding: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #22c55e; margin-bottom: 0.5rem;">
                    {{ $month_stats['services'] ?? 0 }}
                </div>
                <div style="color: #718096;">New Services</div>
            </div>
            <div style="text-align: center; padding: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
                    {{ $month_stats['orders'] ?? 0 }}
                </div>
                <div style="color: #718096;">Total Orders</div>
            </div>
            <div style="text-align: center; padding: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-bottom: 0.5rem;">
                    ${{ number_format($month_stats['revenue'] ?? 0, 0) }}
                </div>
                <div style="color: #718096;">Revenue</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Animate counters on page load
        const animateCounter = (element, target, duration = 2000, isDecimal = false) => {
            let current = 0;
            const increment = target / (duration / 16);
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }

                if (isDecimal) {
                    // For monetary values, format with 2 decimal places
                    element.textContent = '$' + current.toFixed(2);
                } else {
                    // For whole numbers, use integer formatting
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        };

        // Animate all stat values
        document.querySelectorAll('.stats-value').forEach(element => {
            const originalText = element.textContent;

            // Check if this is a monetary value (contains $)
            if (originalText.includes('$')) {
                // Extract the numeric value properly for monetary amounts
                const target = parseFloat(originalText.replace(/[$,]/g, ''));
                if (target > 0) {
                    element.textContent = '$0.00';
                    setTimeout(() => animateCounter(element, target, 2000, true), 500);
                }
            } else {
                // Handle regular numbers (users, services, orders)
                const target = parseInt(originalText.replace(/[^0-9]/g, ''));
                if (target > 0) {
                    element.textContent = '0';
                    setTimeout(() => animateCounter(element, target, 2000, false), 500);
                }
            }
        });
    });
</script>
@endpush