@extends('admin.layouts')

@section('page-title', 'Edit Customer - ' . $customer->full_name)
@section('page-subtitle', 'Update customer profile information and account settings')

@section('content')
<style>
    /* Customer Edit Form Styles */
    .edit-form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .edit-form-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1rem;
        padding-right: 3rem;
    }

    .form-checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-checkbox-group:hover {
        border-color: #667eea;
    }

    .form-checkbox {
        width: 20px;
        height: 20px;
        accent-color: #667eea;
    }

    .form-checkbox-label {
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        margin: 0;
    }

    .form-checkbox-description {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    .form-file-input {
        position: relative;
        overflow: hidden;
        display: block;
        cursor: pointer;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        width: 100%;
        user-select: none;
    }

    .form-file-input:hover {
        border-color: #667eea;
        background: #f0f4ff;
        transform: translateY(-1px);
    }

    .form-file-input:active {
        transform: translateY(0);
    }

    .file-upload-container {
        position: relative;
    }



    .form-file-input input[type=file] {
        position: absolute;
        left: -9999px;
        opacity: 0;
        width: 0.1px;
        height: 0.1px;
    }

    .current-image {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .current-image img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .current-image-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5a67d8;
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
        color: #2d3748;
        text-decoration: none;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-color: #22c55e;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-color: #ef4444;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #f59e0b;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .edit-form-container {
            margin: 0 1rem;
        }
    }
</style>

<!-- Customer Edit Content -->
<div class="edit-form-container">
    <!-- Back Button -->
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('admin_show_customer', $customer) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Customer Details
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 0.5rem 0 0 1.5rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin_update_customer', $customer) }}" enctype="multipart/form-data" id="customerEditForm">
        @csrf
        @method('PUT')

        <!-- Basic Information Section -->
        <div class="edit-form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-user"></i>
                    Basic Information
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" class="form-input"
                            value="{{ old('full_name', $customer->full_name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input"
                            value="{{ old('email', $customer->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input"
                            value="{{ old('phone', $customer->phone) }}">
                    </div>

                    <div class="form-group">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" id="country" name="country" class="form-input"
                            value="{{ old('country', $customer->country) }}">
                    </div>

                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-input"
                            value="{{ old('city', $customer->city) }}">
                    </div>

                    <div class="form-group">
                        <label for="location" class="form-label">Location Details</label>
                        <input type="text" id="location" name="location" class="form-input"
                            value="{{ old('location', $customer->location) }}"
                            placeholder="Address, landmark, etc.">
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Image Section -->
        <div class="edit-form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-image"></i>
                    Profile Image
                </h3>

                @php
                // Get default image from app settings
                $customerImageSetting = \App\Models\AppSetting::where("key", "=", "customer-image")->first();
                $defaultImageName = $customerImageSetting ? $customerImageSetting->value : '';
                $isDefaultImage = ($customer->picture === $defaultImageName);

                // Determine which image to show
                $imageToShow = null;
                $showingDefault = false;

                if ($customer->picture && $customer->picture !== '') {
                $imageToShow = $customer->picture;
                $showingDefault = $isDefaultImage;
                } elseif ($defaultImageName) {
                $imageToShow = $defaultImageName;
                $showingDefault = true;
                }
                @endphp

                @if($imageToShow)
                <div class="current-image">
                    <img src="{{ asset('storage/images/' . $imageToShow) }}" alt="Profile image" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                    <div>
                        @if($showingDefault)
                        <div style="font-weight: 600; color: #667eea;">
                            <i class="fas fa-star"></i> Default Profile Image
                        </div>
                        <div style="font-size: 0.875rem; color: #718096;">This is the platform's default image</div>
                        @else
                        <div style="font-weight: 600; color: #22c55e;">
                            <i class="fas fa-user-circle"></i> Custom Profile Image
                        </div>
                        <div style="font-size: 0.875rem; color: #718096;">Upload a new image to replace</div>
                        <button type="button" onclick="deleteCustomerImage({{ $customer->id }})" style="background: #ef4444; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; margin-top: 0.75rem;">
                            <i class="fas fa-trash"></i> Delete & Use Default
                        </button>
                        @endif
                    </div>
                </div>
                @else
                <div class="current-image">
                    <div class="current-image-placeholder">
                        {{ strtoupper(substr($customer->full_name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600;">No Profile Image</div>
                        <div style="font-size: 0.875rem; color: #718096;">Upload an image below</div>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="profile_image" class="form-label">Upload New Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="form-input">
                </div>
            </div>
        </div>

        <!-- Account Settings Section -->
        <div class="edit-form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-cog"></i>
                    Account Settings
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <div class="form-checkbox-group">
                            <input type="checkbox" id="verified" name="verified" value="1" class="form-checkbox"
                                {{ $customer->verified ? 'checked' : '' }}>
                            <div>
                                <label for="verified" class="form-checkbox-label">Verified Customer</label>
                                <div class="form-checkbox-description">Mark customer as verified (Admin verification)</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-checkbox-group">
                            <input type="checkbox" id="active" name="active" value="1" class="form-checkbox"
                                {{ $customer->active ? 'checked' : '' }}>
                            <div>
                                <label for="active" class="form-checkbox-label">Active Customer</label>
                                <div class="form-checkbox-description">Customer can access the platform</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-checkbox-group" style="border-color: #ef4444;">
                            <input type="checkbox" id="blocked" name="blocked" value="1" class="form-checkbox"
                                {{ $customer->blocked ? 'checked' : '' }}>
                            <div>
                                <label for="blocked" class="form-checkbox-label" style="color: #ef4444;">Block Customer</label>
                                <div class="form-checkbox-description" style="color: #ef4444;">Prevent customer from accessing the platform</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Reset Section -->
        <div class="edit-form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-lock"></i>
                    Password Reset
                </h3>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Optional:</strong> Leave password fields empty to keep current password unchanged.
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-input"
                            minlength="6" placeholder="Enter new password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                            minlength="6" placeholder="Confirm new password">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('admin_show_customer', $customer) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="edit-form-card" style="border-color: #ef4444;">
        <div class="form-section">
            <h3 class="form-section-title" style="color: #ef4444; border-color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
                Danger Zone
            </h3>

            <div class="alert alert-error">
                <i class="fas fa-warning"></i>
                <strong>Warning:</strong> These actions are permanent and cannot be undone.
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; padding: 1rem; background: #fee2e2; border-radius: 8px;">
                <div>
                    <div style="font-weight: 600; color: #991b1b;">Delete Customer Account</div>
                    <div style="font-size: 0.875rem; color: #b91c1c; margin-top: 0.25rem;">
                        Permanently delete this customer and all associated data. This action cannot be undone.
                    </div>
                </div>
                <form method="POST" action="{{ route('admin_delete_customer', $customer) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you absolutely sure you want to delete {{ $customer->full_name }}? This will permanently delete their account, orders, and all associated data. This action cannot be undone!')">
                        <i class="fas fa-trash"></i>
                        Delete Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete image function
    function deleteCustomerImage(customerId) {
        if (confirm('Are you sure you want to delete this image and use the default image instead?')) {
            fetch(`/admin/customers/${customerId}/delete-image`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload page to show default image
                    } else {
                        alert('Error: ' + (data.message || 'Unable to delete image'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting image. Please try again.');
                });
        }
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('customerEditForm');

        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmation = document.getElementById('password_confirmation').value;

                if (password && confirmation && password !== confirmation) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    return false;
                }
            });
        }
    });
</script>

@endsection