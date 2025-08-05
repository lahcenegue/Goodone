@extends('admin.layouts')

@section('page-title', 'Edit Advertisement')
@section('page-subtitle', 'Update advertisement details and track performance')

@section('content')
<style>
    /* Modern Edit Form Styles - Matching Categories/Coupons Design */
    .form-container {
        max-width: 1200px;
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

    /* Analytics Preview Card */
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        position: sticky;
        top: 2rem;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .analytics-item {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .analytics-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .analytics-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Form Sections */
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-subtitle {
        color: #718096;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    /* Form Elements */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row.full-width {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .required {
        color: #ef4444;
    }

    .form-input,
    .form-select,
    .form-textarea {
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .form-input.error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-help {
        color: #718096;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Current Image Display */
    .current-image-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .current-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .image-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .image-meta {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Image Upload Area */
    .image-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-top: 1rem;
        display: none;
    }

    .image-upload-area.show {
        display: block;
    }

    .image-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .upload-text {
        color: #4a5568;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .upload-hint {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Status Badge */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-success {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
        color: white;
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.2);
        border: 2px solid rgba(245, 158, 11, 0.4);
        color: white;
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid rgba(239, 68, 68, 0.4);
        color: white;
    }

    /* Selection Cards */
    .selection-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .selection-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        text-align: center;
    }

    .selection-card:hover {
        border-color: #cbd5e0;
    }

    .selection-card.selected {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .selection-card-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .selection-card-desc {
        color: #718096;
        font-size: 0.875rem;
    }

    /* Checkbox Styles */
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .checkbox-input {
        width: 1.25rem;
        height: 1.25rem;
        accent-color: #667eea;
        cursor: pointer;
    }

    .checkbox-label {
        color: #374151;
        font-weight: 500;
        cursor: pointer;
    }

    /* Date Inputs */
    .date-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* Priority Selector */
    .priority-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
    }

    .priority-option {
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .priority-option:hover {
        border-color: #cbd5e0;
    }

    .priority-option.selected {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .priority-low {
        border-left: 4px solid #22c55e;
    }

    .priority-normal {
        border-left: 4px solid #3b82f6;
    }

    .priority-high {
        border-left: 4px solid #f59e0b;
    }

    .priority-urgent {
        border-left: 4px solid #ef4444;
    }

    /* Budget Controls */
    .budget-controls {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
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

    .btn-outline {
        background: white;
        color: #4a5568;
        border: 2px solid #e2e8f0;
    }

    .btn-outline:hover {
        background: #f8fafc;
        color: #2d3748;
        text-decoration: none;
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .analytics-card {
            position: static;
            order: -1;
        }

        .form-row,
        .date-group {
            grid-template-columns: 1fr;
        }

        .action-buttons {
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
        <a href="{{ route('admin_get_ads') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Advertisements
        </a>
    </div>

    <!-- Form Grid -->
    <div class="form-grid">
        <!-- Main Edit Form -->
        <div class="form-card">
            <!-- Form Header -->
            <div class="form-header">
                <h1 class="form-title">
                    <i class="fas fa-edit" style="color: #667eea;"></i>
                    Edit Advertisement
                </h1>
                <p class="form-subtitle">
                    Update advertisement details and performance settings
                </p>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="{{ route('admin_update_ad', $ad) }}" enctype="multipart/form-data" id="adForm">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h3>
                    <p class="section-subtitle">Update the core details of your advertisement</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Title <span class="required">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $ad->title) }}"
                                class="form-input {{ $errors->has('title') ? 'error' : '' }}"
                                placeholder="Enter ad title" required maxlength="255">
                            <div class="form-help">Keep it concise and engaging (max 255 characters)</div>
                            @error('title')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" value="{{ old('display_order', $ad->display_order) }}"
                                class="form-input" min="0" max="999" placeholder="0">
                            <div class="form-help">Lower numbers show first (0 = highest priority)</div>
                            @error('display_order')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" placeholder="Describe your advertisement (optional)"
                                maxlength="1000">{{ old('description', $ad->description) }}</textarea>
                            <div class="form-help">Optional description to help manage your ads (max 1000 characters)</div>
                            @error('description')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Priority Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Priority Level
                    </h3>
                    <p class="section-subtitle">Set the display priority for this advertisement</p>

                    <div class="priority-selector">
                        <div class="priority-option priority-low {{ old('admin_priority', $ad->admin_priority ?? 2) == 1 ? 'selected' : '' }}" data-priority="1">
                            <div style="font-weight: 600; color: #22c55e;">Low</div>
                            <div style="font-size: 0.8rem; color: #718096;">Normal serving</div>
                        </div>
                        <div class="priority-option priority-normal {{ old('admin_priority', $ad->admin_priority ?? 2) == 2 ? 'selected' : '' }}" data-priority="2">
                            <div style="font-weight: 600; color: #3b82f6;">Normal</div>
                            <div style="font-size: 0.8rem; color: #718096;">Default priority</div>
                        </div>
                        <div class="priority-option priority-high {{ old('admin_priority', $ad->admin_priority ?? 2) == 3 ? 'selected' : '' }}" data-priority="3">
                            <div style="font-weight: 600; color: #f59e0b;">High</div>
                            <div style="font-size: 0.8rem; color: #718096;">Boosted serving</div>
                        </div>
                        <div class="priority-option priority-urgent {{ old('admin_priority', $ad->admin_priority ?? 2) == 4 ? 'selected' : '' }}" data-priority="4">
                            <div style="font-weight: 600; color: #ef4444;">Urgent</div>
                            <div style="font-size: 0.8rem; color: #718096;">Maximum priority</div>
                        </div>
                    </div>
                    <input type="hidden" name="admin_priority" id="admin_priority" value="{{ old('admin_priority', $ad->admin_priority ?? 2) }}">
                </div>

                <!-- Ad Type & Placement Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-layer-group"></i>
                        Type & Placement
                    </h3>
                    <p class="section-subtitle">Configure where and how this ad will appear</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Advertisement Type <span class="required">*</span></label>
                            <div class="selection-cards">
                                <div class="selection-card {{ old('ad_type', $ad->ad_type) === 'internal' ? 'selected' : '' }}" data-type="internal">
                                    <div class="selection-card-title">
                                        <i class="fas fa-home" style="color: #667eea;"></i><br>Internal
                                    </div>
                                    <div class="selection-card-desc">App features</div>
                                </div>
                                <div class="selection-card {{ old('ad_type', $ad->ad_type) === 'external' ? 'selected' : '' }}" data-type="external">
                                    <div class="selection-card-title">
                                        <i class="fas fa-external-link-alt" style="color: #22c55e;"></i><br>External
                                    </div>
                                    <div class="selection-card-desc">Third-party</div>
                                </div>
                            </div>
                            <input type="hidden" name="ad_type" id="ad_type" value="{{ old('ad_type', $ad->ad_type) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Placement <span class="required">*</span></label>
                            <select name="placement" class="form-select" required>
                                <option value="">Select Placement</option>
                                <option value="home_banner" {{ old('placement', $ad->placement) === 'home_banner' ? 'selected' : '' }}>Home Banner</option>
                                <option value="service_list" {{ old('placement', $ad->placement) === 'service_list' ? 'selected' : '' }}>Service List</option>
                                <option value="service_detail" {{ old('placement', $ad->placement) === 'service_detail' ? 'selected' : '' }}>Service Detail</option>
                                <option value="profile" {{ old('placement', $ad->placement) === 'profile' ? 'selected' : '' }}>Profile Page</option>
                            </select>
                            @error('placement')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Image Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-image"></i>
                        Advertisement Image
                    </h3>
                    <p class="section-subtitle">Current image and option to upload a new one</p>

                    <!-- Current Image Display -->
                    <div class="current-image-section">
                        <div class="image-info">
                            <h4 style="margin: 0; color: #2d3748;">Current Image</h4>
                            <div class="image-meta">
                                Uploaded: {{ $ad->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="current-image">
                        <div>
                            <button type="button" id="changeImageBtn" class="btn-modern btn-outline">
                                <i class="fas fa-exchange-alt"></i> Change Image
                            </button>
                        </div>
                    </div>

                    <!-- New Image Upload (Hidden by default) -->
                    <div class="image-upload-area" id="newImageSection">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 0.5rem;"></i>
                        <div class="upload-text">Click to select new image</div>
                        <div class="upload-hint">JPEG, PNG, JPG, GIF, WebP (Max 5MB)</div>
                        <button type="button" id="cancelChangeBtn" class="btn-modern btn-secondary" style="margin-top: 1rem;">
                            <i class="fas fa-times"></i> Cancel Change
                        </button>
                    </div>
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                    @error('image')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Target URL Section (conditional) -->
                <div class="form-section" id="targetUrlSection" style="display: {{ old('ad_type', $ad->ad_type) === 'external' ? 'block' : 'none' }};">
                    <h3 class="section-title">
                        <i class="fas fa-link"></i>
                        Target URL
                    </h3>
                    <p class="section-subtitle">Where should users go when they click this ad?</p>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Target URL</label>
                            <input type="url" name="target_url" value="{{ old('target_url', $ad->target_url) }}"
                                class="form-input" placeholder="https://example.com">
                            <div class="form-help">Full URL including http:// or https://</div>
                            @error('target_url')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Budget & Performance Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-dollar-sign"></i>
                        Budget & Performance Controls
                    </h3>
                    <p class="section-subtitle">Set budget limits and performance thresholds</p>

                    <div class="budget-controls">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Daily Budget Limit (CAD)</label>
                            <input type="number" name="max_daily_budget" value="{{ old('max_daily_budget', $ad->max_daily_budget) }}"
                                class="form-input" min="0" step="0.01" placeholder="0.00">
                            <div class="form-help">Leave empty for unlimited</div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Min CTR Threshold (%)</label>
                            <input type="number" name="min_ctr_threshold" value="{{ old('min_ctr_threshold', $ad->min_ctr_threshold) }}"
                                class="form-input" min="0" max="100" step="0.01" placeholder="1.00">
                            <div class="form-help">Auto-pause if below threshold</div>
                        </div>
                    </div>

                    <div class="checkbox-group" style="margin-top: 1rem;">
                        <input type="checkbox" name="auto_pause_enabled" id="auto_pause_enabled" class="checkbox-input"
                            value="1" {{ old('auto_pause_enabled', $ad->auto_pause_enabled) ? 'checked' : '' }}>
                        <label for="auto_pause_enabled" class="checkbox-label">
                            <i class="fas fa-pause-circle" style="color: #f59e0b; margin-right: 0.25rem;"></i>
                            Enable automatic pause for poor performance
                        </label>
                    </div>
                </div>

                <!-- Scheduling Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Scheduling & Status
                    </h3>
                    <p class="section-subtitle">Configure when this ad should be active</p>

                    <div class="checkbox-group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" class="checkbox-input"
                            value="1" {{ old('is_active', $ad->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="checkbox-label">
                            <i class="fas fa-play-circle" style="color: #22c55e; margin-right: 0.25rem;"></i>
                            This advertisement is active
                        </label>
                    </div>

                    <div class="date-group">
                        <div class="form-group">
                            <label class="form-label">Start Date (Optional)</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $ad->start_date ? $ad->start_date->format('Y-m-d') : '') }}"
                                class="form-input">
                            <div class="form-help">When should this ad start showing?</div>
                            @error('start_date')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $ad->end_date ? $ad->end_date->format('Y-m-d') : '') }}"
                                class="form-input">
                            <div class="form-help">When should this ad stop showing?</div>
                            @error('end_date')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Notes Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Admin Notes
                    </h3>
                    <p class="section-subtitle">Internal notes for admin reference (not visible to users)</p>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="admin_notes" class="form-textarea" placeholder="Add internal notes about this advertisement..."
                                maxlength="1000" rows="4">{{ old('admin_notes', $ad->admin_notes ?? '') }}</textarea>
                            <div class="form-help">Internal notes for admin team reference (max 1000 characters)</div>
                            @error('admin_notes')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Update Advertisement
                    </button>

                    <a href="{{ route('admin_get_ads') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Ads
                    </a>
                </div>
            </form>
        </div>

        <!-- Analytics & Quick Actions Sidebar -->
        <div>
            <!-- Analytics Preview -->
            <div class="analytics-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2 style="color: white; margin: 0; font-size: 1.5rem; font-weight: 700;">
                            <i class="fas fa-chart-line" style="margin-right: 0.5rem;"></i>
                            Performance Overview
                        </h2>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 0.9rem;">{{ $ad->title }}</p>
                        <div style="margin-top: 1rem;">
                            <span class="status-badge status-{{ $ad->status_badge_class }}">
                                <i class="fas {{ $ad->status === 'active' ? 'fa-play' : ($ad->status === 'scheduled' ? 'fa-clock' : ($ad->status === 'expired' ? 'fa-calendar-times' : 'fa-pause')) }}"></i>
                                {{ ucfirst($ad->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if(isset($analytics))
                <div class="analytics-grid">
                    <div class="analytics-item">
                        <div class="analytics-value">{{ number_format($analytics['total_views'] ?? $ad->view_count) }}</div>
                        <div class="analytics-label">Total Views</div>
                    </div>
                    <div class="analytics-item">
                        <div class="analytics-value">{{ number_format($analytics['total_clicks'] ?? $ad->click_count) }}</div>
                        <div class="analytics-label">Total Clicks</div>
                    </div>
                    <div class="analytics-item">
                        <div class="analytics-value">{{ $analytics['ctr'] ?? ($ad->view_count > 0 ? number_format(($ad->click_count / $ad->view_count) * 100, 1) : 0) }}%</div>
                        <div class="analytics-label">Click Rate</div>
                    </div>
                    <div class="analytics-item">
                        <div class="analytics-value">{{ $analytics['days_active'] ?? $ad->created_at->diffInDays(now()) }}</div>
                        <div class="analytics-label">Days Active</div>
                    </div>
                    <div class="analytics-item">
                        <div class="analytics-value">{{ $analytics['avg_daily_views'] ?? ($ad->created_at->diffInDays(now()) > 0 ? round($ad->view_count / $ad->created_at->diffInDays(now()), 1) : 0) }}</div>
                        <div class="analytics-label">Daily Avg Views</div>
                    </div>
                </div>
                @endif

                <div class="quick-actions" style="margin-top: 1.5rem;">
                    @if($ad->view_count > 0 || $ad->click_count > 0)
                    <a href="{{ route('admin_show_ad_analytics', $ad) }}" class="btn-modern btn-outline btn-sm" style="color: white; border-color: rgba(255,255,255,0.3);">
                        <i class="fas fa-chart-bar"></i> Detailed Analytics
                    </a>
                    @endif

                    <form method="POST" action="{{ route('admin_toggle_ad_status', $ad) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-modern btn-outline btn-sm" style="color: white; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1);">
                            <i class="fas {{ $ad->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                            {{ $ad->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ad Information Card -->
            <div class="form-card">
                <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
                    <i class="fas fa-info-circle" style="color: #667eea;"></i>
                    Advertisement Details
                </h3>

                <div style="space-y: 1rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                        <span style="color: #718096; font-size: 0.875rem;">Created:</span>
                        <span style="color: #2d3748; font-weight: 600;">{{ $ad->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                        <span style="color: #718096; font-size: 0.875rem;">Last Modified:</span>
                        <span style="color: #2d3748; font-weight: 600;">{{ $ad->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                        <span style="color: #718096; font-size: 0.875rem;">Current Priority:</span>
                        <span style="color: #2d3748; font-weight: 600;">{{ $ad->formatted_priority ?? 'Normal Priority' }}</span>
                    </div>
                    @if($ad->max_daily_budget)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                        <span style="color: #718096; font-size: 0.875rem;">Daily Budget:</span>
                        <span style="color: #2d3748; font-weight: 600;">${{ number_format($ad->max_daily_budget, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f7fafc;">
                        <span style="color: #718096; font-size: 0.875rem;">Today's Spend:</span>
                        <span style="color: #2d3748; font-weight: 600;">${{ number_format($ad->daily_spend ?? 0, 2) }}</span>
                    </div>
                    @endif
                    @if($ad->performance_score)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                        <span style="color: #718096; font-size: 0.875rem;">Performance Score:</span>
                        <span style="color: #2d3748; font-weight: 600;">{{ number_format($ad->performance_score, 1) }}/10</span>
                    </div>
                    @endif
                </div>

                <!-- Performance Tips -->
                <div style="margin-top: 2rem; padding: 1rem; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #0c4a6e; font-size: 0.9rem; font-weight: 600;">
                        <i class="fas fa-lightbulb"></i> Performance Tips
                    </h4>
                    <ul style="margin: 0; padding-left: 1.2rem; color: #0369a1; font-size: 0.8rem; line-height: 1.4;">
                        <li>Higher priority ads get more visibility</li>
                        <li>Set budget limits to control daily spending</li>
                        <li>Enable auto-pause to manage poor performers</li>
                        <li>Monitor CTR for engagement insights</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Priority Selection
        const priorityOptions = document.querySelectorAll('.priority-option');
        const priorityInput = document.getElementById('admin_priority');

        priorityOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                priorityOptions.forEach(opt => opt.classList.remove('selected'));
                // Add selected class to clicked option
                this.classList.add('selected');
                // Update hidden input
                priorityInput.value = this.dataset.priority;
            });
        });

        // Ad Type Selection
        const typeCards = document.querySelectorAll('[data-type]');
        const adTypeInput = document.getElementById('ad_type');
        const targetUrlSection = document.getElementById('targetUrlSection');

        typeCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                typeCards.forEach(c => c.classList.remove('selected'));
                // Add selected class to clicked card
                this.classList.add('selected');
                // Update hidden input
                adTypeInput.value = this.dataset.type;

                // Show/hide target URL section
                if (this.dataset.type === 'external') {
                    targetUrlSection.style.display = 'block';
                } else {
                    targetUrlSection.style.display = 'none';
                }
            });
        });

        // Image Change Handling
        const changeImageBtn = document.getElementById('changeImageBtn');
        const newImageSection = document.getElementById('newImageSection');
        const cancelChangeBtn = document.getElementById('cancelChangeBtn');
        const imageInput = document.getElementById('imageInput');

        // Show new image upload section
        changeImageBtn.addEventListener('click', function() {
            newImageSection.classList.add('show');
            this.style.display = 'none';
        });

        // Cancel image change
        cancelChangeBtn.addEventListener('click', function() {
            newImageSection.classList.remove('show');
            changeImageBtn.style.display = 'inline-flex';
            imageInput.value = '';
        });

        // Image upload area click
        newImageSection.addEventListener('click', function(e) {
            if (e.target.id !== 'cancelChangeBtn') {
                imageInput.click();
            }
        });

        // File input change
        imageInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    this.value = '';
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image must be smaller than 5MB.');
                    this.value = '';
                    return;
                }

                // Show feedback
                newImageSection.style.borderColor = '#22c55e';
                newImageSection.style.background = '#dcfce7';
                newImageSection.innerHTML = `
               <i class="fas fa-check-circle" style="font-size: 2rem; color: #22c55e; margin-bottom: 0.5rem;"></i>
               <div style="color: #166534; font-weight: 600;">New image selected: ${file.name}</div>
               <div style="color: #166534; font-size: 0.8rem;">Click Update to save changes</div>
               <button type="button" id="cancelChangeBtn" class="btn-modern btn-secondary" style="margin-top: 1rem;">
                   <i class="fas fa-times"></i> Cancel Change
               </button>
           `;

                // Re-bind cancel button
                document.getElementById('cancelChangeBtn').addEventListener('click', function() {
                    newImageSection.classList.remove('show');
                    changeImageBtn.style.display = 'inline-flex';
                    imageInput.value = '';
                    // Reset original content
                    newImageSection.innerHTML = `
                   <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 0.5rem;"></i>
                   <div class="upload-text">Click to select new image</div>
                   <div class="upload-hint">JPEG, PNG, JPG, GIF, WebP (Max 5MB)</div>
                   <button type="button" id="cancelChangeBtn" class="btn-modern btn-secondary" style="margin-top: 1rem;">
                       <i class="fas fa-times"></i> Cancel Change
                   </button>
               `;
                    newImageSection.style.borderColor = '#cbd5e0';
                    newImageSection.style.background = '#f8fafc';
                });
            }
        });

        // Form submission
        const form = document.getElementById('adForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        });

        // Date validation
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        startDateInput.addEventListener('change', function() {
            if (this.value) {
                endDateInput.min = this.value;
            }
        });

        // Initialize end date min value if start date has value
        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
        }
    });
</script>

@endsection