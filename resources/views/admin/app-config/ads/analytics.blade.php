@extends('admin.layouts')

@section('page-title', 'Ad Analytics - ' . $ad->title)
@section('page-subtitle', 'Detailed performance analytics for your advertisement')

@section('content')
<style>
    /* Analytics Page Styles */
    .analytics-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .analytics-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .ad-preview {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .header-info h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .header-meta {
        opacity: 0.9;
        font-size: 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #718096;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .stat-change {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .stat-change.positive {
        background: #dcfce7;
        color: #166534;
    }

    .stat-change.neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    .analytics-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .comparison-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .comparison-item {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    .comparison-label {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }

    .comparison-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
    }

    .performance-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        gap: 0.5rem;
    }

    .performance-excellent {
        background: #dcfce7;
        color: #166534;
    }

    .performance-good {
        background: #dbeafe;
        color: #1e40af;
    }

    .performance-average {
        background: #fef3c7;
        color: #92400e;
    }

    .performance-poor {
        background: #fee2e2;
        color: #991b1b;
    }

    .time-period-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .tab-button {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .tab-button.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .tab-button:hover:not(.active) {
        background: #f8fafc;
    }

    .actions-section {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
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
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5a67d8;
        text-decoration: none;
        color: white;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
        text-decoration: none;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .analytics-header {
            flex-direction: column;
            text-align: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .comparison-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="analytics-container">
    <!-- Analytics Header -->
    <div class="analytics-header">
        <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" class="ad-preview">
        <div class="header-info">
            <h1>{{ $ad->title }}</h1>
            <div class="header-meta">
                {{ $ad->formatted_ad_type }} • {{ $ad->formatted_placement }} •
                Created {{ $ad->created_at->format('M d, Y') }}
            </div>
            <div style="margin-top: 1rem;">
                <span class="performance-badge performance-{{ strtolower(str_replace(' ', '-', $analytics['performance_metrics']['engagement_level'])) }}">
                    <i class="fas fa-chart-line"></i>
                    {{ $analytics['performance_metrics']['engagement_level'] }} Performance
                </span>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($analytics['basic_stats']['total_views']) }}</div>
            <div class="stat-label">Total Views</div>
            <div class="stat-change neutral">
                {{ $analytics['basic_stats']['avg_daily_views'] }}/day avg
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-value">{{ number_format($analytics['basic_stats']['total_clicks']) }}</div>
            <div class="stat-label">Total Clicks</div>
            <div class="stat-change neutral">
                {{ $analytics['basic_stats']['avg_daily_clicks'] }}/day avg
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-value">{{ $analytics['basic_stats']['ctr'] }}%</div>
            <div class="stat-label">Click-Through Rate</div>
            <div class="stat-change {{ $analytics['basic_stats']['ctr'] >= $analytics['comparison_data']['platform_avg_ctr'] ? 'positive' : 'neutral' }}">
                vs {{ $analytics['comparison_data']['platform_avg_ctr'] }}% platform avg
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-value">{{ $analytics['basic_stats']['days_active'] }}</div>
            <div class="stat-label">Days Active</div>
            <div class="stat-change neutral">
                Since {{ $ad->created_at->format('M d') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-value">{{ $analytics['performance_metrics']['performance_score'] }}</div>
            <div class="stat-label">Performance Score</div>
            <div class="stat-change neutral">
                Out of 10
            </div>
        </div>
    </div>

    <!-- Performance Comparison -->
    <div class="analytics-section">
        <h3 class="section-title">
            <i class="fas fa-chart-bar"></i>
            Performance Comparison
        </h3>

        <div class="comparison-grid">
            <div class="comparison-item">
                <div class="comparison-label">Your Ad CTR</div>
                <div class="comparison-value">{{ $analytics['basic_stats']['ctr'] }}%</div>
            </div>

            <div class="comparison-item">
                <div class="comparison-label">Platform Average</div>
                <div class="comparison-value">{{ $analytics['comparison_data']['platform_avg_ctr'] }}%</div>
            </div>

            <div class="comparison-item">
                <div class="comparison-label">{{ $ad->formatted_placement }} Average</div>
                <div class="comparison-value">{{ $analytics['comparison_data']['placement_avg_ctr'] }}%</div>
            </div>

            <div class="comparison-item">
                <div class="comparison-label">{{ $ad->formatted_ad_type }} Average</div>
                <div class="comparison-value">{{ $analytics['comparison_data']['ad_type_avg_ctr'] }}%</div>
            </div>
        </div>
    </div>

    <!-- Time Period Performance -->
    <div class="analytics-section">
        <h3 class="section-title">
            <i class="fas fa-calendar-alt"></i>
            Performance Over Time
        </h3>

        <div class="time-period-tabs">
            <button class="tab-button active" onclick="showPeriod('7days')">Last 7 Days</button>
            <button class="tab-button" onclick="showPeriod('30days')">Last 30 Days</button>
            <button class="tab-button" onclick="showPeriod('alltime')">All Time</button>
        </div>

        <div id="period-7days" class="period-content">
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['last_7_days']['views']) }}</div>
                    <div class="stat-label">Views (7 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['last_7_days']['clicks']) }}</div>
                    <div class="stat-label">Clicks (7 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $analytics['time_periods']['last_7_days']['ctr'] }}%</div>
                    <div class="stat-label">CTR (7 days)</div>
                </div>
            </div>
        </div>

        <div id="period-30days" class="period-content" style="display: none;">
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['last_30_days']['views']) }}</div>
                    <div class="stat-label">Views (30 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['last_30_days']['clicks']) }}</div>
                    <div class="stat-label">Clicks (30 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $analytics['time_periods']['last_30_days']['ctr'] }}%</div>
                    <div class="stat-label">CTR (30 days)</div>
                </div>
            </div>
        </div>

        <div id="period-alltime" class="period-content" style="display: none;">
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['all_time']['views']) }}</div>
                    <div class="stat-label">Total Views</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($analytics['time_periods']['all_time']['clicks']) }}</div>
                    <div class="stat-label">Total Clicks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $analytics['time_periods']['all_time']['ctr'] }}%</div>
                    <div class="stat-label">Overall CTR</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-section">
        <a href="{{ route('admin_edit_ad_form', $ad) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Edit Advertisement
        </a>

        <form method="POST" action="{{ route('admin_toggle_ad_status', $ad) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i class="fas {{ $ad->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                {{ $ad->is_active ? 'Deactivate' : 'Activate' }} Ad
            </button>
        </form>

        <a href="{{ route('admin_get_ads') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Ads
        </a>
    </div>
</div>

<script>
    function showPeriod(period) {
        // Hide all period contents
        document.querySelectorAll('.period-content').forEach(content => {
            content.style.display = 'none';
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected period content
        document.getElementById('period-' + period).style.display = 'block';

        // Add active class to clicked tab
        event.target.classList.add('active');
    }
</script>

@endsection