@extends('admin.layouts')

@section('page-title', 'Platform Fees Calculator')
@section('page-subtitle', 'Calculate fees across different service amounts and scenarios.')

@section('content')
<style>
    /* Calculator Styles - matching your design pattern */
    .calculator-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .calculator-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .calculator-form {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .calculator-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group {
        position: relative;
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
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .input-prefix {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        font-weight: 700;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .form-input.with-prefix {
        padding-left: 2.5rem;
    }

    .btn-modern {
        padding: 1rem 2rem;
        border-radius: 12px;
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

    .quick-amounts {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .amount-btn {
        padding: 0.5rem 1rem;
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        border-radius: 8px;
        color: #4338ca;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .amount-btn:hover {
        background: #e0e7ff;
        border-color: #a5b4fc;
        transform: translateY(-1px);
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .result-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .result-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .result-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .result-card.current-settings::before {
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .result-card.better-scenario::before {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .result-card.worse-scenario::before {
        background: linear-gradient(90deg, #f59e0b, #ea580c);
    }

    .scenario-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .scenario-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a202c;
    }

    .scenario-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-current {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-better {
        background: #dcfce7;
        color: #166534;
    }

    .badge-alternative {
        background: #fef3c7;
        color: #92400e;
    }

    .calculation-display {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }

    .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .calc-row:last-child {
        margin-bottom: 0;
        padding-top: 0.75rem;
        border-top: 2px solid #e2e8f0;
        font-weight: 700;
    }

    .calc-label {
        color: #4a5568;
        font-size: 0.9rem;
    }

    .calc-value {
        font-weight: 700;
        color: #2d3748;
        font-size: 1rem;
    }

    .calc-value.service {
        color: #667eea;
    }

    .calc-value.customer-fee {
        color: #22c55e;
    }

    .calc-value.provider-fee {
        color: #f59e0b;
    }

    .calc-value.total {
        color: #ef4444;
        font-size: 1.1rem;
    }

    .calc-value.platform-revenue {
        color: #8b5cf6;
        font-size: 1.1rem;
    }

    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        color: #718096;
        grid-column: 1 / -1;
    }

    .no-results-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .comparison-summary {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        text-align: center;
    }

    .summary-item {
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .summary-label {
        color: #718096;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .settings-display {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .settings-title {
        color: #0c4a6e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .setting-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #bae6fd;
    }

    .setting-label {
        font-size: 0.8rem;
        color: #0369a1;
        margin-bottom: 0.25rem;
    }

    .setting-value {
        font-weight: 700;
        color: #0c4a6e;
    }

    .impact-summary {
        text-align: center;
        padding: 1rem;
        background: #f1f5f9;
        border-radius: 8px;
        margin-top: 1rem;
        border: 1px solid #e2e8f0;
    }

    .impact-text {
        font-size: 0.85rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .impact-highlight {
        font-weight: 700;
        color: #667eea;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .results-grid {
            grid-template-columns: 1fr;
        }

        .quick-amounts {
            justify-content: center;
        }

        .amount-btn {
            flex: 1;
            min-width: 80px;
        }

        .settings-grid {
            grid-template-columns: 1fr;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="calculator-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_platform_fees') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Platform Fees
        </a>
    </div>

    <!-- Welcome Banner -->
    <div class="calculator-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="text-align: center;">
            <h2 style="color: white; margin: 0; font-size: 2rem; font-weight: 700;">
                <i class="fas fa-calculator me-2"></i>
                Platform Fees Calculator
            </h2>
            <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1.1rem;">
                Calculate fees across different service amounts and compare scenarios
            </p>
        </div>
    </div>

    <!-- Current Settings Display -->
    <div class="settings-display">
        <div class="settings-title">
            <i class="fas fa-cog"></i>
            Current Fee Settings (Combined Fixed + Percentage)
        </div>
        <div class="settings-grid">
            <div class="setting-item">
                <div class="setting-label">Customer Fees</div>
                <div class="setting-value">
                    ${{ $settings['customer_platform_fee'] ?? 0 }} + {{ $settings['customer_platform_fee_percentage'] ?? 2.5 }}%
                </div>
            </div>
            <div class="setting-item">
                <div class="setting-label">Provider Fees (Internal)</div>
                <div class="setting-value">
                    ${{ $settings['provider_platform_fee_fixed'] ?? 0 }} + {{ $settings['provider_platform_fee_percentage'] ?? 5.0 }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Calculator Form -->
    <div class="calculator-form">
        <h3 style="margin: 0 0 1.5rem 0; color: #2d3748; font-size: 1.5rem; font-weight: 600; text-align: center;">
            <i class="fas fa-dollar-sign" style="color: #22c55e;"></i>
            Enter Service Amount
        </h3>

        <form method="GET" action="{{ route('admin_fees_calculator') }}" id="calculatorForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="service_cost" class="form-label">
                        <i class="fas fa-briefcase"></i>
                        Service Cost
                    </label>
                    <div style="position: relative;">
                        <span class="input-prefix">$</span>
                        <input
                            type="number"
                            id="service_cost"
                            name="service_cost"
                            class="form-input with-prefix"
                            placeholder="100.00"
                            value="{{ request('service_cost') }}"
                            min="0.01"
                            step="0.01"
                            oninput="updateLiveCalculations()">
                    </div>
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn-modern btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Calculate Scenarios
                    </button>
                </div>
            </div>

            <!-- Quick Amount Buttons -->
            <div style="text-align: center;">
                <div style="margin-bottom: 0.5rem; color: #718096; font-size: 0.9rem;">Quick amounts:</div>
                <div class="quick-amounts" style="justify-content: center;">
                    <button type="button" class="amount-btn" onclick="setAmount(25)">$25</button>
                    <button type="button" class="amount-btn" onclick="setAmount(50)">$50</button>
                    <button type="button" class="amount-btn" onclick="setAmount(100)">$100</button>
                    <button type="button" class="amount-btn" onclick="setAmount(200)">$200</button>
                    <button type="button" class="amount-btn" onclick="setAmount(500)">$500</button>
                    <button type="button" class="amount-btn" onclick="setAmount(1000)">$1000</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Section -->
    @if(isset($calculations) && count($calculations) > 0)

    <!-- Comparison Summary -->
    <div class="comparison-summary">
        <h3 style="margin: 0 0 1.5rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600; text-align: center;">
            <i class="fas fa-chart-bar" style="color: #8b5cf6;"></i>
            Fee Comparison Summary for ${{ number_format(request('service_cost'), 2) }} Service
        </h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value" style="color: #667eea;">${{ number_format(request('service_cost'), 2) }}</div>
                <div class="summary-label">Service Cost</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #22c55e;">{{ count($calculations) }}</div>
                <div class="summary-label">Scenarios</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #f59e0b;">${{ number_format(min(array_column($calculations, 'customer_fee')), 2) }}</div>
                <div class="summary-label">Min Customer Fee</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #ef4444;">${{ number_format(max(array_column($calculations, 'customer_fee')), 2) }}</div>
                <div class="summary-label">Max Customer Fee</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #8b5cf6;">${{ number_format(min(array_column($calculations, 'platform_revenue')), 2) }}</div>
                <div class="summary-label">Min Platform Revenue</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #06b6d4;">${{ number_format(max(array_column($calculations, 'platform_revenue')), 2) }}</div>
                <div class="summary-label">Max Platform Revenue</div>
            </div>
        </div>
    </div>

    <!-- Results Grid -->
    <div style="margin-bottom: 1rem;">
        <h3 style="margin: 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
            <i class="fas fa-receipt" style="color: #22c55e;"></i>
            Fee Calculations by Scenario
        </h3>
        <p style="color: #718096; margin: 0.5rem 0 0 0;">
            Comparing current settings with alternative fee structures for ${{ number_format(request('service_cost'), 2) }} service
        </p>
    </div>

    <div class="results-grid">
        @foreach($calculations as $index => $calc)
        @php
            $cardClass = '';
            $badgeClass = 'badge-alternative';
            $badgeText = 'Alternative';
            
            if ($calc['scenario_name'] === 'Current Settings') {
                $cardClass = 'current-settings';
                $badgeClass = 'badge-current';
                $badgeText = 'Current';
            } elseif (strpos($calc['scenario_name'], 'Lower') !== false) {
                $cardClass = 'better-scenario';
                $badgeClass = 'badge-better';
                $badgeText = 'Lower Fees';
            } else {
                $cardClass = 'worse-scenario';
            }
        @endphp
        
        <div class="result-card {{ $cardClass }}">
            <div class="scenario-header">
                <div class="scenario-name">{{ $calc['scenario_name'] }}</div>
                <div class="scenario-badge {{ $badgeClass }}">{{ $badgeText }}</div>
            </div>

            <div class="calculation-display">
                <div class="calc-row">
                    <span class="calc-label">Service Cost:</span>
                    <span class="calc-value service">${{ number_format($calc['service_cost'], 2) }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Customer Fee:</span>
                    <span class="calc-value customer-fee">+${{ number_format($calc['customer_fee'], 2) }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Provider Fee:</span>
                    <span class="calc-value provider-fee">-${{ number_format($calc['provider_fee'], 2) }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Customer Pays:</span>
                    <span class="calc-value total">${{ number_format($calc['customer_pays'], 2) }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label">Provider Receives:</span>
                    <span class="calc-value">${{ number_format($calc['provider_receives'], 2) }}</span>
                </div>
                <div class="calc-row">
                    <span class="calc-label"><strong>Platform Revenue:</strong></span>
                    <span class="calc-value platform-revenue">${{ number_format($calc['platform_revenue'], 2) }}</span>
                </div>
            </div>

            <!-- Impact Summary -->
            <div class="impact-summary">
                <div class="impact-text">
                    Platform margin: <span class="impact-highlight">{{ number_format($calc['platform_margin'], 1) }}%</span>
                </div>
                <div class="impact-text" style="font-size: 0.8rem;">
                    Customer Fee: {{ number_format($calc['customer_fee_percentage'], 1) }}% • 
                    Provider Fee: {{ number_format($calc['provider_fee_percentage'], 1) }}%
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    <!-- No Results / Initial State -->
    <div class="no-results">
        <div class="no-results-icon">
            <i class="fas fa-calculator"></i>
        </div>
        <h3 style="color: #4a5568; margin-bottom: 1rem;">Ready to Calculate Fees</h3>
        <p style="margin-bottom: 2rem;">
            Enter a service amount above to see fee calculations across different scenarios with your current settings.
            <br><br>
            <strong>Current Settings:</strong><br>
            Customer Fees: ${{ $settings['customer_platform_fee'] ?? 0 }} + {{ $settings['customer_platform_fee_percentage'] ?? 2.5 }}%<br>
            Provider Fees: ${{ $settings['provider_platform_fee_fixed'] ?? 0 }} + {{ $settings['provider_platform_fee_percentage'] ?? 5.0 }}%
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <button class="btn-modern btn-primary" onclick="setAmount(100)">
                <i class="fas fa-play"></i>
                Try $100 Example
            </button>
            <a href="{{ route('admin_get_platform_fees') }}" class="btn-modern btn-secondary">
                <i class="fas fa-cog"></i>
                Adjust Fee Settings
            </a>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('calculatorForm');
        const serviceInput = document.getElementById('service_cost');

        // Auto-submit on Enter key
        serviceInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });

        // Format input as user types
        serviceInput.addEventListener('input', function() {
            // Remove any non-numeric characters except decimal point
            this.value = this.value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = this.value.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Limit to 2 decimal places
            if (parts[1] && parts[1].length > 2) {
                this.value = parts[0] + '.' + parts[1].substring(0, 2);
            }
        });

        // Highlight result cards on hover
        const resultCards = document.querySelectorAll('.result-card');
        resultCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.borderColor = '#667eea';
                this.style.transform = 'translateY(-4px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.borderColor = '#e2e8f0';
                this.style.transform = 'translateY(-2px)';
            });
        });

        // Form submission loading state
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculating...';
            }
        });

        // Initialize with current value if exists
        if (serviceInput.value) {
            updateLiveCalculations();
        }
    });

    // Set amount from quick buttons
    function setAmount(amount) {
        const input = document.getElementById('service_cost');
        input.value = amount;
        input.focus();
        updateLiveCalculations();
        
        // Auto-submit after short delay
        setTimeout(() => {
            document.getElementById('calculatorForm').submit();
        }, 500);
    }

    // Live calculations preview (optional enhancement)
    function updateLiveCalculations() {
        const amount = parseFloat(document.getElementById('service_cost').value);
        
        if (amount && amount > 0) {
            // Add subtle visual feedback
            const input = document.getElementById('service_cost');
            input.style.borderColor = '#22c55e';
            input.style.backgroundColor = '#f0fdf4';
            
            setTimeout(() => {
                input.style.borderColor = '#e2e8f0';
                input.style.backgroundColor = '#fafafa';
            }, 1000);
        }
    }
</script>
@endpush