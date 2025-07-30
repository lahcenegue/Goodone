@extends('admin.layouts')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header with Welcome Message and Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title text-white mb-1">Welcome back, Administrator!</h4>
                            <p class="mb-0">Here's what's happening with your Goodone platform today.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light">
                                    <i class="bx bx-log-out me-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Users Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                New Customers Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $day_stats['users'] ?? 0 }}
                            </div>
                            @if(isset($day_stats['users_difference']))
                            <div class="text-xs {{ $day_stats['users_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="bx {{ $day_stats['users_difference'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                                {{ number_format(abs($day_stats['users_difference']), 1) }}% vs yesterday
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="bx bx-user-plus bx-lg text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin_get_users') }}" class="btn btn-primary btn-sm">View All Customers</a>
                </div>
            </div>
        </div>

        <!-- Services Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                New Services Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $day_stats['services'] ?? 0 }}
                            </div>
                            @if(isset($day_stats['services_difference']))
                            <div class="text-xs {{ $day_stats['services_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="bx {{ $day_stats['services_difference'] >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                                {{ number_format(abs($day_stats['services_difference']), 1) }}% vs yesterday
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="bx bx-briefcase bx-lg text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin_get_services') }}" class="btn btn-success btn-sm">Manage All Services</a>
                </div>
            </div>
        </div>

        <!-- Orders Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Orders Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $day_stats['completed_orders'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">
                                <i class="bx bx-package"></i>
                                {{ $day_stats['orders'] ?? 0 }} total orders today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bx bx-check-circle bx-lg text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin_get_orders') }}" class="btn btn-info btn-sm">View All Orders</a>
                </div>
            </div>
        </div>

        <!-- Platform Revenue Today -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Platform Earnings Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($day_stats['revenue'] ?? 0, 2) }}
                            </div>
                            @if(isset($day_stats['total_order_value']) && $day_stats['total_order_value'] > 0)
                            <div class="text-xs text-muted">
                                <i class="bx bx-info-circle"></i>
                                From ${{ number_format($day_stats['total_order_value'], 2) }} in orders
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="bx bx-dollar-circle bx-lg text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin_get_app_settings') }}" class="btn btn-warning btn-sm">Fee Settings</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin_get_orders') }}" class="btn btn-primary btn-sm">View All Orders</a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">#{{ $order->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-user-circle bx-sm me-2 text-muted"></i>
                                            <div>
                                                <div class="fw-semibold">{{ $order->full_name ?? 'Unknown User' }}</div>
                                                <small class="text-muted">{{ $order->email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success">${{ number_format($order->price, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                        $statusColors = [
                                        0 => ['badge' => 'bg-secondary', 'text' => 'Pending'],
                                        1 => ['badge' => 'bg-warning', 'text' => 'In Progress'],
                                        2 => ['badge' => 'bg-success', 'text' => 'Completed'],
                                        3 => ['badge' => 'bg-danger', 'text' => 'Cancelled']
                                        ];
                                        $status = $statusColors[$order->status] ?? ['badge' => 'bg-secondary', 'text' => 'Unknown'];
                                        @endphp
                                        <span class="badge {{ $status['badge'] }}">{{ $status['text'] }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-package bx-lg text-muted mb-3"></i>
                        <p class="text-muted mb-1">No orders yet</p>
                        <small class="text-muted">Orders will appear here when customers start booking services</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Platform Overview</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Customers</span>
                            <span class="fw-semibold text-primary">{{ number_format($totalCustomers) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $totalCustomers > 0 ? 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Service Providers</span>
                            <span class="fw-semibold text-success">{{ number_format($totalProviders) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalProviders > 0 ? 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Active Services</span>
                            <span class="fw-semibold text-info">{{ number_format($activeServices) }}/{{ number_format($totalServices) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $totalServices > 0 ? ($activeServices / $totalServices) * 100 : 0 }}%;"></div>
                        </div>
                    </div>

                    @if($pendingWithdrawals > 0)
                    <div class="alert alert-warning py-2 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bx bx-info-circle me-1"></i>
                                <strong>{{ $pendingWithdrawals }}</strong> pending withdrawals
                            </div>
                            <a href="{{ route('admin_withdraw_requests') }}" class="btn btn-warning btn-sm">Review</a>
                        </div>
                    </div>
                    @endif

                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="bx bx-time me-1"></i>
                            Last updated: {{ now()->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Management Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_get_users') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bx bx-user bx-lg mb-2"></i>
                                <br><small>Manage<br>Customers</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_get_service_providers') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bx bx-briefcase bx-lg mb-2"></i>
                                <br><small>Service<br>Providers</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_create_category') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bx bx-category bx-lg mb-2"></i>
                                <br><small>Manage<br>Categories</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_create_coupon') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bx bx-gift bx-lg mb-2"></i>
                                <br><small>Create<br>Coupons</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_withdraw_requests') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="bx bx-money-withdraw bx-lg mb-2"></i>
                                <br><small>Withdraw<br>Requests</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('admin_get_app_settings') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bx bx-cog bx-lg mb-2"></i>
                                <br><small>Platform<br>Settings</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }

    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .card-footer {
        border-top: 1px solid #e3e6f0;
    }

    .text-xs {
        font-size: 0.7rem;
    }
</style>
@endsection