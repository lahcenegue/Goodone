@extends('admin.layouts')

@section('page-title', 'Add New Regional Tax Rate')
@section('page-subtitle', 'Configure tax rates for provinces and regions.')

@section('content')
<style>
    /* Regional Tax Form Styles */
    .form-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
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

    .form-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .form-subtitle {
        color: #718096;
        font-size: 1rem;
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

    .preview-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .preview-header {
        text-align: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .preview-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .preview-subtitle {
        color: #718096;
        font-size: 0.875rem;
    }

    .tax-display {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .display-region {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .display-percentage {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }

    .canadian-provinces {
        background: #fef3c7;
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .provinces-title {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .provinces-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
        max-height: 200px;
        overflow-y: auto;
    }

    .province-option {
        padding: 0.5rem 0.75rem;
        background: white;
        border: 1px solid #fbbf24;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .province-option:hover {
        background: #fef3c7;
        border-color: #f59e0b;
    }

    .province-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f3f4f6;
    }

    .province-name {
        font-weight: 600;
        color: #92400e;
        font-size: 0.875rem;
    }

    .province-rate {
        color: #d97706;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .calculation-preview {
        margin-bottom: 1.5rem;
    }

    .calc-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f7fafc;
    }

    .calc-item:last-child {
        border-bottom: none;
    }

    .calc-label {
        color: #718096;
        font-size: 0.875rem;
    }

    .calc-value {
        color: #2d3748;
        font-weight: 600;
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
        min-width: 140px;
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

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
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
        border-color: #cbd5e0;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .required {
        color: #ef4444;
    }

    .help-text {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    .suggestion-section {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .suggestion-title {
        color: #0c4a6e;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .suggestion-text {
        color: #0369a1;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .preview-card {
            position: static;
            order: -1;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-modern {
            width: 100%;
        }
    }
</style>

<div class="form-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_regional_taxes') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Regional Taxes
        </a>
    </div>

    <!-- Form Grid -->
    <div class="form-grid">
        <!-- Form Card -->
        <div class="form-card">
            <!-- Form Header -->
            <div class="form-header">
                <h1 class="form-title">
                    <i class="fas fa-plus-circle" style="color: #667eea;"></i>
                    Add Regional Tax Rate
                </h1>
                <p class="form-subtitle">
                    Configure tax rates for provinces, states, or custom regions
                </p>
            </div>

            <!-- Tax Form -->
            <form method="POST" action="{{ route('admin_store_regional_tax') }}" id="taxForm">
                @csrf

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
                        placeholder="Enter region or province name"
                        value="{{ old('region') }}"
                        required
                        maxlength="255"
                        oninput="updatePreview()">
                    <div class="help-text">Enter the full name of the province, state, or region (e.g., "Ontario", "California", "British Columbia")</div>
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
                            placeholder="Enter tax percentage"
                            value="{{ old('percentage') }}"
                            required
                            min="0"
                            max="50"
                            step="0.001"
                            oninput="updatePreview()">
                        <span class="input-suffix">%</span>
                    </div>
                    <div class="help-text">Enter tax rate as a percentage (0-50%). You can use up to 3 decimal places for precision.</div>
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
                        placeholder="Enter a description for this tax rate (for internal reference)"
                        rows="3"
                        maxlength="500"
                        oninput="updatePreview()">{{ old('description') }}</textarea>
                    <div class="help-text">Optional internal description to help you identify this tax rate.</div>
                    @error('description')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Suggestion for accuracy -->
                <div class="suggestion-section">
                    <div class="suggestion-title">
                        <i class="fas fa-lightbulb"></i>
                        Tax Rate Accuracy Tip
                    </div>
                    <div class="suggestion-text">
                        Make sure to use the combined tax rate (GST/HST + Provincial tax) for Canadian provinces.
                        For example, Ontario's combined rate is 13% (5% GST + 8% PST).
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Create Tax Rate
                    </button>
                    <a href="{{ route('admin_get_regional_taxes') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Preview & Quick Setup Card -->
        <div class="preview-card">
            <div class="preview-header">
                <h3 class="preview-title">
                    <i class="fas fa-eye"></i>
                    Live Preview
                </h3>
                <p class="preview-subtitle">See how your tax rate will look</p>
            </div>

            <!-- Tax Display -->
            <div class="tax-display">
                <div class="display-region" id="preview-region">Your Region</div>
                <div class="display-percentage">
                    <span id="preview-percentage">0</span>%
                </div>
                <div style="font-size: 0.875rem; opacity: 0.9;" id="preview-desc">Tax Rate</div>
            </div>

            <!-- Calculation Preview -->
            <div class="calculation-preview">
                <h4 style="margin: 0 0 1rem 0; color: #4a5568; font-size: 1rem;">Sample Calculations</h4>
                <div class="calc-item">
                    <span class="calc-label">$50 order:</span>
                    <span class="calc-value">$<span id="calc-50">0.00</span> tax</span>
                </div>
                <div class="calc-item">
                    <span class="calc-label">$100 order:</span>
                    <span class="calc-value">$<span id="calc-100">0.00</span> tax</span>
                </div>
                <div class="calc-item">
                    <span class="calc-label">$200 order:</span>
                    <span class="calc-value">$<span id="calc-200">0.00</span> tax</span>
                </div>
                <div class="calc-item" style="border-top: 2px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.75rem;">
                    <span class="calc-label" style="font-weight: 600;">$100 total with tax:</span>
                    <span class="calc-value" style="color: #667eea; font-weight: 700;">$<span id="calc-total">100.00</span></span>
                </div>
            </div>

            <!-- Canadian Provinces Quick Select -->
            @if(isset($canadianProvinces) && count($canadianProvinces) > 0)
            <div class="canadian-provinces">
                <div class="provinces-title">
                    <i class="fas fa-maple-leaf" style="color: #ef4444;"></i>
                    Canadian Provinces
                </div>
                <div style="margin-bottom: 1rem; color: #92400e; font-size: 0.8rem;">
                    Click any province to auto-fill the form with standard rates:
                </div>
                <div class="provinces-grid">
                    @foreach($canadianProvinces as $province => $rate)
                    <div class="province-option {{ in_array($province, $existingRegions ?? []) ? 'disabled' : '' }}"
                        onclick="{{ in_array($province, $existingRegions ?? []) ? '' : 'fillProvince(\'' . $province . '\', ' . $rate . ')' }}">
                        <span class="province-name">{{ $province }}</span>
                        <span class="province-rate">{{ $rate }}%</span>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top: 0.75rem; color: #92400e; font-size: 0.75rem;">
                    <i class="fas fa-info-circle"></i>
                    Grayed out provinces are already configured
                </div>
            </div>
            @endif

            <!-- Quick Tip -->
            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #667eea;">
                <h4 style="margin: 0 0 0.5rem 0; color: #4a5568; font-size: 0.9rem;">
                    <i class="fas fa-lightbulb"></i>
                    Quick Tip
                </h4>
                <p style="margin: 0; color: #718096; font-size: 0.8rem; line-height: 1.4;">
                    Tax rates are applied to the order total during checkout. Make sure to use the correct combined rate for your region.
                </p>
            </div>
        </div>
    </div>
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Tax Rate...';
        });

        // Initialize preview
        updatePreview();
    });

    // Update live preview
    function updatePreview() {
        const region = document.getElementById('region').value || 'Your Region';
        const percentage = parseFloat(document.getElementById('percentage').value) || 0;
        const description = document.getElementById('description').value || 'Tax Rate';

        // Update display
        document.getElementById('preview-region').textContent = region;
        document.getElementById('preview-percentage').textContent = percentage.toFixed(percentage % 1 === 0 ? 0 : 3);
        document.getElementById('preview-desc').textContent = description;

        // Update calculations
        const calc50 = (50 * percentage / 100).toFixed(2);
        const calc100 = (100 * percentage / 100).toFixed(2);
        const calc200 = (200 * percentage / 100).toFixed(2);
        const calcTotal = (100 + (100 * percentage / 100)).toFixed(2);

        document.getElementById('calc-50').textContent = calc50;
        document.getElementById('calc-100').textContent = calc100;
        document.getElementById('calc-200').textContent = calc200;
        document.getElementById('calc-total').textContent = calcTotal;
    }

    // Fill form with Canadian province data
    function fillProvince(province, rate) {
        document.getElementById('region').value = province;
        document.getElementById('percentage').value = rate;
        document.getElementById('description').value = `Standard ${province} tax rate (GST/HST + Provincial tax)`;
        updatePreview();

        // Highlight the selected province temporarily
        event.target.style.background = '#dcfce7';
        event.target.style.borderColor = '#22c55e';
        setTimeout(() => {
            event.target.style.background = 'white';
            event.target.style.borderColor = '#fbbf24';
        }, 1000);
    }

    // Validate percentage input
    document.getElementById('percentage').addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value > 50) this.value = '50';
        if (value < 0) this.value = '0';
    });

    // Format region name as user types
    document.getElementById('region').addEventListener('input', function() {
        // Capitalize first letter of each word
        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
    });
</script>
@endpush