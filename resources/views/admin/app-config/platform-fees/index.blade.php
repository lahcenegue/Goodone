@extends('admin.layouts')

@section('page-title', 'Platform Fees Configuration')
@section('page-subtitle', 'Configure your platform\'s revenue model and fee structure.')

@section('content')
<style>
    /* Platform Fees Styles */
    .fees-container {
        max-width: 100%;
    }

    .fees-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .fees-grid {
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

    .form-select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        background: white;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-group {
        display: flex;
        gap: 0.75rem;
        align-items: end;
    }

    .input-group .form-group {
        margin-bottom: 0;
        flex: 1;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .fee-preview {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .preview-title {
        color: #0c4a6e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .preview-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #bae6fd;
        text-align: center;
    }

    .preview-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0c4a6e;
        margin-bottom: 0.25rem;
    }

    .preview-label {
        font-size: 0.8rem;
        color: #0369a1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-modern {
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
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
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
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

    .analytics-section {
        grid-column: 1 / -1;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .trend-chart {
        height: 120px;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        align-items: end;
        gap: 0.5rem;
    }

    .trend-bar {
        flex: 1;
        background: #667eea;
        border-radius: 2px;
        min-height: 20px;
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

    .impact-calculator {
        background: #f0fdf4;
        border: 2px solid #22c55e;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .calculator-title {
        color: #166534;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .scenario-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .scenario-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #bbf7d0;
        text-align: center;
    }

    .scenario-amount {
        font-size: 0.8rem;
        color: #059669;
        margin-bottom: 0.25rem;
    }

    .scenario-fee {
        font-weight: 700;
        color: #065f46;
        margin-bottom: 0.25rem;
    }

    .scenario-total {
        font-size: 0.75rem;
        color: #047857;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .fees-grid {
            grid-template-columns: 1fr;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .preview-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .scenario-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="fees-container">
    <!-- Welcome Banner -->
    <div class="fees-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-dollar-sign me-2"></i>
                    Platform Fees Configuration
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Configure your platform's revenue model and fee structure
                </p>
            </div>
            <a href="{{ route('admin_fees_calculator') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-calculator"></i>
                Fee Calculator
            </a>
        </div>
    </div>

    <!-- Revenue Analytics -->
    @if(isset($analytics))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value" style="color: #667eea;">{{ number_format($analytics['current']['total_orders']) }}</div>
            <div class="stats-label">Total Orders</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #22c55e;">${{ number_format($analytics['current']['total_revenue'], 0) }}</div>
            <div class="stats-label">Total Revenue</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #f59e0b;">${{ number_format($analytics['current']['total_platform_fees'], 0) }}</div>
            <div class="stats-label">Platform Fees Collected</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #8b5cf6;">${{ number_format($analytics['current']['avg_platform_fee'], 2) }}</div>
            <div class="stats-label">Avg Fee per Order</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #ef4444;">${{ number_format($analytics['projections']['estimated_monthly_revenue'], 0) }}</div>
            <div class="stats-label">Est. Monthly Revenue</div>
        </div>
    </div>
    @endif

    <!-- Configuration Form -->
    <div class="fees-grid">
        <!-- Customer Fees Configuration -->
        <div class="form-card customer">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user" style="color: #22c55e;"></i>
                    Customer Fees (Combined)
                </h2>
                <p class="card-subtitle">Fixed amount + Percentage fee charged to customers</p>
            </div>

            <!-- REMOVE the <form> tags here - this is just for input collection -->
            <div class="form-group">
                <label for="customer_platform_fee" class="form-label">
                    Fixed Fee Amount ($) <span class="required">*</span>
                </label>
                <input
                    type="number"
                    id="customer_platform_fee"
                    name="customer_platform_fee"
                    class="form-input {{ $errors->has('customer_platform_fee') ? 'error' : '' }}"
                    value="{{ old('customer_platform_fee', $settings['customer_platform_fee']) }}"
                    required
                    min="0"
                    max="50"
                    step="0.01">
                <div class="help-text">Fixed dollar amount added to every order</div>
            </div>

            <div class="form-group">
                <label for="customer_platform_fee_percentage" class="form-label">
                    Percentage Fee (%) <span class="required">*</span>
                </label>
                <input
                    type="number"
                    id="customer_platform_fee_percentage"
                    name="customer_platform_fee_percentage"
                    class="form-input {{ $errors->has('customer_platform_fee_percentage') ? 'error' : '' }}"
                    value="{{ old('customer_platform_fee_percentage', $settings['customer_platform_fee_percentage']) }}"
                    required
                    min="0"
                    max="50"
                    step="0.01">
                <div class="help-text">Percentage of service cost added to order</div>
            </div>

            <div class="help-text" style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong>Combined Fee Example:</strong> For a $100 service with $5 fixed + 2.5% = Customer pays $107.50 total ($5 + $2.50 + $100)
            </div>
        </div>

        <!-- Provider Fees Configuration -->
        <div class="form-card provider">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user-tie" style="color: #f59e0b;"></i>
                    Provider Fees (Combined - Internal)
                </h2>
                <p class="card-subtitle">Fixed amount + Percentage fee deducted from provider earnings</p>
            </div>

            <!-- REMOVE the <form> tags here - this is just for input collection -->
            <div class="form-group">
                <label for="provider_platform_fee_fixed" class="form-label">
                    Fixed Fee Amount ($) <span class="required">*</span>
                </label>
                <input
                    type="number"
                    id="provider_platform_fee_fixed"
                    name="provider_platform_fee_fixed"
                    class="form-input {{ $errors->has('provider_platform_fee_fixed') ? 'error' : '' }}"
                    value="{{ old('provider_platform_fee_fixed', $settings['provider_platform_fee_fixed'] ?? 0) }}"
                    required
                    min="0"
                    max="50"
                    step="0.01">
                <div class="help-text">Fixed dollar amount deducted from each completed service</div>
            </div>

            <div class="form-group">
                <label for="provider_platform_fee_percentage" class="form-label">
                    Percentage Fee (%) <span class="required">*</span>
                </label>
                <input
                    type="number"
                    id="provider_platform_fee_percentage"
                    name="provider_platform_fee_percentage"
                    class="form-input {{ $errors->has('provider_platform_fee_percentage') ? 'error' : '' }}"
                    value="{{ old('provider_platform_fee_percentage', $settings['provider_platform_fee_percentage'] ?? 5.0) }}"
                    required
                    min="0"
                    max="50"
                    step="0.01">
                <div class="help-text">Percentage of service cost deducted from provider earnings</div>
            </div>

            <div class="help-text" style="background: #fef3c7; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong>Combined Fee Example:</strong> For a $100 service with $0 fixed + 5% = Provider loses $5 total, receives $95
            </div>

            <div class="help-text" style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong>Note:</strong> These fees are NOT shown to customers in the API. They're calculated internally when services are completed.
            </div>
        </div>
    </div>

    <!-- Live Preview -->
    <div class="fee-preview">
        <div class="preview-title">
            <i class="fas fa-eye"></i>
            Live Fee Preview (Based on $100 Service)
        </div>
        <div class="preview-grid">
            <div class="preview-item">
                <div class="preview-value" id="customer-pays">$102.50</div>
                <div class="preview-label">Customer Pays</div>
            </div>
            <div class="preview-item">
                <div class="preview-value" id="provider-receives">$95.00</div>
                <div class="preview-label">Provider Receives</div>
            </div>
            <div class="preview-item">
                <div class="preview-value" id="platform-revenue">$7.50</div>
                <div class="preview-label">Platform Revenue</div>
            </div>
            <div class="preview-item">
                <div class="preview-value" id="revenue-margin">7.3%</div>
                <div class="preview-label">Revenue Margin</div>
            </div>
        </div>
    </div>

    <!-- Impact Calculator -->
    <div class="impact-calculator">
        <div class="calculator-title">
            <i class="fas fa-chart-line"></i>
            Revenue Impact on Different Order Sizes
        </div>
        <div class="scenario-grid" id="impact-scenarios">
            <!-- Scenarios will be populated by JavaScript -->
        </div>
    </div>

    <!-- Warning about changes -->
    <div class="warning-box">
        <div class="warning-title">
            <i class="fas fa-exclamation-triangle"></i>
            Important Notice
        </div>
        <div class="warning-text">
            Fee changes will only apply to new orders. Existing orders will maintain their original fee structure to preserve transaction integrity.
        </div>
    </div>

    <!-- Master Form for Updates -->
    <form method="POST" action="{{ route('admin_update_platform_fees') }}" id="masterForm">
        @csrf
        <input type="hidden" id="master_customer_fee" name="customer_platform_fee" value="{{ $settings['customer_platform_fee'] }}">
        <input type="hidden" id="master_customer_percentage" name="customer_platform_fee_percentage" value="{{ $settings['customer_platform_fee_percentage'] }}">
        <input type="hidden" id="master_provider_fee_fixed" name="provider_platform_fee_fixed" value="{{ $settings['provider_platform_fee_fixed'] ?? 0 }}">
        <input type="hidden" id="master_provider_percentage" name="provider_platform_fee_percentage" value="{{ $settings['provider_platform_fee_percentage'] ?? 5.0 }}">

        <div class="form-group">
            <label for="reason" class="form-label">
                Reason for Change <small>(optional)</small>
            </label>
            <textarea
                id="reason"
                name="reason"
                class="form-input"
                placeholder="Explain why you're changing the fee structure..."
                rows="2"
                maxlength="500">{{ old('reason') }}</textarea>
            <input type="hidden" id="master_reason" name="reason_hidden" value="">
            <div class="help-text">Optional note for audit trail and team communication.</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                <i class="fas fa-save"></i>
                Update Platform Fees
            </button>
            <a href="{{ route('admin_fees_calculator') }}" class="btn-modern btn-secondary">
                <i class="fas fa-calculator"></i>
                Fee Calculator
            </a>
            <form method="POST" action="{{ route('admin_reset_platform_fees') }}" style="display: inline;"
                onsubmit="return confirm('Reset to default fees?')">
                @csrf
                <button type="submit" class="btn-modern btn-warning">
                    <i class="fas fa-undo"></i>
                    Reset to Defaults
                </button>
            </form>
        </div>
    </form>

    <!-- Analytics Section -->
    @if(isset($analytics) && $analytics['current']['total_orders'] > 0)
    <div class="form-card analytics-section">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-analytics" style="color: #8b5cf6;"></i>
                Revenue Analytics
            </h2>
            <p class="card-subtitle">Platform fee collection trends and projections</p>
        </div>

        <div class="analytics-grid">
            <!-- Monthly Trend -->
            <div>
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Monthly Fee Collection</h4>
                <div class="trend-chart">
                    @foreach($analytics['monthly_trend'] as $month)
                    @php
                    $maxFees = max(1, max(array_column($analytics['monthly_trend'], 'fees')));
                    $height = ($month['fees'] / $maxFees) * 80;
                    @endphp
                    <div class="trend-bar" style="height: {{ $height }}px;" title="{{ $month['month'] }}: ${{ number_format($month['fees'], 0) }}"></div>
                    @endforeach
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.8rem; color: #718096;">
                    @foreach($analytics['monthly_trend'] as $month)
                    <span>{{ substr($month['month'], 0, 3) }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Projections -->
            <div>
                <h4 style="margin: 0 0 1rem 0; color: #4a5568;">Revenue Projections</h4>
                <div style="space-y: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 6px; margin-bottom: 0.75rem;">
                        <span style="color: #4a5568;">Est. Customer Fee per Order:</span>
                        <span style="font-weight: 600; color: #22c55e;">${{ number_format($analytics['projections']['estimated_customer_fee'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 6px; margin-bottom: 0.75rem;">
                        <span style="color: #4a5568;">Est. Provider Fee per Order:</span>
                        <span style="font-weight: 600; color: #f59e0b;">${{ number_format($analytics['projections']['estimated_provider_fee'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 6px; margin-bottom: 0.75rem;">
                        <span style="color: #4a5568;">Total Platform Revenue per Order:</span>
                        <span style="font-weight: 600; color: #667eea;">${{ number_format($analytics['projections']['estimated_platform_revenue'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px;">
                        <span style="color: #0c4a6e; font-weight: 600;">Est. Monthly Revenue:</span>
                        <span style="font-weight: 700; color: #0c4a6e;">${{ number_format($analytics['projections']['estimated_monthly_revenue'], 0) }}</span>
                    </div>
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
        // Initialize preview
        updatePreview();
        updateImpactScenarios();

        // Form submission handler
        const masterForm = document.getElementById('masterForm');
        const submitBtn = document.getElementById('submitBtn');

        if (masterForm && submitBtn) {
            masterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get all current values from individual forms
                const customerFeeFixed = parseFloat(document.getElementById('customer_platform_fee').value) || 0;
                const customerFeePercentage = parseFloat(document.getElementById('customer_platform_fee_percentage').value) || 0;
                const providerFeeFixed = parseFloat(document.getElementById('provider_platform_fee_fixed').value) || 0;
                const providerFeePercentage = parseFloat(document.getElementById('provider_platform_fee_percentage').value) || 0;
                const reason = document.getElementById('reason').value || '';

                // Update master form hidden inputs
                document.getElementById('master_customer_fee').value = customerFeeFixed;
                document.getElementById('master_customer_percentage').value = customerFeePercentage;
                document.getElementById('master_provider_fee_fixed').value = providerFeeFixed;
                document.getElementById('master_provider_percentage').value = providerFeePercentage;
                document.getElementById('master_reason').value = reason;

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Fees...';

                // Submit the form
                this.submit();
            });
        }

        // Real-time update handlers
        ['customer_platform_fee', 'customer_platform_fee_percentage', 'provider_platform_fee_fixed', 'provider_platform_fee_percentage'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', function() {
                    updatePreview();
                    updateImpactScenarios();
                });
            }
        });

        // Validation
        validateInputs();
    });

    function updatePreview() {
        const customerFeeFixed = parseFloat(document.getElementById('customer_platform_fee').value) || 0;
        const customerFeePercentage = parseFloat(document.getElementById('customer_platform_fee_percentage').value) || 0;
        const providerFeeFixed = parseFloat(document.getElementById('provider_platform_fee_fixed').value) || 0;
        const providerFeePercentage = parseFloat(document.getElementById('provider_platform_fee_percentage').value) || 0;

        const serviceCost = 100; // Base calculation on $100 service

        // Calculate COMBINED customer fee (fixed + percentage)
        const customerFeeFromPercentage = (serviceCost * customerFeePercentage) / 100;
        const customerFeeTotal = customerFeeFixed + customerFeeFromPercentage;

        // Calculate COMBINED provider fee (fixed + percentage)
        const providerFeeFromPercentage = (serviceCost * providerFeePercentage) / 100;
        const providerFeeTotal = providerFeeFixed + providerFeeFromPercentage;

        const customerPays = serviceCost + customerFeeTotal;
        const providerReceives = Math.max(0, serviceCost - providerFeeTotal);
        const platformRevenue = customerFeeTotal + providerFeeTotal;
        const revenueMargin = customerPays > 0 ? (platformRevenue / customerPays) * 100 : 0;

        // Update preview display
        document.getElementById('customer-pays').textContent = '$' + customerPays.toFixed(2);
        document.getElementById('provider-receives').textContent = '$' + providerReceives.toFixed(2);
        document.getElementById('platform-revenue').textContent = '$' + platformRevenue.toFixed(2);
        document.getElementById('revenue-margin').textContent = revenueMargin.toFixed(1) + '%';
    }

    function updateImpactScenarios() {
        const customerFeeFixed = parseFloat(document.getElementById('customer_platform_fee').value) || 0;
        const customerFeePercentage = parseFloat(document.getElementById('customer_platform_fee_percentage').value) || 0;
        const providerFeeFixed = parseFloat(document.getElementById('provider_platform_fee_fixed').value) || 0;
        const providerFeePercentage = parseFloat(document.getElementById('provider_platform_fee_percentage').value) || 0;

        const scenarios = [25, 50, 100, 200, 500, 1000];
        const container = document.getElementById('impact-scenarios');

        if (!container) return;

        container.innerHTML = '';

        scenarios.forEach(amount => {
            // Calculate COMBINED customer fees
            const customerFeeFromPercentage = (amount * customerFeePercentage) / 100;
            const customerFeeTotal = customerFeeFixed + customerFeeFromPercentage;

            // Calculate COMBINED provider fees
            const providerFeeFromPercentage = (amount * providerFeePercentage) / 100;
            const providerFeeTotal = providerFeeFixed + providerFeeFromPercentage;

            const totalPlatformRevenue = customerFeeTotal + providerFeeTotal;
            const customerTotal = amount + customerFeeTotal;
            const providerReceives = Math.max(0, amount - providerFeeTotal);

            const scenarioDiv = document.createElement('div');
            scenarioDiv.className = 'scenario-item';
            scenarioDiv.innerHTML = `
            <div class="scenario-amount">$${amount} Service</div>
            <div class="scenario-fee">$${totalPlatformRevenue.toFixed(2)} Platform Fee</div>
            <div class="scenario-total">Customer pays: $${customerTotal.toFixed(2)}</div>
            <div style="font-size: 0.75rem; color: #059669; margin-top: 0.25rem;">
                Provider gets: $${providerReceives.toFixed(2)}
            </div>
        `;

            container.appendChild(scenarioDiv);
        });
    }

    function validateInputs() {
        const inputs = ['customer_platform_fee', 'customer_platform_fee_percentage', 'provider_platform_fee_fixed', 'provider_platform_fee_percentage'];

        inputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('input', function() {
                    const value = parseFloat(this.value);
                    const isValid = !isNaN(value) && value >= 0 && value <= 50;

                    if (!isValid && this.value !== '') {
                        this.style.borderColor = '#ef4444';
                        this.style.backgroundColor = '#fef2f2';
                    } else {
                        this.style.borderColor = '#e2e8f0';
                        this.style.backgroundColor = '#fafafa';
                    }
                });

                input.addEventListener('blur', function() {
                    const value = parseFloat(this.value);
                    if (value > 50) {
                        this.value = '50';
                        showValidationMessage('Fee cannot exceed 50');
                    } else if (value < 0) {
                        this.value = '0';
                        showValidationMessage('Fee cannot be negative');
                    }
                    updatePreview();
                });
            }
        });
    }

    function showValidationMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #fef2f2;
        border: 2px solid #fecaca;
        color: #991b1b;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        z-index: 10000;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-width: 400px;
    `;
        messageDiv.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(messageDiv);

        setTimeout(() => {
            if (document.body.contains(messageDiv)) {
                document.body.removeChild(messageDiv);
            }
        }, 3000);
    }
</script>
@endpush