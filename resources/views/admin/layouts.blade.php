<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>@yield('page-title', 'Dashboard') - Goodone Admin</title>
  <meta name="description" content="Professional admin panel for Goodone service platform" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href='{{asset("assets2/img/favicon/favicon.ico")}}' />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.min.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href='{{asset("assets2/vendor/css/core.css")}}' class="template-customizer-core-css" />
  <link rel="stylesheet" href='{{asset("assets2/vendor/css/theme-default.css")}}' class="template-customizer-theme-css" />
  <link rel="stylesheet" href='{{asset("assets2/css/demo.css")}}' />

  <!-- MODERN ADMIN STYLES -->
  <style>
    :root {
      --primary-color: #667eea;
      --primary-dark: #5a67d8;
      --secondary-color: #764ba2;
      --success-color: #22c55e;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --info-color: #3b82f6;
      --light-bg: #f8fafc;
      --white: #ffffff;
      --text-primary: #1a202c;
      --text-secondary: #718096;
      --border-color: #e2e8f0;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: var(--light-bg);
      color: var(--text-primary);
      line-height: 1.6;
      font-weight: 400;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* HIDE OLD THEME COMPLETELY */
    .layout-wrapper,
    .layout-container,
    .layout-page,
    .content-wrapper,
    .navbar,
    .layout-menu {
      display: none !important;
    }

    /* MODERN ADMIN LAYOUT */
    .modern-admin-layout {
      display: flex;
      min-height: 100vh;
      background: var(--light-bg);
    }

    /* ================ MODERN SIDEBAR ================ */
    .modern-sidebar {
      width: 280px;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: white;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 1000;
      transition: var(--transition);
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modern-sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .modern-sidebar::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
    }

    .modern-sidebar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

    .sidebar-header {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
      text-align: center;
      background: rgba(0, 0, 0, 0.1);
    }

    .sidebar-logo {
      font-size: 1.875rem;
      font-weight: 800;
      background: linear-gradient(45deg, #fff, #e2e8f0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
    }

    .sidebar-subtitle {
      font-size: 0.875rem;
      opacity: 0.85;
      font-weight: 500;
    }

    .nav-menu {
      padding: 1.5rem 0;
    }

    .nav-section {
      margin-bottom: 2rem;
    }

    .nav-section-title {
      padding: 0.75rem 1.5rem;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      opacity: 0.7;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 0.75rem;
    }

    .nav-item {
      margin: 0.25rem 0;
      position: relative;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.875rem 1.5rem;
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      transition: var(--transition);
      position: relative;
      font-weight: 500;
      border-radius: 0;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
      padding-left: 2rem;
      text-decoration: none;
      transform: translateX(4px);
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border-right: 4px solid #fff;
      font-weight: 600;
    }

    .nav-link i {
      width: 22px;
      margin-right: 1rem;
      text-align: center;
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .nav-link.active i {
      opacity: 1;
    }

    .nav-badge {
      background: var(--danger-color);
      color: white;
      font-size: 0.75rem;
      padding: 0.125rem 0.5rem;
      border-radius: 12px;
      margin-left: auto;
      min-width: 20px;
      text-align: center;
      font-weight: 600;
    }

    /* Submenu Styles */
    .nav-submenu {
      background: rgba(0, 0, 0, 0.15);
      margin-left: 2rem;
      border-left: 2px solid rgba(255, 255, 255, 0.2);
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-item.has-submenu>.nav-link::after {
      content: '\f054';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      margin-left: auto;
      transition: transform 0.3s ease;
      font-size: 0.8rem;
      opacity: 0.7;
    }

    .nav-item.has-submenu.open>.nav-link::after {
      transform: rotate(90deg);
    }

    .nav-item.has-submenu.open .nav-submenu {
      max-height: 500px;
    }

    .nav-submenu .nav-link {
      padding: 0.75rem 1rem;
      font-size: 0.9rem;
      opacity: 0.9;
      font-weight: 400;
    }

    .nav-submenu .nav-link:hover {
      padding-left: 1.5rem;
    }

    .nav-submenu .nav-link i {
      font-size: 0.9rem;
    }

    /* Logout Section */
    .logout-section {
      margin-top: auto;
      padding: 1.5rem;
      border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .logout-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.1);
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      color: white;
      text-decoration: none;
      transition: var(--transition);
      font-weight: 600;
      font-size: 0.95rem;
    }

    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.4);
      transform: translateY(-2px);
      text-decoration: none;
      color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ================ MAIN CONTENT AREA ================ */
    .modern-main-content {
      margin-left: 280px;
      flex: 1;
      background: var(--light-bg);
      min-height: 100vh;
      transition: var(--transition);
      display: flex;
      flex-direction: column;
    }

    .content-header {
      background: var(--white);
      padding: 2rem 2.5rem;
      border-bottom: 1px solid var(--border-color);
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .page-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0 0 0.5rem 0;
      line-height: 1.2;
    }

    .page-subtitle {
      color: var(--text-secondary);
      font-size: 1rem;
      margin: 0;
      font-weight: 400;
    }

    .content-body {
      padding: 2.5rem;
      flex: 1;
    }

    /* ================ MODERN CARDS ================ */
    .modern-card {
      background: var(--white);
      border-radius: 16px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border-color);
      overflow: hidden;
      transition: var(--transition);
    }

    .modern-card:hover {
      box-shadow: var(--shadow-lg);
    }

    .card-header-modern {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid var(--border-color);
      background: #f8fafc;
    }

    .card-title-modern {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0;
    }

    /* ================ SIMPLE PAGINATION FIX ================ */
    /* Hide first and last pagination items (arrows) */
    .pagination .page-item:first-child,
    .pagination .page-item:last-child {
      display: none !important;
    }

    /* Style the remaining pagination nicely */
    .pagination {
      justify-content: center;
      margin: 2rem 0;
    }

    .pagination .page-link {
      color: #4a5568;
      background: white;
      border: 1px solid #e2e8f0;
      padding: 0.75rem 1rem;
      margin: 0 0.125rem;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
      background: #f8fafc;
      border-color: #667eea;
      color: #667eea;
    }

    .pagination .page-item.active .page-link {
      background: #667eea;
      border-color: #667eea;
      color: white;
      font-weight: 600;
    }

    /* ================ RESPONSIVE DESIGN ================ */
    .mobile-menu-toggle {
      display: none;
      position: fixed;
      top: 1rem;
      left: 1rem;
      z-index: 1001;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 0.875rem;
      font-size: 1.2rem;
      cursor: pointer;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }

    .mobile-menu-toggle:hover {
      background: var(--primary-dark);
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      .modern-sidebar {
        transform: translateX(-100%);
        width: 280px;
      }

      .modern-main-content {
        margin-left: 0;
      }

      .modern-sidebar.mobile-open {
        transform: translateX(0);
      }

      .mobile-menu-toggle {
        display: block;
      }

      .content-header {
        padding: 1.5rem 1rem 1.5rem 4rem;
      }

      .content-body {
        padding: 1.5rem;
      }

      .page-title {
        font-size: 1.5rem;
      }
    }

    /* ================ ALERTS & NOTIFICATIONS ================ */
    .alert {
      padding: 1rem 1.5rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      border: none;
      font-weight: 500;
    }

    .alert-success {
      background: #dcfce7;
      color: #166534;
      border-left: 4px solid var(--success-color);
    }

    .alert-error,
    .alert-danger {
      background: #fee2e2;
      color: #991b1b;
      border-left: 4px solid var(--danger-color);
    }

    .alert-warning {
      background: #fef3c7;
      color: #92400e;
      border-left: 4px solid var(--warning-color);
    }

    .alert-info {
      background: #dbeafe;
      color: #1e40af;
      border-left: 4px solid var(--info-color);
    }

    /* ================ MODERN BUTTONS ================ */
    .btn-modern {
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
      cursor: pointer;
      font-size: 0.95rem;
    }

    .btn-primary {
      background: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      color: white;
      text-decoration: none;
      transform: translateY(-1px);
      box-shadow: var(--shadow-lg);
    }

    /* ================ LOADING STATES ================ */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }

    .loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 2px solid #f3f3f3;
      border-top: 2px solid var(--primary-color);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* ================ UTILITIES ================ */
    .text-primary {
      color: var(--text-primary) !important;
    }

    .text-secondary {
      color: var(--text-secondary) !important;
    }

    .bg-primary {
      background-color: var(--primary-color) !important;
    }

    .bg-white {
      background-color: var(--white) !important;
    }

    .shadow {
      box-shadow: var(--shadow) !important;
    }

    .shadow-lg {
      box-shadow: var(--shadow-lg) !important;
    }

    .rounded {
      border-radius: 8px !important;
    }

    .rounded-lg {
      border-radius: 12px !important;
    }

    .rounded-xl {
      border-radius: 16px !important;
    }

    /* Hide any remaining old elements */
    .layout-wrapper,
    .layout-container,
    .layout-page,
    .content-wrapper,
    .navbar,
    .layout-menu,
    .menu,
    .menu-item,
    .menu-link {
      display: none !important;
    }
  </style>

  @stack('styles')
