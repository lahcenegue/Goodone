@extends('admin.layouts')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="card-title text-white mb-2">
                                <i class="bx bx-trending-up me-2"></i>
                                Platform Performance Analytics
                            </h3>
                            <p class="mb-0 opacity-75">Complete overview of your Goodone platform's performance and financial health</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="text-white opacity-75">
                                <small>Last Updated</small><br>
                                <strong>{{ now()->format('M d, Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Platform Statistics -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="fw-bold mb-3">
                <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i>
                Overall Platform Statistics
            </h4>
        </div>

        <!-- Total Users -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-primary rounded-circle">
                            <i class="bx bx-user-plus bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-primary">{{ number_format($totalUsers) }}</h3>
                    <p class="mb-0 text-muted small">Total Customers</p>
                </div>
            </div>
        </div>

        <!-- Total Providers -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-success rounded-circle">
                            <i class="bx bx-briefcase bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-success">{{ number_format($totalProviders) }}</h3>
                    <p class="mb-0 text-muted small">Service Providers</p>
                </div>
            </div>
        </div>

        <!-- Total Services -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-info rounded-circle">
                            <i class="bx bx-grid-alt bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-info">{{ number_format($totalServices) }}</h3>
                    <p class="mb-0 text-muted small">
                        Total Services
                        <br><small class="text-success">{{ number_format($activeServices) }} Active</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-warning rounded-circle">
                            <i class="bx bx-check-circle bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-warning">{{ number_format($totalCompletedOrders) }}</h3>
                    <p class="mb-0 text-muted small">Completed Orders</p>
                </div>
            </div>
        </div>

        <!-- Platform Revenue -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-danger rounded-circle">
                            <i class="bx bx-dollar-circle bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-danger">${{ number_format($totalPlatformRevenue, 2) }}</h3>
                    <p class="mb-0 text-muted small">Platform Earnings</p>
                </div>
            </div>
        </div>

        <!-- Total Volume -->
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial bg-label-dark rounded-circle">
                            <i class="bx bx-trending-up bx-lg"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-dark">${{ number_format($totalCustomerPayments, 2) }}</h3>
                    <p class="mb-0 text-muted small">Total Volume</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Breakdown -->
    <div class="row mb-5">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-pie-chart-alt text-primary me-2"></i>
                        Financial Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Customer Payments -->
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial bg-primary rounded">
                                            <i class="bx bx-wallet"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">${{ number_format($totalCustomerPayments, 2) }}</h6>
                                    <small class="text-muted">Total Customer Payments</small>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Earnings -->
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial bg-success rounded">
                                            <i class="bx bx-dollar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">${{ number_format($totalPlatformRevenue, 2) }}</h6>
                                    <small class="text-muted">Platform Earnings</small>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ $totalCustomerPayments > 0 ? ($totalPlatformRevenue / $totalCustomerPayments) * 100 : 0 }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Provider Earnings -->
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        <div class="avatar-initial bg-info rounded">
                                            <i class="bx bx-money"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">${{ number_format($totalProviderEarnings, 2) }}</h6>
                                    <small class="text-muted">Provider Earnings</small>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-info" style="width: {{ $totalCustomerPayments > 0 ? ($totalProviderEarnings / $totalCustomerPayments) * 100 : 0 }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Composition -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="mb-3">Platform Revenue Composition</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Customer Fees:</span>
                                    <span class="fw-semibold">${{ number_format($customerFees, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Provider Commissions:</span>
                                    <span class="fw-semibold">${{ number_format($providerCommissions, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-target-lock text-success me-2"></i>
                        Key Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Average Order Value -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">${{ number_format($averageOrderValue, 2) }}</h6>
                            <small class="text-muted">Average Order Value</small>
                        </div>
                        <i class="bx bx-trending-up text-success"></i>
                    </div>

                    <!-- Platform Take Rate -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ number_format($platformTakeRate, 1) }}%</h6>
                            <small class="text-muted">Platform Take Rate</small>
                        </div>
                        <i class="bx bx-pie-chart text-primary"></i>
                    </div>

                    <!-- Service Completion Rate -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">{{ number_format($completionRate, 1) }}%</h6>
                            <small class="text-muted">Order Completion Rate</small>
                        </div>
                        <i class="bx bx-check-shield text-success"></i>
                    </div>

                    <!-- Provider Utilization -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ number_format($providerUtilization, 1) }}%</h6>
                            <small class="text-muted">Service Utilization</small>
                        </div>
                        <i class="bx bx-bar-chart-alt text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Health Indicators -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-health text-success me-2"></i>
                        Platform Health Indicators
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Active vs Inactive Services -->
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="progress mx-auto mb-2" style="height: 6px; width: 80%;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalServices > 0 ? ($activeServices / $totalServices) * 100 : 0 }}%;"></div>
                                </div>
                                <h6 class="mb-0">{{ number_format(($totalServices > 0 ? ($activeServices / $totalServices) * 100 : 0), 1) }}%</h6>
                                <small class="text-muted">Services Active</small>
                            </div>
                        </div>

                        <!-- Pending Withdrawals Alert -->
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                @if($pendingWithdrawals > 0)
                                <div class="badge bg-warning mb-2">{{ $pendingWithdrawals }} Pending</div>
                                @else
                                <div class="badge bg-success mb-2">All Clear</div>
                                @endif
                                <h6 class="mb-0">Withdrawals</h6>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>

                        <!-- Growth Trend -->
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="bx bx-trending-up bx-lg text-success mb-2"></i>
                                <h6 class="mb-0">Growing</h6>
                                <small class="text-muted">Platform Trend</small>
                            </div>
                        </div>

                        <!-- System Status -->
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="bx bx-check-circle bx-lg text-success mb-2"></i>
                                <h6 class="mb-0">Operational</h6>
                                <small class="text-muted">System Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card {
        transition: transform 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .avatar-lg {
        width: 3.5rem;
        height: 3.5rem;
    }

    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .progress {
        background-color: rgba(67, 89, 113, 0.1);
    }

    .card {
        border-radius: 0.75rem;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endsection