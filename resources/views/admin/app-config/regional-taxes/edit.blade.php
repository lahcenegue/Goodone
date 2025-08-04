@extends('admin.layouts')

@section('page-title', 'Edit Regional Tax Rate')
@section('page-subtitle', 'Update tax rate settings and view usage analytics.')

@section('content')
<style>
    /* Regional Tax Edit Form Styles */
    .edit-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .card-header {
        text-align: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .card-subtitle {
        color: #718096;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .form-input.error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .form-input:disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .input-group {
        position: relative;
    }

    .input-suffix {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #718096;
        font-weight: 600;
        pointer-events: none;
    }

    .tax-display {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .display-region {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .display-percentage {
        font-size: 3rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }

    .canadian-flag {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 30px;
        height: 20px;
        background: linear-gradient(to right, #ff0000 33%, #ffffff 33%, #ffffff 66%, #ff0000 66%);
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .usage-section {
        margin-bottom: 1.5rem;
    }

    .usage-stats {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    .usage-stat {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-modern {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
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

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .analytics-section {
        grid-column: 1 / -1;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .recent-orders {
        max-height: 300px;
        overflow-y: auto;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .required {
        color: #ef4444;
    }

    .help-text {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    .warning-box {
        background: #fef3c7;
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .warning-title {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .warning-text {
        color: #92400e;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .calculation-section {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .calc-title {
        color: #0c4a6e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .calc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.75rem;
    }

    .calc-item {
        background: white;
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #bae6fd;
    }

    .calc-amount {
        font-size: 0.8rem;
        color: #0369a1;
        margin-bottom: 0.25rem;
    }

    .calc-tax {
        font-weight: 700;
        color: #0c4a6e;
        font-size: 0.9rem;
    }

    .calc-total {
        font-size: 0.75rem;
        color: #075985;
        margin-top: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .edit-grid {
            grid-template-columns: 1fr;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .usage-stats {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .calc-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="edit-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_regional_taxes') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Regional Taxes
        </a>
    </div>

    <!-- Edit Grid -->
    <div class="edit-grid">
        <!-- Tax Edit Form -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-edit" style="color: #667eea;"></i>
                    Edit Tax Rate
                </h2>
                <p class="card-subtitle">Update regional tax configuration</p>
            </div>

            @if($regionTax->orders_count > 0)
            <div class="warning-box">
                <div class="warning-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tax Rate In Use
                </div>
                <div class="warning-text">
                    This tax rate has been used in {{ $regionTax->orders_count }} completed orders. Changes will only affect new orders and won't impact existing order history.
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin_update_regional_tax', $regionTax) }}" id="taxForm">
                @csrf
                @method('PUT')

                <!-- Region Name -->
                <div class="form-group">
                    <label for="region" class="form-label">
                        Region/Province Name <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="region"
                        name="region"
                        class="form-input {{ $errors->has('region') ? 'error' : '' }}"
                        value="{{ old('region', $regionTax->region) }}"
                        required
                        maxlength="255"
                        {{ $regionTax->orders_count > 0 ? 'disabled' : '' }}>
                    @if($regionTax->orders_count > 0)
                    <div class="help-text">Region name cannot be changed after first use to maintain order history integrity.</div>
                    @else
                    <div class="help-text">Enter the full name of the province, state, or region.</div>
                    @endif
                    @error('region')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Tax Percentage -->
                <div class="form-group">
                    <label for="percentage" class="form-label">
                        Tax Percentage <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="percentage"
                            name="percentage"
                            class="form-input {{ $errors->has('percentage') ? 'error' : '' }}"
                            value="{{ old('percentage', $regionTax->percentage) }}"
                            required
                            min="0"
                            max="50"
                            step="0.001">
                        <span class="input-suffix">%</span>
                    </div>
                    <div class="help-text">
                        @if($regionTax->orders_count > 0)
                        Changes will apply to all new orders. Existing orders keep their original tax amounts.
                        @else
                        Enter tax rate as a percentage (0-50%). You can use up to 3 decimal places.
                        @endif
                    </div>
                    @error('percentage')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">
                        Description <small>(optional)</small>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-input {{ $errors->has('description') ? 'error' : '' }}"
                        rows="3"
                        maxlength="500">{{ old('description', $regionTax->description ?? '') }}</textarea>
                    <div class="help-text">Internal description for your reference.</div>
                    @error('description')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Update Tax Rate
                    </button>
                </div>
            </form>
        </div>

        <!-- Tax Display & Analytics -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar" style="color: #22c55e;"></i>
                    Tax Rate Analytics
                </h2>
                <p class="card-subtitle">Current status and usage statistics</p>
            </div>

            <!-- Tax Display -->
            <div class="tax-display">
                @if(in_array($regionTax->region, array_keys($canadianProvinces ?? [])))
                <div class="canadian-flag">🍁</div>
                @endif
                <div class="display-region">{{ $regionTax->region }}</div>
                <div class="display-percentage">{{ $regionTax->percentage }}%</div>
                <div style="font-size: 0.875rem; opacity: 0.9;">
                    {{ $regionTax->description ?: 'Regional Tax Rate' }}
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="usage-section">
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Usage Statistics</h4>
                <div class="usage-stats">
                    <div class="usage-stat">
                        <div class="stat-value">{{ number_format($regionTax->orders_count) }}</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="usage-stat">
                        <div class="stat-value">${{ number_format($regionTax->total_tax_collected, 0) }}</div>
                        <div class="stat-label">Tax Collected</div>
                    </div>
                    <div class="usage-stat">
                        <div class="stat-value">${{ number_format($regionTax->avg_tax_per_order, 2) }}</div>
                        <div class="stat-label">Avg per Order</div>
                    </div>
                </div>
            </div>

            <!-- Tax Calculations -->
            <div class="calculation-section">
                <div class="calc-title">
                    <i class="fas fa-calculator"></i>
                    Tax Calculations
                </div>
                <div class="calc-grid">
                    @foreach([25, 50, 100, 150, 200, 500] as $amount)
                    @php
                    $tax = ($amount * $regionTax->percentage) / 100;
                    $total = $amount + $tax;
                    @endphp
                    <div class="calc-item">
                        <div class="calc-amount">${{ $amount }} order</div>
                        <div class="calc-tax">${{ number_format($tax, 2) }} tax</div>
                        <div class="calc-total">${{ number_format($total, 2) }} total</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center;">
                <a href="{{ route('admin_tax_calculator') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-calculator"></i>
                    Tax Calculator
                </a>

                @if($regionTax->orders_count == 0)
                <form method="POST" action="{{ route('admin_delete_regional_tax', $regionTax) }}" style="display: inline;"
                    onsubmit="return confirm('Are you sure you want to delete this tax rate? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete Tax Rate
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    @if(isset($analytics) && $analytics['usage']['total_orders'] > 0)
    <div class="form-card analytics-section">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-analytics" style="color: #8b5cf6;"></i>
                Detailed Analytics
            </h2>
            <p class="card-subtitle">Tax collection trends and recent activity</p>
        </div>

        <div class="analytics-grid">
            <!-- Monthly Collection Trend -->
            <div>
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Monthly Tax Collection</h4>
                <div style="display: flex; align-items: end; gap: 0.5rem; height: 120px; background: #f8fafc; padding: 1rem; border-radius: 8px;">
                    @foreach($analytics['monthly_collection'] as $month)
                    @php
                    $maxCollection = max(1, max(array_column($analytics['monthly_collection'], 'amount')));
                    $height = ($month['amount'] / $maxCollection) * 80;
                    @endphp
                    <div style="flex: 1; text-align: center;">
                        <div style="background: #667eea; height: {{ $height }}px; margin-bottom: 0.5rem; border-radius: 2px;"></div>
                        <div style="font-size: 0.7rem; color: #718096;">{{ $month['month'] }}</div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: #2d3748;">${{ number_format($month['amount'], 0) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Orders -->
            <div>
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Recent Orders ({{ $analytics['recent_orders']->count() }})</h4>
                <div class="recent-orders">
                    @if($analytics['recent_orders']->count() > 0)
                    @foreach($analytics['recent_orders'] as $order)
                    <div class="order-item">
                        <div>
                            <div style="font-weight: 600;">{{ $order->user->full_name ?? 'Unknown Customer' }}</div>
                            <div style="color: #718096; font-size: 0.8rem;">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: #667eea;">
                                ${{ number_format($order->taxed_amount ?? 0, 2) }}
                            </div>
                            <div style="color: #718096; font-size: 0.8rem;">
                                tax collected
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div style="text-align: center; padding: 2rem; color: #718096;">
                        <i class="fas fa-receipt" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                        <p>No recent orders</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('taxForm');
        const submitBtn = document.getElementById('submitBtn');

        // Form submission handling
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        });

        // Validate percentage input
        document.getElementById('percentage').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value > 50) this.value = '50';
            if (value < 0) this.value = '0';
        });

        // Format region name as user types (if not disabled)
        const regionInput = document.getElementById('region');
        if (!regionInput.disabled) {
            regionInput.addEventListener('input', function() {
                // Capitalize first letter of each word
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });
        }

        // Confirmation for dangerous actions
        const dangerousForms = document.querySelectorAll('form[onsubmit*="confirm"]');
        dangerousForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    setTimeout(() => {
                        button.disabled = false;
                    }, 3000);
                }
            });
        });
    });
</script>
@endpush