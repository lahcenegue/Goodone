@extends('admin.layouts')

@section('page-title', 'Advertisement Manager')
@section('page-subtitle', 'Manage your app advertisements and track their performance')

@section('content')
<style>
    /* Modern Ads Management Styles - Matching Categories/Coupons Design */
    .ads-container {
        max-width: 100%;
    }

    /* Header Banner - Matching Categories Style */
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    /* Modern Card Design */
    .modern-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    /* Statistics Grid - Matching Categories */
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
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Ads Grid - Enhanced Design */
    .ads-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .ad-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .ad-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .ad-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    /* Ad Card Content */
    .ad-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .ad-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }

    .ad-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 1rem;
        border: 2px solid #e2e8f0;
    }

    .ad-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }

    .ad-meta-item {
        display: flex;
        flex-direction: column;
    }

    .ad-meta-label {
        color: #718096;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .ad-meta-value {
        color: #2d3748;
        font-weight: 600;
    }

    /* Performance Stats */
    .ad-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }

    .ad-stat {
        text-align: center;
    }

    .ad-stat-value {
        font-weight: 700;
        color: #2d3748;
        display: block;
        font-size: 1rem;
    }

    .ad-stat-label {
        color: #718096;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Status Badges - Matching Categories Style */
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

    /* Action Buttons - Matching Categories */
    .ad-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
        justify-content: center;
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
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #edf2f7;
        color: #2d3748;
        text-decoration: none;
    }

    .btn-success {
        background: #dcfce7;
        color: #166534;
        border: 2px solid #bbf7d0;
    }

    .btn-success:hover {
        background: #bbf7d0;
        color: #14532d;
        text-decoration: none;
    }

    .btn-warning {
        background: #fef3c7;
        color: #92400e;
        border: 2px solid #f59e0b;
    }

    .btn-warning:hover {
        background: #fde68a;
        color: #78350f;
        text-decoration: none;
    }

    .btn-danger {
        background: #fed7d7;
        color: #c53030;
        border: 2px solid #feb2b2;
    }

    .btn-danger:hover {
        background: #fbb6ce;
        color: #97266d;
        text-decoration: none;
    }

    /* Search and Filters - Matching Categories */
    .search-section {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .search-input {
        flex: 1;
        min-width: 300px;
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
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Empty State - Matching Categories */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #718096;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Bulk Actions Bar */
    .bulk-actions-bar {
        background: #f0f4ff;
        border: 2px solid #667eea;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 2rem;
        display: none;
    }

    .bulk-actions-bar.show {
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .ads-grid {
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

        .chart-container {
            padding: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="ads-container">
    <!-- Welcome Banner - Matching Categories Design -->
    <div class="chart-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-layer-group me-2"></i>
                    Advertisement Manager
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Create, manage, and track your app advertisements with advanced targeting
                </p>
            </div>
            <div>
                <a href="{{ route('admin_create_ad_form') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-plus"></i>
                    Create New Ad
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Overview - Matching Categories Grid -->
    @if(isset($stats))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value" style="color: #667eea;">{{ $stats['total_ads'] ?? 0 }}</div>
            <div class="stats-label">Total Ads</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #22c55e;">{{ $stats['active_ads'] ?? 0 }}</div>
            <div class="stats-label">Active Ads</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #f59e0b;">{{ number_format($stats['total_views'] ?? 0) }}</div>
            <div class="stats-label">Total Views</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #ef4444;">{{ number_format($stats['total_clicks'] ?? 0) }}</div>
            <div class="stats-label">Total Clicks</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #8b5cf6;">{{ $stats['avg_ctr'] ?? 0 }}%</div>
            <div class="stats-label">Avg CTR</div>
        </div>
    </div>
    @endif

    <!-- Search and Filters - Matching Categories Style -->
    <div class="modern-card">
        <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
            <i class="fas fa-search me-2"></i>
            Search & Filter Advertisements
        </h3>
        <form method="GET" action="{{ route('admin_get_ads') }}" id="filtersForm">
            <div class="search-section">
                <input
                    type="text"
                    name="search"
                    placeholder="Search by title, description, or URL..."
                    value="{{ $search ?? '' }}"
                    class="search-input">

                <select name="ad_type" class="filter-select">
                    <option value="all" {{ ($ad_type ?? '') === 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="internal" {{ ($ad_type ?? '') === 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="external" {{ ($ad_type ?? '') === 'external' ? 'selected' : '' }}>External</option>
                </select>

                <select name="placement" class="filter-select">
                    <option value="all" {{ ($placement ?? '') === 'all' ? 'selected' : '' }}>All Placements</option>
                    <option value="home_banner" {{ ($placement ?? '') === 'home_banner' ? 'selected' : '' }}>Home Banner</option>
                    <option value="service_list" {{ ($placement ?? '') === 'service_list' ? 'selected' : '' }}>Service List</option>
                    <option value="service_detail" {{ ($placement ?? '') === 'service_detail' ? 'selected' : '' }}>Service Detail</option>
                    <option value="profile" {{ ($placement ?? '') === 'profile' ? 'selected' : '' }}>Profile</option>
                </select>

                <select name="status" class="filter-select">
                    <option value="all" {{ ($status ?? '') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="scheduled" {{ ($status ?? '') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="expired" {{ ($status ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <select name="sort" class="filter-select">
                    <option value="created_at" {{ ($sort ?? '') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                    <option value="title" {{ ($sort ?? '') === 'title' ? 'selected' : '' }}>Title</option>
                    <option value="view_count" {{ ($sort ?? '') === 'view_count' ? 'selected' : '' }}>Views</option>
                    <option value="click_count" {{ ($sort ?? '') === 'click_count' ? 'selected' : '' }}>Clicks</option>
                    <option value="display_order" {{ ($sort ?? '') === 'display_order' ? 'selected' : '' }}>Priority</option>
                </select>

                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>

                @if($search || ($status ?? '') !== 'all' || ($ad_type ?? '') !== 'all' || ($placement ?? '') !== 'all')
                <a href="{{ route('admin_get_ads') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ads Grid -->
    @if(isset($ads) && $ads->count() > 0)
    <div class="ads-grid">
        @foreach($ads as $ad)
        <div class="ad-card">
            <!-- Ad Image -->
            @if($ad->image)
            <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="ad-image">
            @else
            <div class="ad-image" style="display: flex; align-items: center; justify-content: center; background: #f8fafc; color: #9ca3af;">
                <i class="fas fa-image" style="font-size: 2rem;"></i>
            </div>
            @endif

            <!-- Ad Header -->
            <div class="ad-header">
                <div>
                    <h3 class="ad-title">{{ $ad->title }}</h3>
                    <span class="status-badge status-{{ $ad->status_badge_class }}">
                        {{ ucfirst($ad->status) }}
                    </span>
                </div>
            </div>

            <!-- Ad Meta Information -->
            <div class="ad-meta">
                <div class="ad-meta-item">
                    <span class="ad-meta-label">Type</span>
                    <span class="ad-meta-value">{{ $ad->formatted_ad_type }}</span>
                </div>
                <div class="ad-meta-item">
                    <span class="ad-meta-label">Placement</span>
                    <span class="ad-meta-value">{{ $ad->formatted_placement }}</span>
                </div>
                <div class="ad-meta-item">
                    <span class="ad-meta-label">Display Order</span>
                    <span class="ad-meta-value">{{ $ad->display_order }}</span>
                </div>
                <div class="ad-meta-item">
                    <span class="ad-meta-label">Created</span>
                    <span class="ad-meta-value">{{ $ad->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Performance Stats -->
            <div class="ad-stats">
                <div class="ad-stat">
                    <span class="ad-stat-value">{{ number_format($ad->view_count) }}</span>
                    <span class="ad-stat-label">Views</span>
                </div>
                <div class="ad-stat">
                    <span class="ad-stat-value">{{ number_format($ad->click_count) }}</span>
                    <span class="ad-stat-label">Clicks</span>
                </div>
                <div class="ad-stat">
                    <span class="ad-stat-value">{{ $ad->view_count > 0 ? number_format(($ad->click_count / $ad->view_count) * 100, 1) : 0 }}%</span>
                    <span class="ad-stat-label">CTR</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="ad-actions">
                <a href="{{ route('admin_edit_ad_form', $ad) }}" class="btn-modern btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>

                @if($ad->view_count > 0 || $ad->click_count > 0)
                <a href="{{ route('admin_show_ad_analytics', $ad) }}" class="btn-modern btn-secondary">
                    <i class="fas fa-chart-bar"></i> Analytics
                </a>
                @endif

                <form method="POST" action="{{ route('admin_toggle_ad_status', $ad) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-modern {{ $ad->is_active ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas {{ $ad->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                        {{ $ad->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin_delete_ad', $ad) }}" style="display: inline;"
                    onsubmit="return confirm('Are you sure you want to delete this advertisement? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($ads->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 2rem;">
        {{ $ads->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    <!-- Empty State - Matching Categories -->
    <div class="modern-card">
        <div class="empty-state">
            <i class="fas fa-bullhorn"></i>
            <h3 style="margin-bottom: 1rem; color: #4a5568;">No Advertisements Found</h3>
            <p style="margin-bottom: 2rem;">
                @if($search || ($status ?? '') !== 'all' || ($ad_type ?? '') !== 'all' || ($placement ?? '') !== 'all')
                No advertisements match your current filters. Try adjusting your search criteria.
                @else
                Start monetizing your app by creating your first advertisement.
                @endif
            </p>
            @if(!$search && ($status ?? '') === 'all')
            <a href="{{ route('admin_create_ad_form') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus"></i>
                Create Your First Ad
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit filters on change - Matching Categories behavior
        const filterSelects = document.querySelectorAll('select[name="ad_type"], select[name="placement"], select[name="status"], select[name="sort"]');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filtersForm').submit();
            });
        });

        // Search with delay - Matching Categories behavior
        const searchInput = document.querySelector('input[name="search"]');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filtersForm').submit();
                }, 500);
            });
        }

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
    });
</script>

@endsection