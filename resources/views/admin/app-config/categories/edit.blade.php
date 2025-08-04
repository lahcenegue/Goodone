@extends('admin.layouts')

@section('page-title', 'Edit Category')
@section('page-subtitle', 'Update category information and manage subcategories.')

@section('content')
<style>
    /* Category Edit Form Styles */
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

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .current-image {
        max-width: 100%;
        max-height: 200px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .image-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .image-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .upload-text {
        color: #4a5568;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .upload-hint {
        color: #718096;
        font-size: 0.8rem;
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
        min-width: 120px;
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

    .btn-success {
        background: #dcfce7;
        color: #166534;
        border: 2px solid #bbf7d0;
    }

    .btn-success:hover {
        background: #bbf7d0;
        color: #14532d;
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

    /* Subcategories Section */
    .subcategories-section {
        grid-column: 1 / -1;
    }

    .subcategory-item {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .subcategory-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e0;
    }

    .subcategory-name {
        flex: 1;
        font-weight: 600;
        color: #2d3748;
        margin-right: 1rem;
    }

    .subcategory-stats {
        font-size: 0.8rem;
        color: #718096;
        margin-right: 1rem;
    }

    .subcategory-actions {
        display: flex;
        gap: 0.5rem;
    }

    .add-subcategory-form {
        background: #f0f9ff;
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .inline-form {
        display: flex;
        gap: 1rem;
        align-items: end;
    }

    .inline-form .form-group {
        margin-bottom: 0;
        flex: 1;
    }

    .required {
        color: #ef4444;
    }

    /* Edit Subcategory Modal */
    .edit-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (max-width: 968px) {
        .edit-grid {
            grid-template-columns: 1fr;
        }

        .inline-form {
            flex-direction: column;
            align-items: stretch;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>

<div class="edit-container">
    <!-- Back Navigation -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin_get_categories') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Categories
        </a>
    </div>

    <!-- Edit Grid -->
    <div class="edit-grid">
        <!-- Category Edit Form -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-edit" style="color: #667eea;"></i>
                    Edit Category
                </h2>
                <p class="card-subtitle">Update category name and image</p>
            </div>

            <form method="POST" action="{{ route('admin_update_category', $category) }}" enctype="multipart/form-data" id="categoryForm">
                @csrf
                @method('PUT')

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
                        value="{{ old('name', $category->name) }}"
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

                <!-- Current Image -->
                @if($category->image)
                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <img 
                        src="{{ asset('storage/images/' . $category->image) }}" 
                        alt="{{ $category->name }}" 
                        class="current-image"
                        onerror="this.style.display='none'"
                    >
                </div>
                @endif

                <!-- New Image Upload -->
                <div class="form-group">
                    <label for="image" class="form-label">
                        Update Image <small>(optional)</small>
                    </label>
                    <div class="image-upload-area" onclick="document.getElementById('image').click()">
                        <i class="fas fa-image" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 0.5rem;"></i>
                        <div class="upload-text">Click to select new image</div>
                        <div class="upload-hint">JPEG, PNG, JPG, GIF (max 2MB)</div>
                    </div>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/jpeg,image/png,image/jpg,image/gif"
                        style="display: none;"
                    >
                    @error('image')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Update Category
                    </button>
                </div>
            </form>
        </div>

        <!-- Category Stats -->
        <div class="form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar" style="color: #22c55e;"></i>
                    Category Statistics
                </h2>
                <p class="card-subtitle">Overview of this category's usage</p>
            </div>

            <div style="text-align: center;">
                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: #667eea;">
                        {{ $category->subcategories->count() }}
                    </div>
                    <div style="color: #718096;">Subcategories</div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: 700; color: #22c55e;">
                        {{ $category->services->count() }}
                    </div>
                    <div style="color: #718096;">Services</div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #f59e0b;">
                        {{ $category->services->where('active', true)->count() }}
                    </div>
                    <div style="color: #718096;">Active Services</div>
                </div>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <div style="font-size: 0.9rem; color: #718096;">
                        Created: {{ $category->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories Management -->
    <div class="form-card subcategories-section">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-list" style="color: #8b5cf6;"></i>
                Manage Subcategories
            </h2>
            <p class="card-subtitle">Add, edit, or remove subcategories for {{ $category->name }}</p>
        </div>

        <!-- Add New Subcategory -->
        <div class="add-subcategory-form">
            <h4 style="margin: 0 0 1rem 0; color: #0284c7;">
                <i class="fas fa-plus"></i>
                Add New Subcategory
            </h4>
            <form method="POST" action="{{ route('admin_store_subcategory', $category) }}" id="subcategoryForm">
                @csrf
                <div class="inline-form">
                    <div class="form-group">
                        <label for="subcategory_name" class="form-label">
                            Subcategory Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="subcategory_name" 
                            name="name" 
                            class="form-input {{ $errors->has('name') ? 'error' : '' }}" 
                            placeholder="Enter subcategory name"
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
                    <button type="submit" class="btn-modern btn-success">
                        <i class="fas fa-plus"></i>
                        Add Subcategory
                    </button>
                </div>
            </form>
        </div>

        <!-- Subcategories List -->
        @if($category->subcategories->count() > 0)
        <div>
            <h4 style="margin: 0 0 1rem 0; color: #4a5568;">
                Existing Subcategories ({{ $category->subcategories->count() }})
            </h4>
            @foreach($category->subcategories as $subcategory)
            <div class="subcategory-item">
                <div class="subcategory-name">{{ $subcategory->name }}</div>
                <div class="subcategory-stats">
                    {{ $subcategory->services->count() }} services
                </div>
                <div class="subcategory-actions">
                    <button onclick="editSubcategory({{ $subcategory->id }}, '{{ $subcategory->name }}')" class="btn-modern btn-secondary">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    @if($subcategory->services->count() == 0)
                    <form method="POST" action="{{ route('admin_delete_subcategory', $subcategory) }}" style="display: inline;" 
                          onsubmit="return confirm('Are you sure you want to delete this subcategory?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-modern btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </form>
                    @else
                    <button class="btn-modern btn-danger" disabled title="Cannot delete subcategory with existing services">
                        <i class="fas fa-lock"></i>
                        Protected
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 2rem; color: #718096;">
            <i class="fas fa-list" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
            <p>No subcategories yet. Add the first one above!</p>
        </div>
        @endif
    </div>
</div>

<!-- Edit Subcategory Modal -->
<div id="editModal" class="edit-modal">
    <div class="modal-content">
        <h3 style="margin: 0 0 1.5rem 0; color: #2d3748;">
            <i class="fas fa-edit"></i>
            Edit Subcategory
        </h3>
        <form id="editSubcategoryForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_name" class="form-label">
                    Subcategory Name <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit_name" 
                    name="name" 
                    class="form-input"
                    required
                    maxlength="255"
                >
            </div>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save"></i>
                    Update
                </button>
                <button type="button" onclick="closeEditModal()" class="btn-modern btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryForm = document.getElementById('categoryForm');
    const subcategoryForm = document.getElementById('subcategoryForm');
    const imageInput = document.getElementById('image');

    // Category form submission
    categoryForm.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    });

    // Subcategory form submission
    subcategoryForm.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    });

    // Image upload preview
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }

            // Show upload feedback
            const uploadArea = document.querySelector('.image-upload-area');
            uploadArea.style.borderColor = '#22c55e';
            uploadArea.style.background = '#dcfce7';
            uploadArea.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #22c55e; margin-bottom: 0.5rem;"></i>
                <div style="color: #166534; font-weight: 600;">New image selected: ${file.name}</div>
                <div style="color: #166534; font-size: 0.8rem;">Click submit to update</div>
            `;
        }
    });
});

// Edit subcategory modal functions
function editSubcategory(id, name) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editSubcategoryForm');
    const nameInput = document.getElementById('edit_name');

    form.action = `/admin/app-config/subcategories/${id}`;
    nameInput.value = name;
    modal.style.display = 'flex';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endpush