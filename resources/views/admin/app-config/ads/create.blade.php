@extends('admin.layouts')

@section('page-title', 'Create New Advertisement')
@section('page-subtitle', 'Create a new advertisement with advanced targeting and controls')

@section('content')
<style>
    /* Modern Form Styles - Matching Categories/Coupons Design */
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
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

    /* Selection Cards */
    .selection-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .selection-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
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

    /* Image Upload Styles */
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

    .image-upload-area.dragover {
        border-color: #667eea;
        background: #e0e7ff;
    }

    .image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        margin: 1rem 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .upload-icon {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
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

    /* Multi-select Checkboxes */
    .multi-select-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .multi-select-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .multi-select-item:hover {
        background: #f1f5f9;
    }

    .multi-select-item input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        accent-color: #667eea;
    }

    .multi-select-item label {
        font-size: 0.875rem;
        color: #4a5568;
        cursor: pointer;
        flex: 1;
    }

    /* Preview Panel */
    .preview-panel {
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

    .ad-mockup {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        text-align: center;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .mockup-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        word-break: break-word;
    }

    .mockup-description {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .mockup-type {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
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
        font-size: 0.875rem;
    }

    /* Action Buttons */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
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

    /* Priority Selector */
    .priority-selector {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-top: 0.5rem;
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

    .priority-label {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .priority-desc {
        font-size: 0.75rem;
        color: #718096;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .preview-panel {
            position: static;
            order: -1;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .selection-cards {
            grid-template-columns: 1fr;
        }

        .priority-selector {
            grid-template-columns: repeat(2, 1fr);
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
        <a href="{{ route('admin_get_ads') }}" class="btn-modern btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Advertisements
        </a>
    </div>

    <!-- Form Grid -->
    <div class="form-grid">
        <!-- Main Form -->
        <div class="form-card">
            <!-- Form Header -->
            <div class="form-section">
                <h1 style="color: #667eea; margin: 0; font-size: 1.75rem; font-weight: 700; text-align: center;">
                    <i class="fas fa-plus-circle" style="margin-right: 0.5rem;"></i>
                    Create New Advertisement
                </h1>
                <p style="text-align: center; color: #718096; margin: 0.5rem 0 0 0;">
                    Design and launch your advertising campaign with advanced targeting
                </p>
            </div>

            <!-- Create Form -->
            <form method="POST" action="{{ route('admin_store_ad') }}" enctype="multipart/form-data" id="adForm">
                @csrf

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h3>
                    <p class="section-subtitle">Enter the essential details for your advertisement</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Advertisement Title <span class="required">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-input" placeholder="Enter compelling ad title" required maxlength="255"
                                oninput="updatePreview()">
                            <div class="form-help">Keep it concise and engaging (max 255 characters)</div>
                            @error('title')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Display Priority</label>
                            <div class="priority-selector">
                                <div class="priority-option" data-priority="1">
                                    <div class="priority-label">Low</div>
                                    <div class="priority-desc">Standard</div>
                                </div>
                                <div class="priority-option selected" data-priority="2">
                                    <div class="priority-label">Normal</div>
                                    <div class="priority-desc">Default</div>
                                </div>
                                <div class="priority-option" data-priority="3">
                                    <div class="priority-label">High</div>
                                    <div class="priority-desc">Important</div>
                                </div>
                                <div class="priority-option" data-priority="4">
                                    <div class="priority-label">Urgent</div>
                                    <div class="priority-desc">Critical</div>
                                </div>
                            </div>
                            <input type="hidden" name="admin_priority" id="admin_priority" value="2">
                            <div class="form-help">Higher priority ads show first</div>
                        </div>
                    </div>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" placeholder="Describe your advertisement (optional)"
                                maxlength="1000" oninput="updatePreview()">{{ old('description') }}</textarea>
                            <div class="form-help">Optional description for internal reference (max 1000 characters)</div>
                            @error('description')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ad Type & Placement Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-layer-group"></i>
                        Advertisement Type & Placement
                    </h3>
                    <p class="section-subtitle">Choose the type and where this ad will appear</p>

                    <div class="form-group">
                        <label class="form-label">Advertisement Type <span class="required">*</span></label>
                        <div class="selection-cards">
                            <div class="selection-card {{ old('ad_type') === 'internal' ? 'selected' : '' }}" data-type="internal">
                                <div class="selection-card-title">
                                    <i class="fas fa-home" style="margin-right: 0.5rem; color: #667eea;"></i>
                                    Internal Ad
                                </div>
                                <div class="selection-card-desc">
                                    Promote your own app features, services, or content
                                </div>
                            </div>
                            <div class="selection-card {{ old('ad_type') === 'external' ? 'selected' : '' }}" data-type="external">
                                <div class="selection-card-title">
                                    <i class="fas fa-external-link-alt" style="margin-right: 0.5rem; color: #22c55e;"></i>
                                    External Ad
                                </div>
                                <div class="selection-card-desc">
                                    Third-party advertisements from other companies
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="ad_type" id="ad_type" value="{{ old('ad_type') }}" required>
                        @error('ad_type')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ad Placement <span class="required">*</span></label>
                        <div class="selection-cards">
                            <div class="selection-card {{ old('placement') === 'home_banner' ? 'selected' : '' }}" data-placement="home_banner">
                                <div class="selection-card-title">
                                    <i class="fas fa-home" style="margin-right: 0.5rem; color: #667eea;"></i>
                                    Home Banner
                                </div>
                                <div class="selection-card-desc">
                                    Main banner on the app's home page
                                </div>
                            </div>
                            <div class="selection-card {{ old('placement') === 'service_list' ? 'selected' : '' }}" data-placement="service_list">
                                <div class="selection-card-title">
                                    <i class="fas fa-list" style="margin-right: 0.5rem; color: #22c55e;"></i>
                                    Service List
                                </div>
                                <div class="selection-card-desc">
                                    Appears in service listing pages
                                </div>
                            </div>
                            <div class="selection-card {{ old('placement') === 'service_detail' ? 'selected' : '' }}" data-placement="service_detail">
                                <div class="selection-card-title">
                                    <i class="fas fa-info-circle" style="margin-right: 0.5rem; color: #f59e0b;"></i>
                                    Service Detail
                                </div>
                                <div class="selection-card-desc">
                                    Shows on service detail pages
                                </div>
                            </div>
                            <div class="selection-card {{ old('placement') === 'profile' ? 'selected' : '' }}" data-placement="profile">
                                <div class="selection-card-title">
                                    <i class="fas fa-user" style="margin-right: 0.5rem; color: #8b5cf6;"></i>
                                    Profile Page
                                </div>
                                <div class="selection-card-desc">
                                    Displays on user profile pages
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="placement" id="placement" value="{{ old('placement') }}" required>
                        @error('placement')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-image"></i>
                        Advertisement Image
                    </h3>
                    <p class="section-subtitle">Upload an eye-catching image for your advertisement</p>

                    <div class="form-group">
                        <label class="form-label">
                            Ad Image <span class="required">*</span>
                        </label>
                        <div class="image-upload-area" id="imageUploadArea">
                            <div id="uploadPrompt">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <div class="upload-text">Click to upload or drag and drop</div>
                                <div class="upload-hint">JPEG, PNG, JPG, GIF, WebP (Max 5MB)</div>
                            </div>
                            <div id="imagePreview" style="display: none;">
                                <img id="previewImg" class="image-preview" alt="Preview">
                                <div style="margin-top: 1rem;">
                                    <button type="button" id="changeImageBtn" class="btn-modern btn-secondary">
                                        <i class="fas fa-exchange-alt"></i> Change Image
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;" required>
                        @error('image')
                        <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Target URL Section -->
                <div class="form-section" id="targetUrlSection" style="display: none;">
                    <h3 class="section-title">
                        <i class="fas fa-link"></i>
                        Target URL
                    </h3>
                    <p class="section-subtitle">Where should users go when they click this ad?</p>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Target URL</label>
                            <input type="url" name="target_url" value="{{ old('target_url') }}"
                                class="form-input" placeholder="https://example.com" oninput="updatePreview()">
                            <div class="form-help">Full URL including http:// or https://</div>
                            @error('target_url')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Advanced Targeting Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-bullseye"></i>
                        Advanced Targeting
                    </h3>
                    <p class="section-subtitle">Define who should see this advertisement</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Target User Types</label>
                            <div class="multi-select-group">
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_user_types[]" value="customer" id="target_customers" checked>
                                    <label for="target_customers">Customers</label>
                                </div>
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_user_types[]" value="worker" id="target_providers">
                                    <label for="target_providers">Service Providers</label>
                                </div>
                            </div>
                            <div class="form-help">Select which user types should see this ad</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Target Activity Levels</label>
                            <div class="multi-select-group">
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_activity_levels[]" value="new" id="target_new">
                                    <label for="target_new">New Users</label>
                                </div>
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_activity_levels[]" value="medium" id="target_medium" checked>
                                    <label for="target_medium">Active Users</label>
                                </div>
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_activity_levels[]" value="high" id="target_high">
                                    <label for="target_high">Power Users</label>
                                </div>
                                <div class="multi-select-item">
                                    <input type="checkbox" name="target_activity_levels[]" value="vip" id="target_vip">
                                    <label for="target_vip">VIP Users</label>
                                </div>
                            </div>
                            <div class="form-help">Target users based on their activity level</div>
                        </div>
                    </div>
                </div>

                <!-- Budget & Performance Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-chart-line"></i>
                        Budget & Performance Controls
                    </h3>
                    <p class="section-subtitle">Set budget limits and performance thresholds</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Daily Budget Limit</label>
                            <input type="number" name="max_daily_budget" value="{{ old('max_daily_budget') }}"
                                class="form-input" min="0" step="0.01" placeholder="0.00">
                            <div class="form-help">(Optional) Maximum daily spend. Leave empty for unlimited.</div>
                            @error('max_daily_budget')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Minimum CTR Threshold</label>
                            <input type="number" name="min_ctr_threshold" value="{{ old('min_ctr_threshold') }}"
                                class="form-input" min="0" max="100" step="0.1" placeholder="1.0">
                            <div class="form-help">(Optional) Auto-pause if CTR falls below this %</div>
                            @error('min_ctr_threshold')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" name="auto_pause_enabled" id="auto_pause_enabled" class="checkbox-input" value="1" {{ old('auto_pause_enabled') ? 'checked' : '' }}>
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
                            value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="checkbox-label">
                            <i class="fas fa-play-circle" style="color: #22c55e; margin-right: 0.25rem;"></i>
                            Activate this advertisement immediately
                        </label>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Start Date (Optional)</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="form-input" min="{{ date('Y-m-d') }}">
                            <div class="form-help">When should this ad start showing?</div>
                            @error('start_date')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">End Date (Optional)</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                class="form-input" min="{{ date('Y-m-d') }}">
                            <div class="form-help">When should this ad stop showing?</div>
                            @error('end_date')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" value="{{ old('display_order', 0) }}"
                                class="form-input" min="0" max="999" placeholder="0">
                            <div class="form-help">Lower numbers show first (0 = highest priority)</div>
                            @error('display_order')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
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
                    <p class="section-subtitle">Internal notes for admin reference</p>

                    <div class="form-row full-width">
                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="admin_notes" class="form-textarea" placeholder="Add any internal notes about this advertisement..."
                                maxlength="1000">{{ old('admin_notes') }}</textarea>
                            <div class="form-help">Private notes visible only to administrators (max 1000 characters)</div>
                            @error('admin_notes')
                            <div class="form-help" style="color: #ef4444;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin_get_ads') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Create Advertisement
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Panel -->
        <div class="preview-panel">
            <div class="preview-header">
                <h3 class="preview-title">
                    <i class="fas fa-eye"></i>
                    Live Preview
                </h3>
                <p class="preview-subtitle">See how your ad will appear</p>
            </div>

            <!-- Ad Mockup -->
            <div class="ad-mockup">
                <div class="mockup-type" id="preview-type">Internal</div>
                <div class="mockup-title" id="preview-title">Your Advertisement Title</div>
                <div class="mockup-description" id="preview-description">Advertisement description will appear here</div>
                <div style="margin-top: 1rem; padding: 0.5rem; background: rgba(255,255,255,0.2); border-radius: 6px;">
                    <i class="fas fa-mouse-pointer"></i> <span id="preview-action">Click Action</span>
                </div>
            </div>

            <!-- Preview Details -->
            <div class="preview-details">
                <div class="detail-item">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value" id="detail-type">Not Selected</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Placement:</span>
                    <span class="detail-value" id="detail-placement">Not Selected</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Priority:</span>
                    <span class="detail-value" id="detail-priority">Normal</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Target:</span>
                    <span class="detail-value" id="detail-target">Customers</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" style="color: #22c55e;" id="detail-status">Ready to Create</span>
                </div>
            </div>

            <!-- Tips Section -->
            <div style="margin-top: 1.5rem; padding: 1rem; background: #f0f9ff; border: 2px solid #0ea5e9; border-radius: 8px;">
                <h4 style="margin: 0 0 0.5rem 0; color: #0c4a6e; font-size: 0.9rem;">
                    <i class="fas fa-lightbulb"></i> Pro Tips
                </h4>
                <div style="font-size: 0.8rem; color: #0369a1; line-height: 1.4;">
                    • Use compelling titles to increase click rates<br>
                    • High priority ads show first<br>
                    • Target specific user types for better results<br>
                    • Set budget limits to control spending<br>
                    • Monitor performance and adjust as needed
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Enhanced ad form loaded');

        // Form elements
        const form = document.getElementById('adForm');
        const submitBtn = document.getElementById('submitBtn');

        // Priority Selection
        const priorityOptions = document.querySelectorAll('.priority-option');
        const priorityInput = document.getElementById('admin_priority');

        priorityOptions.forEach(option => {
            option.addEventListener('click', function() {
                priorityOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                priorityInput.value = this.dataset.priority;
                updatePreview();
            });
        });

        // Ad Type Selection
        const typeCards = document.querySelectorAll('[data-type]');
        const adTypeInput = document.getElementById('ad_type');
        const targetUrlSection = document.getElementById('targetUrlSection');

        typeCards.forEach(card => {
            card.addEventListener('click', function() {
                typeCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                adTypeInput.value = this.dataset.type;

                // Show/hide target URL section
                if (this.dataset.type === 'external') {
                    targetUrlSection.style.display = 'block';
                } else {
                    targetUrlSection.style.display = 'none';
                }

                updatePreview();
            });
        });

        // Placement Selection
        const placementCards = document.querySelectorAll('[data-placement]');
        const placementInput = document.getElementById('placement');

        placementCards.forEach(card => {
            card.addEventListener('click', function() {
                placementCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                placementInput.value = this.dataset.placement;
                updatePreview();
            });
        });

        // Image Upload Handling
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imageInput = document.getElementById('imageInput');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const changeImageBtn = document.getElementById('changeImageBtn');

        // Click to upload
        imageUploadArea.addEventListener('click', function(e) {
            if (e.target.id !== 'changeImageBtn') {
                imageInput.click();
            }
        });

        // Change image button
        changeImageBtn.addEventListener('click', function() {
            imageInput.click();
        });

        // Drag and drop
        imageUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        imageUploadArea.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });

        imageUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                handleImageSelect(files[0]);
            }
        });

        // File input change
        imageInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleImageSelect(this.files[0]);
            }
        });

        function handleImageSelect(file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image must be smaller than 5MB.');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadPrompt.style.display = 'none';
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            console.log('Form submission started');

            // Validation
            const title = document.querySelector('input[name="title"]')?.value;
            const adType = document.querySelector('input[name="ad_type"]')?.value;
            const placement = document.querySelector('input[name="placement"]')?.value;
            const image = document.querySelector('input[name="image"]')?.files[0];

            let hasErrors = false;

            if (!title || title.trim() === '') {
                alert('Advertisement title is required');
                hasErrors = true;
            }

            if (!adType) {
                alert('Please select an advertisement type');
                hasErrors = true;
            }

            if (!placement) {
                alert('Please select an ad placement');
                hasErrors = true;
            }

            if (!image) {
                alert('Please select an image');
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // Update button state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Advertisement...';
        });

        // Initialize preview
        updatePreview();
    });

    // Update live preview
    function updatePreview() {
        const title = document.querySelector('input[name="title"]')?.value || 'Your Advertisement Title';
        const description = document.querySelector('textarea[name="description"]')?.value || 'Advertisement description will appear here';
        const adType = document.querySelector('input[name="ad_type"]')?.value || '';
        const placement = document.querySelector('input[name="placement"]')?.value || '';
        const priority = document.querySelector('input[name="admin_priority"]')?.value || '2';
        const targetUrl = document.querySelector('input[name="target_url"]')?.value || '';

        // Update mockup
        document.getElementById('preview-title').textContent = title;
        document.getElementById('preview-description').textContent = description;
        document.getElementById('preview-type').textContent = adType === 'internal' ? 'Internal' : adType === 'external' ? 'External' : 'Type';

        // Update action text
        const actionElement = document.getElementById('preview-action');
        if (adType === 'external' && targetUrl) {
            actionElement.innerHTML = '<i class="fas fa-external-link-alt"></i> Visit Website';
        } else if (adType === 'internal') {
            actionElement.innerHTML = '<i class="fas fa-mouse-pointer"></i> View Feature';
        } else {
            actionElement.innerHTML = '<i class="fas fa-mouse-pointer"></i> Click Action';
        }

        // Update details
        document.getElementById('detail-type').textContent = adType ? (adType === 'internal' ? 'Internal Ad' : 'External Ad') : 'Not Selected';
        document.getElementById('detail-placement').textContent = placement ? formatPlacement(placement) : 'Not Selected';

        const priorityNames = {
            '1': 'Low',
            '2': 'Normal',
            '3': 'High',
            '4': 'Urgent'
        };
        document.getElementById('detail-priority').textContent = priorityNames[priority] || 'Normal';

        // Update target info
        const targetTypes = [];
        if (document.querySelector('input[name="target_user_types[]"][value="customer"]')?.checked) {
            targetTypes.push('Customers');
        }
        if (document.querySelector('input[name="target_user_types[]"][value="worker"]')?.checked) {
            targetTypes.push('Providers');
        }
        document.getElementById('detail-target').textContent = targetTypes.length > 0 ? targetTypes.join(', ') : 'All Users';
    }

    function formatPlacement(placement) {
        const placements = {
            'home_banner': 'Home Banner',
            'service_list': 'Service List',
            'service_detail': 'Service Detail',
            'profile': 'Profile Page'
        };
        return placements[placement] || placement;
    }
</script>

@endsection