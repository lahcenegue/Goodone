@extends('admin.layouts')

@section('page-title', 'Coupons Management')
@section('page-subtitle', 'Create and manage discount coupons for your platform.')

@section('content')
<style>
    /* Coupons Management Styles */
    .coupons-container {
        max-width: 100%;
    }

    .coupons-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .coupons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .coupon-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .coupon-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .coupon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .coupon-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .coupon-code {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        font-family: 'Courier New', monospace;
        background: #f0f4ff;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 2px dashed #667eea;
    }

    .coupon-discount {
        font-size: 2rem;
        font-weight: 800;
        color: #22c55e;
        text-align: center;
        margin: 0.5rem 0;
    }

    .coupon-usage {
        margin-bottom: 1rem;
    }

    .usage-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .usage-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .usage-fill.success {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .usage-fill.warning {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .usage-fill.danger {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    .usage-text {
        font-size: 0.875rem;
        color: #718096;
        display: flex;
        justify-content: space-between;
    }

    .coupon-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .coupon-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

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

    .btn-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8, #553c9a);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #edf2f7;
        color: #2d3748;
        text-decoration: none;
    }

    .btn-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .btn-success:hover {
        background: #bbf7d0;
        color: #14532d;
        text-decoration: none;
    }

    .btn-danger {
        background: #fed7d7;
        color: #c53030;
        border: 1px solid #feb2b2;
    }

    .btn-danger:hover {
        background: #fbb6ce;
        color: #97266d;
        text-decoration: none;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        color: #718096;
        font-size: 0.875rem;
    }

    .search-section {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-select {
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        background: white;
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .coupons-grid {
            grid-template-columns: 1fr;
        }

        .search-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input,
        .filter-select {
            min-width: 100%;
        }

        .coupon-actions {
            justify-content: center;
        }

        .coupon-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="coupons-container">
    <!-- Welcome Banner -->
    <div class="coupons-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-tags me-2"></i>
                    Coupons Management
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Create and manage discount coupons to boost customer engagement
                </p>
            </div>
            <a href="{{ route('admin_create_coupon_form') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-plus"></i>
                Add New Coupon
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($stats))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value" style="color: #667eea;">{{ $stats['total_coupons'] }}</div>
            <div class="stats-label">Total Coupons</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #22c55e;">{{ $stats['active_coupons'] }}</div>
            <div class="stats-label">Active Coupons</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #ef4444;">{{ $stats['expired_coupons'] }}</div>
            <div class="stats-label">Expired Coupons</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #f59e0b;">{{ number_format($stats['total_usage']) }}</div>
            <div class="stats-label">Total Usage</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #8b5cf6;">${{ number_format($stats['total_savings'], 2) }}</div>
            <div class="stats-label">Total Savings</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #06b6d4;">{{ number_format($stats['avg_discount'], 1) }}%</div>
            <div class="stats-label">Avg Discount</div>
        </div>
    </div>
    @endif

    <!-- Search and Filters -->
    <div class="modern-card" style="margin-bottom: 2rem;">
        <div style="padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
                <i class="fas fa-search me-2"></i>
                Search & Filter Coupons
            </h3>
            <form method="GET" action="{{ route('admin_get_coupons') }}">
                <div class="search-section">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search by coupon code or discount..."
                        value="{{ $search ?? '' }}"
                        class="search-input">
                    <select name="status" class="filter-select">
                        <option value="all" {{ ($status ?? '') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ ($status ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="unlimited" {{ ($status ?? '') === 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                    </select>
                    <select name="sort" class="filter-select">
                        <option value="created_at" {{ ($sort ?? '') === 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                        <option value="coupon" {{ ($sort ?? '') === 'coupon' ? 'selected' : '' }}>Sort by Code</option>
                        <option value="percentage" {{ ($sort ?? '') === 'percentage' ? 'selected' : '' }}>Sort by Discount</option>
                        <option value="times_used" {{ ($sort ?? '') === 'times_used' ? 'selected' : '' }}>Sort by Usage</option>
                    </select>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                    @if($search || ($status ?? '') !== 'all')
                    <a href="{{ route('admin_get_coupons') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Coupons Grid -->
    @if($coupons->count() > 0)
    <div class="coupons-grid">
        @foreach($coupons as $coupon)
        <div class="coupon-card">
            <!-- Coupon Header -->
            <div class="coupon-header">
                <div class="coupon-code">{{ $coupon->coupon }}</div>
                <span class="status-badge status-{{ $coupon->status_class }}">
                    {{ $coupon->status_text }}
                </span>
            </div>

            <!-- Discount Amount -->
            <div class="coupon-discount">{{ $coupon->percentage }}% OFF</div>

            <!-- Usage Progress -->
            <div class="coupon-usage">
                <div class="usage-bar">
                    @php
                    $barClass = 'success';
                    if ($coupon->usage_percentage > 80) $barClass = 'danger';
                    elseif ($coupon->usage_percentage > 60) $barClass = 'warning';
                    @endphp
                    <div class="usage-fill {{ $barClass }}" style="width: {{ $coupon->usage_percentage }}%"></div>
                </div>
                <div class="usage-text">
                    <span>{{ $coupon->times_used }} used</span>
                    <span>{{ $coupon->remaining_uses }} remaining</span>
                </div>
            </div>

            <!-- Coupon Statistics -->
            <div class="coupon-stats">
                <div class="stat-item">
                    <div class="stat-value">${{ number_format($coupon->total_savings, 0) }}</div>
                    <div class="stat-label">Total Savings</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $coupon->recent_orders->count() }}</div>
                    <div class="stat-label">Recent Orders</div>
                </div>
            </div>

            <!-- Coupon Actions -->
            <div class="coupon-actions">
                <a href="{{ route('admin_edit_coupon_form', $coupon) }}" class="btn-modern btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>

                @if($coupon->status === 'expired' && $coupon->max_usage > 0)
                <form method="POST" action="{{ route('admin_toggle_coupon_status', $coupon) }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn-modern btn-success"
                        onclick="return confirm('Reset usage count to 0?')">
                        <i class="fas fa-redo"></i>
                        Reset
                    </button>
                </form>
                @endif

                @if($coupon->times_used == 0)
                <form method="POST" action="{{ route('admin_delete_coupon', $coupon) }}" style="display: inline;"
                    onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </form>
                @else
                <button class="btn-modern btn-danger" disabled title="Cannot delete used coupon">
                    <i class="fas fa-lock"></i>
                    Protected
                </button>
                @endif
            </div>

            <!-- Created Date -->
            <div style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: #718096;">
                Created: {{ $coupon->created_at->format('M d, Y') }}
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($coupons->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 2rem;">
        {{ $coupons->links() }}
    </div>
    @endif

    @else
    <!-- Empty State -->
    <div class="modern-card">
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-tags"></i>
            </div>
            <h3 style="color: #4a5568; margin-bottom: 1rem;">No Coupons Found</h3>
            <p style="margin-bottom: 2rem;">
                @if($search || ($status ?? '') !== 'all')
                No coupons match your current filters. Try adjusting your search criteria.
                @else
                Start boosting sales by creating your first discount coupon.
                @endif
            </p>
            @if(!$search && ($status ?? '') === 'all')
            <a href="{{ route('admin_create_coupon_form') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus"></i>
                Create Your First Coupon
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading state to buttons
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                    // Re-enable after 3 seconds in case of network issues
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                }
            });
        });

        // Auto-submit form when filters change
        const filterSelects = document.querySelectorAll('.filter-select');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });

        // Copy coupon code to clipboard
        const couponCodes = document.querySelectorAll('.coupon-code');
        couponCodes.forEach(code => {
            code.style.cursor = 'pointer';
            code.title = 'Click to copy';
            code.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent).then(() => {
                    // Show feedback
                    const original = this.style.background;
                    this.style.background = '#dcfce7';
                    this.style.borderColor = '#22c55e';
                    setTimeout(() => {
                        this.style.background = original;
                        this.style.borderColor = '#667eea';
                    }, 1000);
                });
            });
        });
    });
</script>
@endpush