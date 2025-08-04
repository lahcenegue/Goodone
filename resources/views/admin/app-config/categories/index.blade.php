@extends('admin.layouts')

@section('page-title', 'Categories Management')
@section('page-subtitle', 'Manage service categories and subcategories for your platform.')

@section('content')
<style>
    /* Categories Management Styles */
    .categories-container {
        max-width: 100%;
    }

    .categories-header {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .category-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .category-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 1rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
    }

    .category-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .category-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #718096;
    }

    .category-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
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
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #edf2f7;
        color: #2d3748;
        text-decoration: none;
    }

    .btn-danger {
        background: #fed7d7;
        color: #c53030;
        border: 1px solid #feb2b2;
    }

    .btn-danger:hover {
        background: #fbb6ce;
        color: #97266d;
        text-decoration: none;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        color: #718096;
        font-size: 0.875rem;
    }

    .search-section {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .search-input {
        flex: 1;
        min-width: 300px;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .categories-grid {
            grid-template-columns: 1fr;
        }

        .search-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            min-width: 100%;
        }

        .category-actions {
            justify-content: center;
        }
    }
</style>

<div class="categories-container">
    <!-- Welcome Banner -->
    <div class="categories-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="color: white; margin: 0; font-size: 1.75rem; font-weight: 700;">
                    <i class="fas fa-layer-group me-2"></i>
                    Categories Management
                </h2>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0; font-size: 1rem;">
                    Organize your platform services with categories and subcategories
                </p>
            </div>
            <a href="{{ route('admin_create_category_form') }}" class="btn-modern" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-plus"></i>
                Add New Category
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($stats))
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-value">{{ $stats['total_categories'] }}</div>
            <div class="stats-label">Total Categories</div>
        </div>
        <div class="stats-card">
            <div class="stats-value">{{ $stats['total_subcategories'] }}</div>
            <div class="stats-label">Total Subcategories</div>
        </div>
        <div class="stats-card">
            <div class="stats-value">{{ $stats['categories_with_services'] }}</div>
            <div class="stats-label">Categories with Services</div>
        </div>
        <div class="stats-card">
            <div class="stats-value" style="font-size: 1.25rem;">{{ $stats['most_used_category'] }}</div>
            <div class="stats-label">Most Used Category</div>
        </div>
    </div>
    @endif

    <!-- Search and Filters -->
    <div class="modern-card" style="margin-bottom: 2rem;">
        <div style="padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem 0; color: #2d3748; font-size: 1.25rem; font-weight: 600;">
                <i class="fas fa-search me-2"></i>
                Search Categories
            </h3>
            <form method="GET" action="{{ route('admin_get_categories') }}">
                <div class="search-section">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search categories by name..." 
                        value="{{ $search ?? '' }}" 
                        class="search-input"
                    >
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    @if($search)
                    <a href="{{ route('admin_get_categories') }}" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    @if($categories->count() > 0)
    <div class="categories-grid">
        @foreach($categories as $category)
        <div class="category-card">
            <!-- Category Image -->
            @if($category->image)
            <img 
                src="{{ asset('storage/images/' . $category->image) }}" 
                alt="{{ $category->name }}" 
                class="category-image"
                onerror="this.src='{{ asset('storage/images/default-category.png') }}'"
            >
            @else
            <div class="category-image" style="display: flex; align-items: center; justify-content: center; background: #f8fafc;">
                <i class="fas fa-image" style="font-size: 3rem; color: #cbd5e0;"></i>
            </div>
            @endif

            <!-- Category Info -->
            <div class="category-name">{{ $category->name }}</div>
            
            <div class="category-stats">
                <span><i class="fas fa-list"></i> {{ $category->subcategories_count }} subcategories</span>
                <span><i class="fas fa-briefcase"></i> {{ $category->services_count ?? 0 }} services</span>
            </div>

            <!-- Category Actions -->
            <div class="category-actions">
                <a href="{{ route('admin_edit_category_form', $category) }}" class="btn-modern btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route('admin_get_subcategories', $category) }}" class="btn-modern btn-secondary">
                    <i class="fas fa-list"></i>
                    Subcategories ({{ $category->subcategories_count }})
                </a>
                @if($category->services_count == 0)
                <form method="POST" action="{{ route('admin_delete_category', $category) }}" style="display: inline;" 
                      onsubmit="return confirm('Are you sure you want to delete this category and all its subcategories?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modern btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </form>
                @else
                <button class="btn-modern btn-danger" disabled title="Cannot delete category with existing services">
                    <i class="fas fa-lock"></i>
                    Protected
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 2rem;">
        {{ $categories->links() }}
    </div>
    @endif

    @else
    <!-- Empty State -->
    <div class="modern-card">
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <h3 style="color: #4a5568; margin-bottom: 1rem;">No Categories Found</h3>
            <p style="margin-bottom: 2rem;">
                @if($search)
                    No categories match your search "{{ $search }}". Try a different search term.
                @else
                    Start organizing your platform by creating your first category.
                @endif
            </p>
            @if(!$search)
            <a href="{{ route('admin_create_category_form') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus"></i>
                Create Your First Category
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to buttons
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                
                // Re-enable after 3 seconds in case of network issues
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 3000);
            }
        });
    });

    // Image error handling
    const images = document.querySelectorAll('.category-image');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'flex';
            this.style.alignItems = 'center';
            this.style.justifyContent = 'center';
            this.style.background = '#f8fafc';
            this.innerHTML = '<i class="fas fa-image" style="font-size: 3rem; color: #cbd5e0;"></i>';
        });
    });
});
</script>
@endpush