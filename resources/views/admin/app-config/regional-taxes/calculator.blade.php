@extends('admin.layouts')

@section('page-title', 'Tax Calculator')
@section('page-subtitle', 'Calculate taxes across all regions and preview order totals.')

@section('content')
<style>
    /* Tax Calculator Styles */
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
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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

    .result-card.canadian::before {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .region-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }

    .tax-rate {
        font-size: 0.875rem;
        color: #667eea;
        font-weight: 600;
    }

    .canadian-flag {
        width: 24px;
        height: 16px;
        background: linear-gradient(to right, #ff0000 33%, #ffffff 33%, #ffffff 66%, #ff0000 66%);
        border-radius: 2px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
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

    .calc-value.subtotal {
        color: #667eea;
        font-size: 1.1rem;
    }

    .calc-value.tax {
        color: #f59e0b;
    }

    .calc-value.total {
        color: #22c55e;
        font-size: 1.25rem;
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
    }
</style>

<div class="calculator-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_regional_taxes') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Regional Taxes
        </a>
    </div>

    <!-- Welcome Banner -->
    <div class="calculator-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="text-align: center;">
            <h2 style="color: white; margin: 0; font-size: 2rem; font-weight: 700;">
                <i class="fas fa-calculator me-2"></i>
                Tax Calculator
            </h2>
            <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1.1rem;">
                Calculate taxes across all regions and compare rates instantly
            </p>
        </div>
    </div>

    <!-- Calculator Form -->
    <div class="calculator-form">
        <h3 style="margin: 0 0 1.5rem 0; color: #2d3748; font-size: 1.5rem; font-weight: 600; text-align: center;">
            <i class="fas fa-dollar-sign" style="color: #22c55e;"></i>
            Enter Order Amount
        </h3>

        <form method="GET" action="{{ route('admin_tax_calculator') }}" id="calculatorForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="amount" class="form-label">
                        <i class="fas fa-shopping-cart"></i>
                        Order Subtotal
                    </label>
                    <div style="position: relative;">
                        <span class="input-prefix">$</span>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            class="form-input with-prefix"
                            placeholder="100.00"
                            value="{{ request('amount') }}"
                            min="0.01"
                            step="0.01"
                            oninput="updateCalculations()">
                    </div>
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn-modern btn-primary" style="width: 100%;">
                        <i class="fas fa-calculator"></i>
                        Calculate Taxes
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
            Tax Comparison Summary
        </h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value" style="color: #667eea;">${{ number_format(request('amount'), 2) }}</div>
                <div class="summary-label">Order Subtotal</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #22c55e;">{{ count($calculations) }}</div>
                <div class="summary-label">Regions</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #f59e0b;">{{ number_format(min(array_column($calculations, 'tax_rate')), 3) }}%</div>
                <div class="summary-label">Lowest Rate</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #ef4444;">{{ number_format(max(array_column($calculations, 'tax_rate')), 3) }}%</div>
                <div class="summary-label">Highest Rate</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #8b5cf6;">${{ number_format(min(array_column($calculations, 'total_amount')), 2) }}</div>
                <div class="summary-label">Lowest Total</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #06b6d4;">${{ number_format(max(array_column($calculations, 'total_amount')), 2) }}</div>
                <div class="summary-label">Highest Total</div>
            </div>
        </div>
    </div>

    <!-- Results Grid -->
    <div style="margin-bottom: 1rem;">
        <h3 style="margin: 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
            <i class="fas fa-receipt" style="color: #22c55e;"></i>
            Tax Calculations by Region
        </h3>
        <p style="color: #718096; margin: 0.5rem 0 0 0;">
            Showing tax calculations for ${{ number_format(request('amount'), 2) }} order across {{ count($calculations) }} regions
        </p>
    </div>

    <div class="results-grid">
        @php
        // Sort calculations by total amount for better comparison
        usort($calculations, function($a, $b) {
        return $a['total_amount'] <=> $b['total_amount'];
            });
            @endphp

            @foreach($calculations as $calc)
            @php
            $isCanadian = in_array($calc['region'], [
            'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick',
            'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia',
            'Nunavut', 'Ontario', 'Prince Edward Island', 'Quebec', 'Saskatchewan', 'Yukon'
            ]);
            @endphp
            <div class="result-card {{ $isCanadian ? 'canadian' : '' }}">
                <div class="result-header">
                    <div>
                        <div class="region-name">{{ $calc['region'] }}</div>
                        <div class="tax-rate">{{ number_format($calc['tax_rate'], 3) }}% tax rate</div>
                    </div>
                    @if($isCanadian)
                    <div class="canadian-flag">🍁</div>
                    @endif
                </div>

                <div class="calculation-display">
                    <div class="calc-row">
                        <span class="calc-label">Order Subtotal:</span>
                        <span class="calc-value subtotal">${{ number_format($calc['base_amount'], 2) }}</span>
                    </div>
                    <div class="calc-row">
                        <span class="calc-label">Tax ({{ number_format($calc['tax_rate'], 3) }}%):</span>
                        <span class="calc-value tax">+${{ number_format($calc['tax_amount'], 2) }}</span>
                    </div>
                    <div class="calc-row">
                        <span class="calc-label">Order Total:</span>
                        <span class="calc-value total">${{ number_format($calc['total_amount'], 2) }}</span>
                    </div>
                </div>

                <!-- Additional Info -->
                <div style="text-align: center; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                    <div style="font-size: 0.8rem; color: #718096;">
                        Customer pays <strong style="color: #22c55e;">${{ number_format($calc['tax_amount'], 2) }} more</strong> than base regions
                    </div>
                </div>
            </div>
            @endforeach
    </div>

    @elseif(request('amount'))
    <!-- No Results -->
    <div class="no-results">
        <div class="no-results-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="color: #4a5568; margin-bottom: 1rem;">No Tax Rates Configured</h3>
        <p style="margin-bottom: 2rem;">
            You need to add regional tax rates before using the calculator.
        </p>
        <a href="{{ route('admin_create_regional_tax_form') }}" class="btn-modern btn-primary">
            <i class="fas fa-plus"></i>
            Add Your First Tax Rate
        </a>
    </div>

    @else
    <!-- Initial State -->
    <div class="no-results">
        <div class="no-results-icon">
            <i class="fas fa-calculator"></i>
        </div>
        <h3 style="color: #4a5568; margin-bottom: 1rem;">Ready to Calculate</h3>
        <p style="margin-bottom: 2rem;">
            Enter an order amount above to see tax calculations across all your configured regions.
        </p>
        @if(count($regionalTaxes) == 0)
        <a href="{{ route('admin_create_regional_tax_form') }}" class="btn-modern btn-primary">
            <i class="fas fa-plus"></i>
            Add Tax Rates First
        </a>
        @endif
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('calculatorForm');
        const amountInput = document.getElementById('amount');

        // Auto-submit on Enter key
        amountInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });

        // Format input as user types
        amountInput.addEventListener('input', function() {
            // Remove any non-numeric characters except decimal point
            this.value = this.value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const parts = this.value.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            }
        });

        // Highlight result cards on hover
        const resultCards = document.querySelectorAll('.result-card');
        resultCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.borderColor = '#667eea';
            });

            card.addEventListener('mouseleave', function() {
                this.style.borderColor = '#e2e8f0';
            });
        });
    });

    // Set amount from quick buttons
    function setAmount(amount) {
        document.getElementById('amount').value = amount;
        document.getElementById('calculatorForm').submit();
    }

    // Update calculations in real-time (placeholder for future enhancement)
    function updateCalculations() {
        // This could be enhanced to show real-time calculations without form submission
        // For now, we'll rely on form submission for accuracy
    }
</script>
@endpush