</head>

<body>
  <!-- Mobile Menu Toggle -->
  <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
    <i class="fas fa-bars"></i>
  </button>

  <div class="modern-admin-layout">
    <!-- Modern Sidebar -->
    <nav class="modern-sidebar" id="modernSidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">Goodone Admin</div>
        <div class="sidebar-subtitle">Management Panel</div>
      </div>

      <div class="nav-menu">
        <!-- Dashboard Section -->
        <div class="nav-section">
          <div class="nav-section-title">Dashboard</div>
          <div class="nav-item">
            <a href="{{ route('admin_home') }}" class="nav-link {{ Request::routeIs('admin_home') ? 'active' : '' }}">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </a>
          </div>
        </div>

        <!-- Analytics Section -->
        <div class="nav-section">
          <div class="nav-section-title">Analytics</div>
          <div class="nav-item">
            <a href="{{ route('admin_platform_statistics') }}" class="nav-link {{ Request::routeIs('admin_platform_statistics') ? 'active' : '' }}">
              <i class="fas fa-chart-line"></i>
              <span>Platform Statistics</span>
            </a>
          </div>
        </div>

        <!-- App Configuration Section -->
        <div class="nav-section">
          <div class="nav-section-title">App Configuration</div>
          <div class="nav-item">
            <a href="{{ route('admin_get_categories') }}" class="nav-link {{ Request::routeIs('admin_get_categories', 'admin_create_category_form', 'admin_edit_category_form') ? 'active' : '' }}">
              <i class="fas fa-layer-group"></i>
              <span>Categories</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="{{ route('admin_get_coupons') }}" class="nav-link {{ Request::routeIs('admin_get_coupons', 'admin_create_coupon_form', 'admin_edit_coupon_form') ? 'active' : '' }}">
              <i class="fas fa-tags"></i>
              <span>Coupons</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="{{ route('admin_get_regional_taxes') }}" class="nav-link {{ Request::routeIs('admin_get_regional_taxes', 'admin_create_regional_tax_form', 'admin_edit_regional_tax_form', 'admin_tax_calculator') ? 'active' : '' }}">
              <i class="fas fa-percentage"></i>
              <span>Regional Taxes</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="{{ route('admin_get_platform_fees') }}" class="nav-link {{ Request::routeIs('admin_get_platform_fees', 'admin_fees_calculator', 'admin_update_platform_fees', 'admin_reset_platform_fees') ? 'active' : '' }}">
              <i class="fas fa-dollar-sign"></i>
              <span>Platform Fees</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="{{ route('admin_get_default_images') }}" class="nav-link {{ Request::routeIs('admin_get_default_images', 'admin_update_default_images', 'admin_reset_default_images') ? 'active' : '' }}">
              <i class="fas fa-image"></i>
              <span>Default Images</span>
            </a>
          </div>

          <!-- User Management Section -->
          <div class="nav-section">
            <div class="nav-section-title">User Management</div>
            <div class="nav-item">
              <a href="{{ route('admin_get_customers') }}" class="nav-link {{ Request::routeIs('admin_get_customers', 'admin_show_customer', 'admin_edit_customer_form') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Customers</span>
              </a>
            </div>
            <div class="nav-item">
              <a href="#" onclick="alert('Service Provider management coming soon!')" class="nav-link" style="opacity: 0.6;">
                <i class="fas fa-user-tie"></i>
                <span>Service Providers</span>
              </a>
            </div>
          </div>


          <!-- Features Coming Soon -->
          <div class="nav-section">
            <div class="nav-section-title">Coming Soon</div>
            <div class="nav-item">
              <a href="#" onclick="alert('Order management coming soon!')" class="nav-link" style="opacity: 0.6;">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
              </a>
            </div>
            <div class="nav-item">
              <a href="#" onclick="alert('Service management coming soon!')" class="nav-link" style="opacity: 0.6;">
                <i class="fas fa-cogs"></i>
                <span>Services</span>
              </a>
            </div>
            <div class="nav-item">
              <a href="#" onclick="alert('Withdrawal management coming soon!')" class="nav-link" style="opacity: 0.6;">
                <i class="fas fa-money-bill-wave"></i>
                <span>Withdrawals</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Logout Section -->
        <div class="logout-section">
          <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="logout-btn">
              <i class="fas fa-sign-out-alt"></i>
              <span>Logout</span>
            </button>
          </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="modern-main-content">
      <div class="content-header">
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        <p class="page-subtitle">@yield('page-subtitle', 'Welcome back! Here\'s what\'s happening with your platform.')</p>
      </div>

      <div class="content-body">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          {{ session('error') }}
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

        @yield('content')
      </div>
    </main>
  </div>

  <!-- Core JS -->
  <script src='{{asset("assets2/vendor/libs/jquery/jquery.js")}}'></script>
  <script src='{{asset("assets2/vendor/libs/popper/popper.js")}}'></script>
  <script src='{{asset("assets2/vendor/js/bootstrap.js")}}'></script>
  <script src='{{asset("assets2/vendor/libs/perfect-scrollbar/perfect-scrollbar.js")}}'></script>
  <script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.min.js"></script>
  <script src='{{asset("assets2/vendor/js/menu.js")}}'></script>
  <script src='{{asset("assets2/js/main.js")}}'></script>

  <!-- Modern Admin JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Handle submenu toggles
      const menuItems = document.querySelectorAll('.nav-item.has-submenu > .nav-link');

      menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          const parentItem = this.parentElement;
          const isOpen = parentItem.classList.contains('open');

          // Close all other submenus
          document.querySelectorAll('.nav-item.has-submenu.open').forEach(openItem => {
            if (openItem !== parentItem) {
              openItem.classList.remove('open');
            }
          });

          // Toggle current submenu
          parentItem.classList.toggle('open', !isOpen);
        });
      });

      // Initialize DataTables if present
      if (typeof DataTable !== 'undefined') {
        try {
          new DataTable('table');
        } catch (e) {
          console.log('DataTable initialization skipped');
        }
      }
    });

    // Mobile menu toggle
    function toggleMobileMenu() {
      const sidebar = document.getElementById('modernSidebar');
      sidebar.classList.toggle('mobile-open');
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
      const sidebar = document.getElementById('modernSidebar');
      const toggle = document.querySelector('.mobile-menu-toggle');

      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('mobile-open');
      }
    });
  </script>

  @stack('scripts')
</body>

</html>