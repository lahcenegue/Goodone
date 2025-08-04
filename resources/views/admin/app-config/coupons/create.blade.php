@extends('admin.layouts')

@section('page-title', 'Add New Coupon')
@section('page-subtitle', 'Create a new discount coupon to boost customer engagement.')

@section('content')
<style>
    /* Coupon Form Styles */
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

    .coupon-preview {
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

    .coupon-mockup {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .coupon-mockup::before {
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

    .coupon-mockup::after {
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

    .mockup-code {
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        margin-bottom: 0.5rem;
        word-break: break-all;
    }

    .mockup-discount {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0.5rem 0;
    }

    .mockup-description {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .preview-details {
        space-y: 1rem;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f7fafc;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #718096;
        font-size: 0.875rem;
    }

    .detail-value {
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

    .code-suggestions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .suggestion-btn {
        padding: 0.25rem 0.75rem;
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        border-radius: 6px;
        color: #4338ca;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .suggestion-btn:hover {
        background: #e0e7ff;
        border-color: #a5b4fc;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .coupon-preview {
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
        <a href="{{ route('admin_get_coupons') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Coupons
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
                    Create New Coupon
                </h1>
                <p class="form-subtitle">
                    Set up a discount coupon to attract customers and boost sales
                </p>
            </div>

            <!-- Coupon Form -->
            <form method="POST" action="{{ route('admin_store_coupon') }}" id="couponForm">
                @csrf

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
                        placeholder="Enter coupon code (e.g., SAVE20, WELCOME)"
                        value="{{ old('coupon') }}"
                        required
                        maxlength="50"
                        style="text-transform: uppercase;"
                        oninput="updatePreview()">
                    <div class="help-text">Use uppercase letters and numbers only. Keep it short and memorable.</div>
                    <div class="code-suggestions">
                        <span style="font-size: 0.8rem; color: #718096; margin-right: 0.5rem;">Quick suggestions:</span>
                        <button type="button" class="suggestion-btn" onclick="setSuggestion('SAVE20')">SAVE20</button>
                        <button type="button" class="suggestion-btn" onclick="setSuggestion('WELCOME10')">WELCOME10</button>
                        <button type="button" class="suggestion-btn" onclick="setSuggestion('FIRST15')">FIRST15</button>
                        <button type="button" class="suggestion-btn" onclick="generateRandom()">Generate Random</button>
                    </div>
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
                            placeholder="Enter discount percentage"
                            value="{{ old('percentage') }}"
                            required
                            min="1"
                            max="100"
                            step="0.01"
                            oninput="updatePreview()">
                        <span class="input-suffix">%</span>
                    </div>
                    <div class="help-text">Enter a value between 1% and 100%</div>
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
                        placeholder="Enter maximum number of uses"
                        value="{{ old('max_usage') }}"
                        required
                        min="0"
                        oninput="updatePreview()">
                    <div class="help-text">Set to 0 for unlimited usage. Otherwise, specify the maximum number of times this coupon can be used.</div>
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
                        placeholder="Enter a description for this coupon (for internal use)"
                        rows="3"
                        maxlength="500"
                        oninput="updatePreview()">{{ old('description') }}</textarea>
                    <div class="help-text">Optional internal description to help you remember what this coupon is for.</div>
                    @error('description')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Create Coupon
                    </button>
                    <a href="{{ route('admin_get_coupons') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Preview Card -->
        <div class="coupon-preview">
            <div class="preview-header">
                <h3 class="preview-title">
                    <i class="fas fa-eye"></i>
                    Live Preview
                </h3>
                <p class="preview-subtitle">See how your coupon will look</p>
            </div>

            <!-- Coupon Mockup -->
            <div class="coupon-mockup">
                <div class="mockup-code" id="preview-code">YOUR-COUPON</div>
                <div class="mockup-discount">
                    <span id="preview-percentage">0</span>% OFF
                </div>
                <div class="mockup-description" id="preview-desc">Discount Coupon</div>
            </div>

            <!-- Preview Details -->
            <div class="preview-details">
                <div class="detail-item">
                    <span class="detail-label">Code:</span>
                    <span class="detail-value" id="detail-code">-</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Discount:</span>
                    <span class="detail-value" id="detail-percentage">0%</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Max Usage:</span>
                    <span class="detail-value" id="detail-usage">0</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" style="color: #22c55e;">Ready to Create</span>
                </div>
            </div>

            <!-- Usage Examples -->
            <div style="margin-top: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                <h4 style="margin: 0 0 0.5rem 0; color: #4a5568; font-size: 0.9rem;">Usage Examples:</h4>
                <div style="font-size: 0.8rem; color: #718096; line-height: 1.4;">
                    • Customer enters code at checkout<br>
                    • <span id="example-savings">$0</span> saved on a $100 order<br>
                    • Can be used <span id="example-times">0</span> times
                </div>
            </div>
        </div>
    </div>
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Coupon...';
        });

        // Initialize preview
        updatePreview();
    });

    // Update live preview
    function updatePreview() {
        const code = document.getElementById('coupon').value.toUpperCase() || 'YOUR-COUPON';
        const percentage = document.getElementById('percentage').value || '0';
        const maxUsage = document.getElementById('max_usage').value || '0';
        const description = document.getElementById('description').value || 'Discount Coupon';

        // Update mockup
        document.getElementById('preview-code').textContent = code;
        document.getElementById('preview-percentage').textContent = percentage;
        document.getElementById('preview-desc').textContent = description;

        // Update details
        document.getElementById('detail-code').textContent = code;
        document.getElementById('detail-percentage').textContent = percentage + '%';
        document.getElementById('detail-usage').textContent = maxUsage == '0' ? 'Unlimited' : maxUsage;

        // Update examples
        const savings = (100 * parseFloat(percentage || 0) / 100).toFixed(2);
        document.getElementById('example-savings').textContent = '$' + savings;
        document.getElementById('example-times').textContent = maxUsage == '0' ? 'unlimited' : maxUsage;
    }

    // Set suggestion
    function setSuggestion(code) {
        document.getElementById('coupon').value = code;
        updatePreview();
    }

    // Generate random coupon code
    function generateRandom() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 8; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('coupon').value = result;
        updatePreview();
    }

    // Force uppercase input
    document.getElementById('coupon').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Validate percentage input
    document.getElementById('percentage').addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value > 100) this.value = '100';
        if (value < 0) this.value = '0';
    });

    // Validate max usage input
    document.getElementById('max_usage').addEventListener('input', function() {
        const value = parseInt(this.value);
        if (value < 0) this.value = '0';
    });
</script>
@endpush