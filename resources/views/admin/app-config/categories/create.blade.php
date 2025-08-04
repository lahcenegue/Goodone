@extends('admin.layouts')

@section('page-title', 'Add New Category')
@section('page-subtitle', 'Create a new service category for your platform.')

@section('content')
<style>
    /* Category Form Styles */
    .form-container {
        max-width: 800px;
        margin: 0 auto;
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

    .image-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .image-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .image-upload-area.drag-over {
        border-color: #667eea;
        background: #e0e7ff;
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

    .upload-hint {
        color: #718096;
        font-size: 0.875rem;
    }

    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        margin-top: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
            margin: 1rem;
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
        <a href="{{ route('admin_get_categories') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Categories
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <!-- Form Header -->
        <div class="form-header">
            <h1 class="form-title">
                <i class="fas fa-plus-circle" style="color: #667eea;"></i>
                Add New Category
            </h1>
            <p class="form-subtitle">
                Create a new service category to organize your platform offerings
            </p>
        </div>

        <!-- Category Form -->
        <form method="POST" action="{{ route('admin_store_category') }}" enctype="multipart/form-data" id="categoryForm">
            @csrf

            <!-- Category Name -->
            <div class="form-group">
                <label for="name" class="form-label">
                    Category Name <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input {{ $errors->has('name') ? 'error' : '' }}" 
                    placeholder="Enter category name (e.g., Home Services, Automotive, etc.)"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                >
                @error('name')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Category Image -->
            <div class="form-group">
                <label for="image" class="form-label">
                    Category Image <span class="required">*</span>
                </label>
                <div class="image-upload-area" onclick="document.getElementById('image').click()">
                    <div id="upload-content">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Click to upload category image</div>
                        <div class="upload-hint">Or drag and drop an image here</div>
                        <div class="upload-hint">Supported formats: JPEG, PNG, JPG, GIF (max 2MB)</div>
                    </div>
                    <div id="preview-content" style="display: none;">
                        <img id="image-preview" class="image-preview" alt="Category preview">
                        <div style="margin-top: 1rem;">
                            <span style="color: #22c55e; font-weight: 600;">
                                <i class="fas fa-check-circle"></i>
                                Image selected successfully
                            </span>
                        </div>
                    </div>
                </div>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/jpeg,image/png,image/jpg,image/gif"
                    style="display: none;"
                    required
                >
                @error('image')
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
                    Create Category
                </button>
                <a href="{{ route('admin_get_categories') }}" class="btn-modern btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    const submitBtn = document.getElementById('submitBtn');
    const imageInput = document.getElementById('image');
    const uploadArea = document.querySelector('.image-upload-area');
    const uploadContent = document.getElementById('upload-content');
    const previewContent = document.getElementById('preview-content');
    const imagePreview = document.getElementById('image-preview');

    // Form submission handling
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Category...';
    });

    // Image upload handling
    imageInput.addEventListener('change', function(e) {
        handleImageSelect(e.target.files[0]);
    });

    // Drag and drop handling
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                imageInput.files = files;
                handleImageSelect(file);
            } else {
                alert('Please select a valid image file.');
            }
        }
    });

    function handleImageSelect(file) {
        if (file) {
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                imageInput.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                imageInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                uploadContent.style.display = 'none';
                previewContent.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Reset preview when clicking on preview area
    previewContent.addEventListener('click', function() {
        imageInput.value = '';
        uploadContent.style.display = 'block';
        previewContent.style.display = 'none';
        uploadArea.classList.remove('drag-over');
    });

    // Character counter for name field
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function() {
        const remaining = 255 - this.value.length;
        
        // Remove existing counter
        const existingCounter = this.parentNode.querySelector('.char-counter');
        if (existingCounter) {
            existingCounter.remove();
        }

        // Add counter if more than 200 characters
        if (this.value.length > 200) {
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.style.fontSize = '0.8rem';
            counter.style.color = remaining < 20 ? '#ef4444' : '#718096';
            counter.style.marginTop = '0.25rem';
            counter.textContent = `${remaining} characters remaining`;
            this.parentNode.appendChild(counter);
        }
    });
});
</script>
@endpush