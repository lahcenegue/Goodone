@extends('admin.layouts')

@section('page-title', 'Customer Management')
@section('page-subtitle', 'Manage and monitor all your platform customers with comprehensive tools')

@section('content')
<style>
    /* Modern Customer Management Styles - matches dashboard design */
    .customers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .customer-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .customer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .customer-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .customer-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        overflow: hidden;
    }

    .customer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .customer-info h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0 0 0.25rem 0;
    }

    .customer-email {
        color: #718096;
        font-size: 0.875rem;
    }

    .customer-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin: 1rem 0;
    }

    .customer-stat {
        text-align: center;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .customer-stat-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
    }

    .customer-stat-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .customer-badges {
        display: flex;
        gap: 0.5rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .customer-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .action-btn {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        color: #4a5568;
        text-decoration: none;
        text-align: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    .action-btn:hover {
        background: #f8fafc;
        border-color: #667eea;
        color: #667eea;
        text-decoration: none;
    }

    .action-btn.primary {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .action-btn.primary:hover {
        background: #5a67d8;
        border-color: #5a67d8;
        color: white;
    }

    .action-btn.danger {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    .action-btn.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }

    /* Search and filters */
    .search-filters-section {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: center;
        margin-bottom: 2rem;
    }

    .search-container {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1.1rem;
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #4a5568;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .filter-btn:hover {
        border-color: #667eea;
        background: #f8fafc;
        color: #667eea;
        text-decoration: none;
    }

    .filter-btn.active {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    /* Stats cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Modern card */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-wrapper .pagination {
        margin: 0;
    }

    .pagination .page-link {
        color: #4a5568;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        margin: 0 0.25rem;
        border-radius: 6px;
        text-decoration: none;
    }

    .pagination .page-link:hover {
        background: #f8fafc;
        border-color: #667eea;
        color: #667eea;
    }

    .pagination .page-item.active .page-link {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .customers-grid {
            grid-template-columns: 1fr;
        }

        .search-filters-section {
            grid-template-columns: 1fr;
        }

        .filter-buttons {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Customer Management Content -->
<div class="customers-container">
    <!-- Welcome Banner -->
    <div class="modern-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-users me-2"></i>
                    Customer Management Dashboard
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Manage and monitor all your platform customers with comprehensive tools
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.875rem; opacity: 0.8;">Total Customers</div>
                <div style="font-size: 2rem; font-weight: 700; margin-top: 0.25rem;">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="search-filters-section">
        <form method="GET" action="{{ route('admin_get_customers') }}" class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="search" class="search-input"
                placeholder="Search customers by name, email, phone, location..."
                value="{{ $search }}" />
            <input type="hidden" name="status" value="{{ $status }}" />
            <input type="hidden" name="sort" value="{{ $sort }}" />
            <input type="hidden" name="direction" value="{{ $direction }}" />
        </form>
        <div class="filter-buttons">
            <a href="{{ route('admin_get_customers', ['status' => 'all', 'search' => $search]) }}"
                class="filter-btn {{ $status === 'all' ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                All Customers
            </a>
            <a href="{{ route('admin_get_customers', ['status' => 'active', 'search' => $search]) }}"
                class="filter-btn {{ $status === 'active' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                Active
            </a>
            <a href="{{ route('admin_get_customers', ['status' => 'blocked', 'search' => $search]) }}"
                class="filter-btn {{ $status === 'blocked' ? 'active' : '' }}">
                <i class="fas fa-ban"></i>
                Blocked
            </a>
            <a href="{{ route('admin_get_customers', ['status' => 'verified', 'search' => $search]) }}"
                class="filter-btn {{ $status === 'verified' ? 'active' : '' }}">
                <i class="fas fa-certificate"></i>
                Verified
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">Total Customers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['active']) }}</div>
            <div class="stat-label">Active Customers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['blocked']) }}</div>
            <div class="stat-label">Blocked Customers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['verified']) }}</div>
            <div class="stat-label">Verified Customers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['new_this_month']) }}</div>
            <div class="stat-label">New This Month</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['total_spent'], 0) }}</div>
            <div class="stat-label">Total Customer Spending</div>
        </div>
    </div>

    <!-- Customers Grid -->
    <div class="modern-card">
        <div class="card-header">
            <h3 class="card-title">
                @if($search)
                Search Results for "{{ $search }}"
                @elseif($status !== 'all')
                {{ ucfirst($status) }} Customers
                @else
                All Customers
                @endif
            </h3>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span style="color: #718096; font-size: 0.875rem;">
                    {{ $customers->total() }} customers found
                </span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin_get_customers', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
                        class="filter-btn" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-sort"></i>
                        Date {{ $sort === 'created_at' ? ($direction === 'asc' ? '?' : '?') : '' }}
                    </a>
                    <a href="{{ route('admin_get_customers', array_merge(request()->query(), ['sort' => 'full_name', 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
                        class="filter-btn" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-sort-alpha-down"></i>
                        Name {{ $sort === 'full_name' ? ($direction === 'asc' ? '?' : '?') : '' }}
                    </a>
                </div>
            </div>
        </div>
        <div style="padding: 1.5rem;">
            @if($customers->count() > 0)
            <div class="customers-grid">
                @foreach($customers as $customer)
                <div class="customer-card">
                    <div class="customer-header">
                        <div class="customer-avatar">
                            @php
                            // Get default image from app settings
                            $customerImageSetting = \App\Models\AppSetting::where("key", "=", "customer-image")->first();
                            $defaultImageName = $customerImageSetting ? $customerImageSetting->value : '';

                            // Determine which image to show
                            $imageToShow = null;
                            if ($customer->picture && $customer->picture !== '') {
                            $imageToShow = $customer->picture;
                            } elseif ($defaultImageName) {
                            $imageToShow = $defaultImageName;
                            }
                            @endphp

                            @if($imageToShow)
                            <img src="{{ asset('storage/images/' . $imageToShow) }}" alt="{{ $customer->full_name }}" />
                            @else
                            {{ strtoupper(substr($customer->full_name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="customer-info">
                            <h3>{{ $customer->full_name }}</h3>
                            <div class="customer-email">{{ $customer->email }}</div>
                        </div>
                    </div>

                    <div class="customer-stats">
                        <div class="customer-stat">
                            <div class="customer-stat-value">{{ $customer->total_orders }}</div>
                            <div class="customer-stat-label">Orders</div>
                        </div>
                        <div class="customer-stat">
                            <div class="customer-stat-value">${{ number_format($customer->total_spent, 0) }}</div>
                            <div class="customer-stat-label">Total Spent</div>
                        </div>
                    </div>

                    <div class="customer-badges">
                        <span class="badge badge-{{ $customer->status_badge['class'] }}">
                            {{ $customer->status_badge['text'] }}
                        </span>

                        {{-- Email Verification Badge --}}
                        @if($customer->email_verified_at)
                        <span class="badge badge-success">Email Verified</span>
                        @else
                        <span class="badge badge-warning">Email Unverified</span>
                        @endif

                        {{-- Admin Manual Verification Badge --}}
                        @if($customer->verified)
                        <span class="badge badge-info">Admin Verified</span>
                        @endif

                        {{-- VIP Customer Badge --}}
                        @if($customer->total_spent > 1000)
                        <span class="badge badge-warning">VIP</span>
                        @endif
                    </div>

                    <div style="color: #718096; font-size: 0.875rem; margin: 0.5rem 0;">
                        <div><i class="fas fa-phone"></i> {{ $customer->phone ?: 'No phone' }}</div>
                        @if($customer->city || $customer->country)
                        <div><i class="fas fa-map-marker-alt"></i> {{ $customer->city }}, {{ $customer->country }}</div>
                        @endif
                        <div><i class="fas fa-calendar"></i> Joined {{ $customer->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="customer-actions">
                        <a href="{{ route('admin_show_customer', $customer) }}" class="action-btn primary">
                            <i class="fas fa-eye"></i>
                            View Details
                        </a>
                        @if($customer->blocked)
                        <form method="POST" action="{{ route('admin_toggle_customer_block', $customer) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" class="action-btn" style="width: 100%;">
                                <i class="fas fa-unlock"></i>
                                Unblock
                            </button>
                        </form>
                        @else
                        <a href="{{ route('admin_edit_customer_form', $customer) }}" class="action-btn">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                        @endif
                        @if(!$customer->active)
                        <form method="POST" action="{{ route('admin_toggle_customer_activation', $customer) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" class="action-btn">
                                <i class="fas fa-check"></i>
                                Activate
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $customers->appends(request()->query())->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <i class="fas fa-users" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <h3 style="margin-bottom: 0.5rem;">No customers found</h3>
                <p>Try adjusting your search criteria or filters.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search form on input with debounce
        const searchInput = document.querySelector('.search-input');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.closest('form').submit();
                }, 500);
            });
        }

        // Confirm before blocking/deleting customers
        document.querySelectorAll('form[action*="toggle-block"], form[action*="delete"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = this.action.includes('delete') ? 'delete' : 'block';
                const customer = this.closest('.customer-card').querySelector('h3').textContent;

                if (!confirm(`Are you sure you want to ${action} ${customer}?`)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

@endsection