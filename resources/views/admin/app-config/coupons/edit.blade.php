@extends('admin.layouts')

@section('page-title', 'Edit Coupon')
@section('page-subtitle', 'Update coupon settings and view usage analytics.')

@section('content')
<style>
    /* Coupon Edit Form Styles */
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

    .coupon-display {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .coupon-display::before {
        content: '';
        position: absolute;
        top: 50%;
        left: -10px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #f8fafc;
        transform: translateY(-50%);
    }

    .coupon-display::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -10px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #f8fafc;
        transform: translateY(-50%);
    }

    .display-code {
        font-size: 2rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        margin-bottom: 0.5rem;
    }

    .display-discount {
        font-size: 3rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 1rem;
        display: inline-block;
    }

    .status-success {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.2);
        border: 2px solid rgba(245, 158, 11, 0.4);
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid rgba(239, 68, 68, 0.4);
    }

    .usage-section {
        margin-bottom: 1.5rem;
    }

    .usage-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 12px;
        overflow: hidden;
        margin-bottom: 0.75rem;
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

    .usage-stats {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        text-align: center;
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
    }
</style>

<div class="edit-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_coupons') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Coupons
        </a>
    </div>

    <!-- Edit Grid -->
    <div class="edit-grid">
        <!-- Coupon Edit Form -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-edit" style="color: #667eea;"></i>
                    Edit Coupon
                </h2>
                <p class="card-subtitle">Update coupon settings and limits</p>
            </div>

            @if($coupon->times_used > 0)
            <div class="warning-box">
                <div class="warning-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Coupon In Use
                </div>
                <div class="warning-text">
                    This coupon has been used {{ $coupon->times_used }} times. Be careful when making changes as it may affect existing customers who have saved this coupon.
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin_update_coupon', $coupon) }}" id="couponForm">
                @csrf
                @method('PUT')

                <!-- Coupon Code -->
                <div class="form-group">
                    <label for="coupon" class="form-label">
                        Coupon Code <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="coupon"
                        name="coupon"
                        class="form-input {{ $errors->has('coupon') ? 'error' : '' }}"
                        value="{{ old('coupon', $coupon->coupon) }}"
                        required
                        maxlength="50"
                        style="text-transform: uppercase;"
                        {{ $coupon->times_used > 0 ? 'disabled' : '' }}>
                    @if($coupon->times_used > 0)
                    <div class="help-text">Code cannot be changed after first use to prevent confusion.</div>
                    @else
                    <div class="help-text">Use uppercase letters and numbers only.</div>
                    @endif
                    @error('coupon')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Discount Percentage -->
                <div class="form-group">
                    <label for="percentage" class="form-label">
                        Discount Percentage <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="percentage"
                            name="percentage"
                            class="form-input {{ $errors->has('percentage') ? 'error' : '' }}"
                            value="{{ old('percentage', $coupon->percentage) }}"
                            required
                            min="1"
                            max="100"
                            step="0.01">
                        <span class="input-suffix">%</span>
                    </div>
                    <div class="help-text">
                        @if($coupon->times_used > 0)
                        Be careful: changing this affects all future uses of this coupon.
                        @else
                        Enter a value between 1% and 100%.
                        @endif
                    </div>
                    @error('percentage')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Maximum Usage -->
                <div class="form-group">
                    <label for="max_usage" class="form-label">
                        Maximum Usage <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="max_usage"
                        name="max_usage"
                        class="form-input {{ $errors->has('max_usage') ? 'error' : '' }}"
                        value="{{ old('max_usage', $coupon->max_usage) }}"
                        required
                        min="{{ $coupon->times_used }}">
                    <div class="help-text">
                        @if($coupon->times_used > 0)
                        Cannot be less than current usage ({{ $coupon->times_used }}). Set to 0 for unlimited.
                        @else
                        Set to 0 for unlimited usage.
                        @endif
                    </div>
                    @error('max_usage')
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
                        maxlength="500">{{ old('description', $coupon->description ?? '') }}</textarea>
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
                        Update Coupon
                    </button>
                </div>
            </form>
        </div>

        <!-- Coupon Display & Stats -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar" style="color: #22c55e;"></i>
                    Coupon Analytics
                </h2>
                <p class="card-subtitle">Current status and usage statistics</p>
            </div>

            <!-- Coupon Display -->
            <div class="coupon-display">
                <div class="display-code">{{ $coupon->coupon }}</div>
                <div class="display-discount">{{ $coupon->percentage }}% OFF</div>
                @php
                $statusClass = 'success';
                $statusText = 'Active';
                if ($coupon->max_usage > 0 && $coupon->times_used >= $coupon->max_usage) {
                $statusClass = 'danger';
                $statusText = 'Expired';
                } elseif ($coupon->max_usage > 0 && $coupon->times_used / $coupon->max_usage > 0.8) {
                $statusClass = 'warning';
                $statusText = 'Nearly Full';
                } elseif ($coupon->max_usage == 0) {
                $statusClass = 'success';
                $statusText = 'Unlimited';
                }
                @endphp
                <div class="status-badge status-{{ $statusClass }}">{{ $statusText }}</div>
            </div>

            <!-- Usage Progress -->
            <div class="usage-section">
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Usage Progress</h4>
                @if($coupon->max_usage > 0)
                @php
                $usagePercentage = min(100, ($coupon->times_used / $coupon->max_usage) * 100);
                $barClass = 'success';
                if ($usagePercentage > 80) $barClass = 'danger';
                elseif ($usagePercentage > 60) $barClass = 'warning';
                @endphp
                <div class="usage-bar">
                    <div class="usage-fill {{ $barClass }}" style="width: {{ $usagePercentage }}%"></div>
                </div>
                <div style="text-align: center; font-size: 0.875rem; color: #718096; margin-bottom: 1rem;">
                    {{ $coupon->times_used }} of {{ $coupon->max_usage }} uses ({{ number_format($usagePercentage, 1) }}%)
                </div>
                @else
                <div style="text-align: center; padding: 1rem; background: #f0f9ff; border: 2px dashed #0ea5e9; border-radius: 8px; margin-bottom: 1rem;">
                    <i class="fas fa-infinity" style="color: #0ea5e9; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                    <div style="color: #0c4a6e; font-weight: 600;">Unlimited Usage</div>
                    <div style="color: #0369a1; font-size: 0.875rem;">Used {{ $coupon->times_used }} times</div>
                </div>
                @endif

                <div class="usage-stats">
                    <div class="usage-stat">
                        <div class="stat-value">{{ $coupon->times_used }}</div>
                        <div class="stat-label">Total Uses</div>
                    </div>
                    <div class="usage-stat">
                        <div class="stat-value">
                            @if($coupon->max_usage > 0)
                            {{ max(0, $coupon->max_usage - $coupon->times_used) }}
                            @else
                            ∞
                            @endif
                        </div>
                        <div class="stat-label">Remaining</div>
                    </div>
                    <div class="usage-stat">
                        <div class="stat-value">${{ number_format($analytics['financial']['total_savings'] ?? 0, 0) }}</div>
                        <div class="stat-label">Total Savings</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center;">
                @if($coupon->max_usage > 0 && $coupon->times_used >= $coupon->max_usage)
                <form method="POST" action="{{ route('admin_toggle_coupon_status', $coupon) }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn-modern btn-warning"
                        onclick="return confirm('Reset usage count to 0? This will make the coupon active again.')">
                        <i class="fas fa-redo"></i>
                        Reset Usage
                    </button>
                </form>
                @endif

                @if($coupon->times_used == 0)
                <form method="POST" action="{{ route('admin_delete_coupon', $coupon) }}" style="display: inline;"
                    onsubmit="return confirm('Are you sure you want to delete this coupon? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete Coupon
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    @if(isset($analytics) && $analytics['usage']['total_uses'] > 0)
    <div class="form-card analytics-section">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-analytics" style="color: #8b5cf6;"></i>
                Detailed Analytics
            </h2>
            <p class="card-subtitle">Usage trends and recent activity</p>
        </div>

        <div class="analytics-grid">
            <!-- Monthly Usage Trend -->
            <div>
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Monthly Usage Trend</h4>
                <div style="display: flex; align-items: end; gap: 0.5rem; height: 100px; background: #f8fafc; padding: 1rem; border-radius: 8px;">
                    @foreach($analytics['monthly_usage'] as $month)
                    @php
                    $maxUsage = max(1, max(array_column($analytics['monthly_usage'], 'usage')));
                    $height = ($month['usage'] / $maxUsage) * 60;
                    @endphp
                    <div style="flex: 1; text-align: center;">
                        <div style="background: #667eea; height: {{ $height }}px; margin-bottom: 0.5rem; border-radius: 2px;"></div>
                        <div style="font-size: 0.7rem; color: #718096;">{{ $month['month'] }}</div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: #2d3748;">{{ $month['usage'] }}</div>
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
                            <div style="font-weight: 600;">{{ $order->user->full_name ?? 'Unknown' }}</div>
                            <div style="color: #718096; font-size: 0.8rem;">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600; color: #22c55e;">
                                -${{ number_format($order->discounted_amount ?? 0, 2) }}
                            </div>
                            <div style="color: #718096; font-size: 0.8rem;">
                                saved
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div style="text-align: center; padding: 2rem; color: #718096;">
                        <i class="fas fa-shopping-cart" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
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
        const form = document.getElementById('couponForm');
        const submitBtn = document.getElementById('submitBtn');

        // Form submission handling
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        });

        // Force uppercase for coupon code (if not disabled)
        const couponInput = document.getElementById('coupon');
        if (!couponInput.disabled) {
            couponInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Validate percentage input
        document.getElementById('percentage').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value > 100) this.value = '100';
            if (value < 0) this.value = '0';
        });

        // Validate max usage input
        const maxUsageInput = document.getElementById('max_usage');
        const minUsage = {
            {
                $coupon - > times_used
            }
        };
        maxUsageInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value < minUsage && value !== 0) {
                this.value = minUsage;
            }
        });

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