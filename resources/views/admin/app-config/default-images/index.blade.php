@extends('admin.layouts')

@section('page-title', 'Default Images Management')
@section('page-subtitle', 'Configure default profile images for customers and service providers.')

@section('content')
<style>
    /* Default Images Management Styles */
    .images-container {
        max-width: 100%;
    }

    .images-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .images-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .image-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .image-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .image-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .image-card.customer::before {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    .image-card.provider::before {
        background: linear-gradient(90deg, #f59e0b, #ea580c);
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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .card-subtitle {
        color: #718096;
        font-size: 0.95rem;
    }

    .image-preview {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .current-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid #e2e8f0;
        object-fit: cover;
        margin: 0 auto 1rem auto;
        display: block;
        transition: all 0.3s ease;
    }

    .current-image:hover {
        border-color: #667eea;
        transform: scale(1.05);
    }

    .image-info {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-label {
        color: #4a5568;
    }

    .info-value {
        color: #2d3748;
        font-weight: 600;
    }

    .upload-section {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .upload-section.dragover {
        border-color: #667eea;
        background-color: #f0f4ff;
    }

    .upload-section:hover {
        border-color: #667eea;
        background-color: #fafbff;
    }

    .upload-icon {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .upload-text {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .upload-subtext {
        color: #718096;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
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

    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #ea580c);
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #ea580c, #dc2626);
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

    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
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

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .success-message {
        color: #22c55e;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    .preview-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .preview-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        text-align: center;
    }

    .preview-image {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        margin: 1rem auto;
        border: 4px solid #e2e8f0;
    }

    .help-text {
        font-size: 0.8rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .images-grid {
            grid-template-columns: 1fr;
        }

        .actions-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="images-container">
    <!-- Welcome Banner -->
    <div class="images-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="text-align: center;">
            <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                <i class="fas fa-image me-2"></i>
                Default Images Management
            </h2>
            <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                Configure default profile images for customers and service providers
            </p>
        </div>
    </div>

    <!-- Usage Statistics -->
    @if(isset($analytics))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value" style="color: #22c55e;">{{ number_format($analytics['usage']['total_customers']) }}</div>
            <div class="stats-label">Total Customers</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #f59e0b;">{{ number_format($analytics['usage']['total_providers']) }}</div>
            <div class="stats-label">Total Providers</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #667eea;">{{ number_format($analytics['usage']['customers_using_default']) }}</div>
            <div class="stats-label">Using Default Customer Image</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #8b5cf6;">{{ number_format($analytics['usage']['providers_using_default']) }}</div>
            <div class="stats-label">Using Default Provider Image</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #ef4444;">{{ number_format($analytics['usage']['customer_custom_percentage'], 1) }}%</div>
            <div class="stats-label">Customers with Custom Images</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="color: #06b6d4;">{{ number_format($analytics['usage']['provider_custom_percentage'], 1) }}%</div>
            <div class="stats-label">Providers with Custom Images</div>
        </div>
    </div>
    @endif

    <!-- Important Notice -->
    <div class="warning-box">
        <div class="warning-title">
            <i class="fas fa-info-circle"></i>
            Important Notice
        </div>
        <div class="warning-text">
            Default images are used when users haven't uploaded their own profile pictures. Changes will apply immediately to all users currently using default images. Recommended image size: 400x400px, max 2MB.
        </div>
    </div>

    <!-- Image Management Grid -->
    <div class="images-grid">
        <!-- Customer Default Image -->
        <div class="image-card customer">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user" style="color: #22c55e;"></i>
                    Customer Default Image
                </h2>
                <p class="card-subtitle">Default profile image for customer accounts</p>
            </div>

            <!-- Current Image Preview -->
            <div class="image-preview">
                <img
                    src="{{ asset('storage/images/' . ($settings['customer_image'] ?? 'default-customer.png')) }}"
                    alt="Customer Default Image"
                    class="current-image"
                    onerror="this.src='{{ asset('images/default-customer.png') }}'">
            </div>

            <!-- Image Information -->
            <div class="image-info">
                <div class="info-row">
                    <span class="info-label">Current Image:</span>
                    <span class="info-value">{{ $settings['customer_image'] ?? 'default-customer.png' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value">
                        {{ $settings['customer_image_updated'] ? \Carbon\Carbon::parse($settings['customer_image_updated'])->format('M d, Y H:i') : 'Never' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Users Using This:</span>
                    <span class="info-value">{{ isset($analytics) ? number_format($analytics['usage']['customers_using_default']) : '0' }}</span>
                </div>
            </div>

            <!-- Upload Section -->
            <form method="POST" action="{{ route('admin_update_default_images') }}" enctype="multipart/form-data" id="customerImageForm">
                @csrf
                <div class="upload-section" id="customerUpload">
                    <input type="file" name="customer_image" class="file-input" id="customerImageInput" accept="image/*">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Click or drag to upload new customer image</div>
                    <div class="upload-subtext">Supports: JPEG, PNG, GIF, WebP (Max: 2MB)</div>
                    <button type="button" class="btn-modern btn-primary" onclick="document.getElementById('customerImageInput').click()">
                        <i class="fas fa-upload"></i>
                        Choose Image
                    </button>
                </div>

                <div class="form-group">
                    <label for="customer_reason" class="form-label">
                        Reason for Change <small>(optional)</small>
                    </label>
                    <input
                        type="text"
                        id="customer_reason"
                        name="reason"
                        class="form-input"
                        placeholder="e.g., Improved branding, better quality image..."
                        maxlength="500">
                    <div class="help-text">Optional note for audit trail.</div>
                </div>

                <div class="actions-grid">
                    <button type="submit" class="btn-modern btn-success" id="customerSubmitBtn" disabled>
                        <i class="fas fa-save"></i>
                        Update Customer Image
                    </button>
                    <button type="button" class="btn-modern btn-warning" onclick="resetImage('customer')">
                        <i class="fas fa-undo"></i>
                        Reset to Default
                    </button>
                </div>
            </form>
        </div>

        <!-- Provider Default Image -->
        <div class="image-card provider">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user-tie" style="color: #f59e0b;"></i>
                    Provider Default Image
                </h2>
                <p class="card-subtitle">Default profile image for service provider accounts</p>
            </div>

            <!-- Current Image Preview -->
            <div class="image-preview">
                <img
                    src="{{ asset('storage/images/' . ($settings['provider_image'] ?? 'default-provider.png')) }}"
                    alt="Provider Default Image"
                    class="current-image"
                    onerror="this.src='{{ asset('images/default-provider.png') }}'">
            </div>

            <!-- Image Information -->
            <div class="image-info">
                <div class="info-row">
                    <span class="info-label">Current Image:</span>
                    <span class="info-value">{{ $settings['provider_image'] ?? 'default-provider.png' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value">
                        {{ $settings['provider_image_updated'] ? \Carbon\Carbon::parse($settings['provider_image_updated'])->format('M d, Y H:i') : 'Never' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Users Using This:</span>
                    <span class="info-value">{{ isset($analytics) ? number_format($analytics['usage']['providers_using_default']) : '0' }}</span>
                </div>
            </div>

            <!-- Upload Section -->
            <form method="POST" action="{{ route('admin_update_default_images') }}" enctype="multipart/form-data" id="providerImageForm">
                @csrf
                <div class="upload-section" id="providerUpload">
                    <input type="file" name="provider_image" class="file-input" id="providerImageInput" accept="image/*">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Click or drag to upload new provider image</div>
                    <div class="upload-subtext">Supports: JPEG, PNG, GIF, WebP (Max: 2MB)</div>
                    <button type="button" class="btn-modern btn-primary" onclick="document.getElementById('providerImageInput').click()">
                        <i class="fas fa-upload"></i>
                        Choose Image
                    </button>
                </div>

                <div class="form-group">
                    <label for="provider_reason" class="form-label">
                        Reason for Change <small>(optional)</small>
                    </label>
                    <input
                        type="text"
                        id="provider_reason"
                        name="reason"
                        class="form-input"
                        placeholder="e.g., Improved branding, better quality image..."
                        maxlength="500">
                    <div class="help-text">Optional note for audit trail.</div>
                </div>

                <div class="actions-grid">
                    <button type="submit" class="btn-modern btn-success" id="providerSubmitBtn" disabled>
                        <i class="fas fa-save"></i>
                        Update Provider Image
                    </button>
                    <button type="button" class="btn-modern btn-warning" onclick="resetImage('provider')">
                        <i class="fas fa-undo"></i>
                        Reset to Default
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="text-align: center; margin-top: 2rem;">
        <button type="button" class="btn-modern btn-danger" onclick="resetImage('both')"
            style="margin-right: 1rem;">
            <i class="fas fa-undo-alt"></i>
            Reset Both Images to Defaults
        </button>
        <a href="{{ route('admin_get_customers') }}" class="btn-modern btn-secondary">
            <i class="fas fa-users"></i>
            View Customers
        </a>
    </div>
</div>

<!-- Preview Modal -->
<div class="preview-modal" id="previewModal">
    <div class="preview-content">
        <h3 style="margin-bottom: 1rem; color: #2d3748;">Image Preview</h3>
        <img id="previewImage" class="preview-image" src="" alt="Preview">
        <div id="previewInfo" style="margin: 1rem 0; color: #4a5568; font-size: 0.9rem;"></div>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" class="btn-modern btn-success" onclick="confirmUpload()">
                <i class="fas fa-check"></i>
                Use This Image
            </button>
            <button type="button" class="btn-modern btn-secondary" onclick="closePreview()">
                <i class="fas fa-times"></i>
                Cancel
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentUploadType = null;
    let currentFile = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize file upload handlers
        initializeFileUploads();

        // Initialize drag and drop
        initializeDragAndDrop();

        // Form submission handlers
        initializeFormHandlers();
    });

    function initializeFileUploads() {
        // Customer image upload
        const customerInput = document.getElementById('customerImageInput');
        const customerSubmitBtn = document.getElementById('customerSubmitBtn');

        if (customerInput) {
            customerInput.addEventListener('change', function(e) {
                handleFileSelect(e, 'customer', customerSubmitBtn);
            });
        }

        // Provider image upload
        const providerInput = document.getElementById('providerImageInput');
        const providerSubmitBtn = document.getElementById('providerSubmitBtn');

        if (providerInput) {
            providerInput.addEventListener('change', function(e) {
                handleFileSelect(e, 'provider', providerSubmitBtn);
            });
        }
    }

    function initializeDragAndDrop() {
        const uploadSections = ['customerUpload', 'providerUpload'];

        uploadSections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (!section) return;

            section.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            section.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            section.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = this.querySelector('.file-input');
                    const submitBtn = sectionId.includes('customer') ?
                        document.getElementById('customerSubmitBtn') :
                        document.getElementById('providerSubmitBtn');

                    input.files = files;
                    handleFileSelect({
                        target: input
                    }, sectionId.replace('Upload', ''), submitBtn);
                }
            });
        });
    }

    function handleFileSelect(event, type, submitBtn) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file
        if (!validateFile(file)) {
            event.target.value = '';
            return;
        }

        currentFile = file;
        currentUploadType = type;

        // Show preview
        showImagePreview(file, type);

        // Enable submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }

        // Update upload section
        updateUploadSection(type, file);
    }

    function validateFile(file) {
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            showError('Please select a valid image file (JPEG, PNG, GIF, WebP)');
            return false;
        }

        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            showError('Image file must be smaller than 2MB');
            return false;
        }

        return true;
    }

    function showImagePreview(file, type) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const modal = document.getElementById('previewModal');
            const previewImg = document.getElementById('previewImage');
            const previewInfo = document.getElementById('previewInfo');

            previewImg.src = e.target.result;
            previewInfo.innerHTML = `
            <strong>File:</strong> ${file.name}<br>
            <strong>Size:</strong> ${formatFileSize(file.size)}<br>
            <strong>Type:</strong> ${file.type}<br>
            <strong>For:</strong> ${type.charAt(0).toUpperCase() + type.slice(1)} Default Image
        `;

            modal.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }

    function updateUploadSection(type, file) {
        const uploadSection = document.getElementById(type + 'Upload');
        if (!uploadSection) return;

        const uploadText = uploadSection.querySelector('.upload-text');
        const uploadSubtext = uploadSection.querySelector('.upload-subtext');

        if (uploadText) {
            uploadText.textContent = `Selected: ${file.name}`;
            uploadText.style.color = '#22c55e';
            uploadText.style.fontWeight = '600';
        }

        if (uploadSubtext) {
            uploadSubtext.innerHTML = `
            Size: ${formatFileSize(file.size)} • Type: ${file.type}
            <br><span style="color: #22c55e;">
                <i class="fas fa-check-circle"></i> Ready to upload
            </span>
        `;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function confirmUpload() {
        closePreview();
        // Form will be submitted when user clicks the submit button
    }

    function closePreview() {
        const modal = document.getElementById('previewModal');
        modal.style.display = 'none';
    }

    function resetImage(type) {
        const confirmMessage = type === 'both' ?
            'Are you sure you want to reset both customer and provider images to system defaults? This will affect all users currently using default images.' :
            `Are you sure you want to reset the ${type} default image to system default? This will affect all ${type}s currently using the default image.`;

        if (!confirm(confirmMessage)) {
            return;
        }

        // Create and submit reset form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin_reset_default_images") }}';

        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Image type
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = 'image_type';
        typeInput.value = type;
        form.appendChild(typeInput);

        // Reason
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = `Reset ${type} image(s) to system defaults via admin interface`;
        form.appendChild(reasonInput);

        document.body.appendChild(form);
        form.submit();
    }

    function initializeFormHandlers() {
        const forms = ['customerImageForm', 'providerImageForm'];

        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                }
            });
        });
    }

    function showError(message) {
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = `
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
        errorDiv.innerHTML = `
       <div style="display: flex; align-items: center; gap: 0.5rem;">
           <i class="fas fa-exclamation-triangle"></i>
           <span>${message}</span>
       </div>
   `;

        document.body.appendChild(errorDiv);

        setTimeout(() => {
            if (document.body.contains(errorDiv)) {
                document.body.removeChild(errorDiv);
            }
        }, 5000);
    }

    function showSuccess(message) {
        // Create success message element
        const successDiv = document.createElement('div');
        successDiv.style.cssText = `
       position: fixed;
       top: 20px;
       right: 20px;
       background: #f0fdf4;
       border: 2px solid #bbf7d0;
       color: #166534;
       padding: 1rem 1.5rem;
       border-radius: 12px;
       z-index: 10000;
       box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
       max-width: 400px;
   `;
        successDiv.innerHTML = `
       <div style="display: flex; align-items: center; gap: 0.5rem;">
           <i class="fas fa-check-circle"></i>
           <span>${message}</span>
       </div>
   `;

        document.body.appendChild(successDiv);

        setTimeout(() => {
            if (document.body.contains(successDiv)) {
                document.body.removeChild(successDiv);
            }
        }, 5000);
    }

    // Handle click outside modal to close
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('previewModal');
        if (e.target === modal) {
            closePreview();
        }
    });

    // Handle escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreview();
        }
    });

    // Real-time file validation on input change
    document.addEventListener('change', function(e) {
        if (e.target.type === 'file' && e.target.accept === 'image/*') {
            const file = e.target.files[0];
            if (file && !validateFile(file)) {
                e.target.value = '';

                // Reset upload section
                const uploadSection = e.target.closest('.upload-section');
                if (uploadSection) {
                    const uploadText = uploadSection.querySelector('.upload-text');
                    const uploadSubtext = uploadSection.querySelector('.upload-subtext');

                    if (uploadText) {
                        uploadText.textContent = 'Click or drag to upload new image';
                        uploadText.style.color = '#4a5568';
                        uploadText.style.fontWeight = 'normal';
                    }

                    if (uploadSubtext) {
                        uploadSubtext.innerHTML = 'Supports: JPEG, PNG, GIF, WebP (Max: 2MB)';
                    }
                }

                // Disable submit button
                const form = e.target.closest('form');
                if (form) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.6';
                    }
                }
            }
        }
    });

    // Auto-focus reason input when file is selected
    document.addEventListener('change', function(e) {
        if (e.target.type === 'file' && e.target.files.length > 0) {
            setTimeout(() => {
                const form = e.target.closest('form');
                if (form) {
                    const reasonInput = form.querySelector('input[name="reason"]');
                    if (reasonInput) {
                        reasonInput.focus();
                    }
                }
            }, 100);
        }
    });
</script>
@endpush