@extends('admin.layouts')

@section('page-title', 'Regional Taxes Management')
@section('page-subtitle', 'Configure tax rates for different provinces and regions.')

@section('content')
<style>
    /* Regional Taxes Management Styles */
    .taxes-container {
        max-width: 100%;
    }

    .taxes-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .taxes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .tax-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .tax-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .tax-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .tax-card.canadian::before {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    .tax-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .tax-region {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }

    .tax-percentage {
        font-size: 2.5rem;
        font-weight: 800;
        color: #667eea;
        text-align: center;
        margin: 1rem 0;
    }

    .canadian-flag {
        width: 24px;
        height: 16px;
        background: linear-gradient(to right, #ff0000 33%, #ffffff 33%, #ffffff 66%, #ff0000 66%);
        border-radius: 2px;
        position: relative;
    }

    .canadian-flag::before {
        content: '🍁';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 10px;
    }

    .tax-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .tax-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
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

    .quick-setup {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .setup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .setup-title {
        color: #0c4a6e;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }

    .provinces-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    .province-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: white;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .province-name {
        font-weight: 600;
        color: #0c4a6e;
    }

    .province-rate {
        color: #0369a1;
        font-weight: 700;
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

    .calculator-preview {
        background: #fef3c7;
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .taxes-grid {
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

        .tax-actions {
            justify-content: center;
        }

        .provinces-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="taxes-container">
    <!-- Welcome Banner -->
    <div class="taxes-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-percentage me-2"></i>
                    Regional Taxes Management
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Configure tax rates for different provinces and regions
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('admin_tax_calculator') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-calculator"></i>
                    Tax Calculator
                </a>
                <a href="{{ route('admin_create_regional_tax_form') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-plus"></i>
                    Add Tax Rate
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($stats))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value" style="color: #667eea;">{{ $stats['total_regions'] }}</div>
            <div class="stats-label">Configured Regions</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #22c55e;">{{ number_format($stats['avg_tax_rate'], 2) }}%</div>
            <div class="stats-label">Average Tax Rate</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #ef4444;">{{ number_format($stats['highest_tax'], 2) }}%</div>
            <div class="stats-label">Highest Tax Rate</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #f59e0b;">{{ number_format($stats['total_orders_with_tax']) }}</div>
            <div class="stats-label">Orders with Tax</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #8b5cf6;">${{ number_format($stats['total_tax_collected'], 2) }}</div>
            <div class="stats-label">Total Tax Collected</div>
        </div>
    </div>
    @endif

    <!-- Canadian Provinces Quick Setup -->
    @if(isset($canadianProvinces) && $regionalTaxes->count() < count($canadianProvinces))
        <div class="quick-setup">
        <div class="setup-header">
            <h3 class="setup-title">
                <i class="fas fa-maple-leaf" style="color: #ef4444;"></i>
                Canadian Provinces Quick Setup
            </h3>
            <span style="color: #0369a1; font-size: 0.875rem;">
                {{ count($canadianProvinces) - $regionalTaxes->whereIn('region', array_keys($canadianProvinces))->count() }} provinces available
            </span>
        </div>
        <p style="color: #0c4a6e; margin-bottom: 1rem; font-size: 0.9rem;">
            Quickly add standard Canadian provincial tax rates. You can modify these rates after adding them.
        </p>
        <div class="provinces-grid">
            @foreach($canadianProvinces as $province => $rate)
            @if(!$regionalTaxes->where('region', $province)->count())
            <div class="province-item">
                <span class="province-name">{{ $province }}</span>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="province-rate">{{ $rate }}%</span>
                    <form method="POST" action="{{ route('admin_store_regional_tax') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="region" value="{{ $province }}">
                        <input type="hidden" name="percentage" value="{{ $rate }}">
                        <button type="submit" class="btn-modern btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <i class="fas fa-plus"></i>
                            Add
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endforeach
        </div>
</div>
@endif

<!-- Search and Filters -->
<div class="modern-card" style="margin-bottom: 2rem;">
    <div style="padding: 1.5rem;">
        <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
            <i class="fas fa-search me-2"></i>
            Search Tax Rates
        </h3>
        <form method="GET" action="{{ route('admin_get_regional_taxes') }}">
            <div class="search-section">
                <input
                    type="text"
                    name="search"
                    placeholder="Search by region or tax rate..."
                    value="{{ $search ?? '' }}"
                    class="search-input">
                <select name="sort" class="filter-select">
                    <option value="region" {{ ($sort ?? '') === 'region' ? 'selected' : '' }}>Sort by Region</option>
                    <option value="percentage" {{ ($sort ?? '') === 'percentage' ? 'selected' : '' }}>Sort by Tax Rate</option>
                    <option value="created_at" {{ ($sort ?? '') === 'created_at' ? 'selected' : '' }}>Sort by Date Added</option>
                </select>
                <select name="direction" class="filter-select">
                    <option value="asc" {{ ($direction ?? '') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ ($direction ?? '') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-search"></i>
                    Apply
                </button>
                @if($search)
                <a href="{{ route('admin_get_regional_taxes') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Regional Taxes Grid -->
@if($regionalTaxes->count() > 0)
<div class="taxes-grid">
    @foreach($regionalTaxes as $tax)
    <div class="tax-card {{ $tax->is_canadian_province ? 'canadian' : '' }}">
        <!-- Tax Header -->
        <div class="tax-header">
            <div>
                <div class="tax-region">
                    {{ $tax->region }}
                    @if($tax->is_canadian_province)
                    <span class="canadian-flag" title="Canadian Province"></span>
                    @endif
                </div>
                @if($tax->description)
                <div style="color: #718096; font-size: 0.875rem;">{{ $tax->description }}</div>
                @endif
            </div>
        </div>

        <!-- Tax Percentage -->
        <div class="tax-percentage">{{ $tax->percentage }}%</div>

        <!-- Tax Statistics -->
        <div class="tax-stats">
            <div class="stat-item">
                <div class="stat-value">{{ number_format($tax->orders_count) }}</div>
                <div class="stat-label">Orders</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${{ number_format($tax->total_tax_collected, 0) }}</div>
                <div class="stat-label">Tax Collected</div>
            </div>
        </div>

        <!-- Sample Calculation -->
        <div style="background: #f8fafc; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; text-align: center;">
            <div style="font-size: 0.8rem; color: #718096; margin-bottom: 0.25rem;">Sample on $100 order:</div>
            <div style="font-weight: 700; color: #2d3748;">${{ number_format(100 * $tax->percentage / 100, 2) }} tax = ${{ number_format(100 + (100 * $tax->percentage / 100), 2) }} total</div>
        </div>

        <!-- Tax Actions -->
        <div class="tax-actions">
            <a href="{{ route('admin_edit_regional_tax_form', $tax) }}" class="btn-modern btn-primary">
                <i class="fas fa-edit"></i>
                Edit
            </a>

            @if($tax->orders_count == 0)
            <form method="POST" action="{{ route('admin_delete_regional_tax', $tax) }}" style="display: inline;"
                onsubmit="return confirm('Are you sure you want to delete this tax rate?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-modern btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            </form>
            @else
            <button class="btn-modern btn-danger" disabled title="Cannot delete tax rate used in orders">
                <i class="fas fa-lock"></i>
                Protected
            </button>
            @endif
        </div>

        <!-- Created Date -->
        <div style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: #718096;">
            Added: {{ $tax->created_at->format('M d, Y') }}
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if($regionalTaxes->hasPages())
<div style="display: flex; justify-content: center; margin-top: 2rem;">
    {{ $regionalTaxes->links() }}
</div>
@endif

@else
<!-- Empty State -->
<div class="modern-card">
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-percentage"></i>
        </div>
        <h3 style="color: #4a5568; margin-bottom: 1rem;">No Tax Rates Configured</h3>
        <p style="margin-bottom: 2rem;">
            @if($search)
            No tax rates match your search "{{ $search }}". Try a different search term.
            @else
            Start by adding tax rates for the regions where your platform operates.
            @endif
        </p>
        @if(!$search)
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('admin_create_regional_tax_form') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus"></i>
                Add Tax Rate
            </a>
            <a href="{{ route('admin_tax_calculator') }}" class="btn-modern btn-secondary">
                <i class="fas fa-calculator"></i>
                Tax Calculator
            </a>
        </div>
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

        // Hover effect for tax cards
        const taxCards = document.querySelectorAll('.tax-card');
        taxCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.borderColor = '#667eea';
            });

            card.addEventListener('mouseleave', function() {
                this.style.borderColor = '#e2e8f0';
            });
        });
    });
</script>
@endpush