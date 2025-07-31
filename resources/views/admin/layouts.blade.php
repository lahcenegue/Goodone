<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Goodone Admin Panel</title>
  <meta name="description" content="Professional admin panel for Goodone service platform" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href='{{asset("assets2/img/favicon/favicon.ico")}}' />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href='{{asset("assets2/vendor/css/core.css")}}' class="template-customizer-core-css" />
  <link rel="stylesheet" href='{{asset("assets2/vendor/css/theme-default.css")}}' class="template-customizer-theme-css" />
  <link rel="stylesheet" href='{{asset("assets2/css/demo.css")}}' />

  <!-- Modern Admin Styles -->
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f8fafc;
      color: #2d3748;
      line-height: 1.6;
    }

    .admin-layout {
      display: flex;
      min-height: 100vh;
    }

    /* ================ SIDEBAR STYLES ================ */
    .sidebar {
      width: 280px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 1000;
      transition: all 0.3s ease;
      box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: rgba(255,255,255,0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.3);
      border-radius: 3px;
    }

    .sidebar-header {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      text-align: center;
    }

    .logo {
      font-size: 1.75rem;
      font-weight: 700;
      background: linear-gradient(45deg, #fff, #e2e8f0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .subtitle {
      font-size: 0.875rem;
      opacity: 0.8;
      margin-top: 0.25rem;
    }

    .nav-menu {
      padding: 1rem 0;
    }

    .nav-section {
      margin-bottom: 1.5rem;
    }

    .nav-section-title {
      padding: 0.75rem 1.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      opacity: 0.7;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      margin-bottom: 0.5rem;
    }

    .nav-item {
      margin: 0.25rem 0;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.75rem 1.5rem;
      color: rgba(255,255,255,0.9);
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
      border-radius: 0;
    }

    .nav-link:hover {
      background: rgba(255,255,255,0.1);
      color: white;
      padding-left: 2rem;
      text-decoration: none;
    }

    .nav-link.active {
      background: rgba(255,255,255,0.15);
      color: white;
      border-right: 4px solid #fff;
    }

    .nav-link i {
      width: 20px;
      margin-right: 1rem;
      text-align: center;
      font-size: 1.1rem;
    }

    .nav-submenu {
      background: rgba(0,0,0,0.1);
      margin-left: 2rem;
      margin-right: 0;
      border-left: 2px solid rgba(255,255,255,0.2);
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }

    .nav-submenu .nav-link {
      padding: 0.6rem 1rem;
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .nav-submenu .nav-link:hover {
      padding-left: 1.5rem;
    }

    .nav-submenu .nav-link i {
      font-size: 0.875rem;
    }

    /* Toggle functionality */
    .nav-item.has-submenu > .nav-link::after {
      content: '\f054';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      margin-left: auto;
      transition: transform 0.3s ease;
      font-size: 0.8rem;
    }

    .nav-item.has-submenu.open > .nav-link::after {
      transform: rotate(90deg);
    }

    .nav-item.has-submenu.open .nav-submenu {
      max-height: 500px;
    }

    /* Logout section */
    .logout-section {
      margin-top: auto;
      padding: 1.5rem;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .logout-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 0.875rem;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .logout-btn:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-1px);
      text-decoration: none;
      color: white;
    }

    /* Badge for notifications */
    .nav-badge {
      background: #ef4444;
      color: white;
      font-size: 0.75rem;
      padding: 0.125rem 0.375rem;
      border-radius: 10px;
      margin-left: auto;
      min-width: 20px;
      text-align: center;
    }

    /* ================ MAIN CONTENT STYLES ================ */
    .main-content {
      margin-left: 280px;
      flex: 1;
      background: #f8fafc;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    .content-header {
      background: white;
      padding: 1.5rem 2rem;
      border-bottom: 1px solid #e2e8f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .page-title {
      font-size: 1.875rem;
      font-weight: 700;
      color: #2d3748;
      margin: 0;
    }

    .page-subtitle {
      color: #718096;
      margin-top: 0.25rem;
      font-size: 0.95rem;
    }

    .content-body {
      padding: 2rem;
    }

    /* ================ RESPONSIVE DESIGN ================ */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .sidebar.mobile-open {
        transform: translateX(0);
      }

      .mobile-menu-toggle {
        display: block;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1.2rem;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      }
    }

    .mobile-menu-toggle {
      display: none;
    }

    /* ================ MODERN CARD STYLES ================ */
    .modern-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }

    .card-header-modern {
      padding: 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      background: #f8fafc;
    }

    .card-title-modern {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2d3748;
      margin: 0;
    }

    /* ================ OVERRIDE EXISTING STYLES ================ */
    .layout-wrapper {
      display: none !important;
    }
  </style>

  <!-- Helpers -->
  <script src='{{asset("assets2/vendor/js/helpers.js")}}'></script>
  <script src='{{asset("assets2/js/config.js")}}'></script>
</head>

<body>
  <!-- Mobile Menu Toggle -->
  <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
  </button>

  <div class="admin-layout">
    <!-- Modern Sidebar -->
    <nav class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">Goodone Admin</div>
        <div class="subtitle">Management Panel</div>
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

        <!-- User Management Section -->
        <div class="nav-section">
          <div class="nav-section-title">User Management</div>
          <div class="nav-item">
            <a href="{{ route('admin_get_users') }}" class="nav-link {{ Request::routeIs('admin_get_users') ? 'active' : '' }}">
              <i class="fas fa-users"></i>
              <span>Customers</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="{{ route('admin_get_service_providers') }}" class="nav-link {{ Request::routeIs('admin_get_service_providers') ? 'active' : '' }}">
              <i class="fas fa-user-tie"></i>
              <span>Service Providers</span>
            </a>
          </div>
        </div>

        <!-- Order Management Section -->
        <div class="nav-section">
          <div class="nav-section-title">Order Management</div>
          <div class="nav-item has-submenu {{ Request::routeIs('admin_get_orders') ? 'open' : '' }}">
            <a href="#" class="nav-link">
              <i class="fas fa-shopping-cart"></i>
              <span>All Orders</span>
            </a>
            <div class="nav-submenu">
              <div class="nav-item">
                <a href="{{ route('admin_get_orders') }}" class="nav-link {{ Request::routeIs('admin_get_orders') ? 'active' : '' }}">
                  <i class="fas fa-list"></i>
                  <span>All Orders</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-clock"></i>
                  <span>Pending Orders</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-check-circle"></i>
                  <span>Completed Orders</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-undo"></i>
                  <span>Refund Requests</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Service Management Section -->
        <div class="nav-section">
          <div class="nav-section-title">Service Management</div>
          <div class="nav-item has-submenu {{ Request::routeIs('admin_get_services') ? 'open' : '' }}">
            <a href="#" class="nav-link">
              <i class="fas fa-cogs"></i>
              <span>All Services</span>
            </a>
            <div class="nav-submenu">
              <div class="nav-item">
                <a href="{{ route('admin_get_services') }}" class="nav-link {{ Request::routeIs('admin_get_services') ? 'active' : '' }}">
                  <i class="fas fa-list"></i>
                  <span>All Services</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-flag"></i>
                  <span>Reported Services</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="#" class="nav-link">
                  <i class="fas fa-exclamation-triangle"></i>
                  <span>Flagged Content</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- App Configuration Section -->
        <div class="nav-section">
          <div class="nav-section-title">App Configuration</div>
          <div class="nav-item has-submenu {{ Request::routeIs('admin_create_category', 'admin_create_subcategory', 'admin_create_coupon', 'admin_create_region_tax', 'admin_get_app_settings', 'admin_get_default_images') ? 'open' : '' }}">
            <a href="#" class="nav-link">
              <i class="fas fa-cog"></i>
              <span>Configuration</span>
            </a>
            <div class="nav-submenu">
              <div class="nav-item">
                <a href="{{ route('admin_create_category') }}" class="nav-link {{ Request::routeIs('admin_create_category') ? 'active' : '' }}">
                  <i class="fas fa-folder"></i>
                  <span>Categories</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="{{ route('admin_create_subcategory') }}" class="nav-link {{ Request::routeIs('admin_create_subcategory') ? 'active' : '' }}">
                  <i class="fas fa-folder-open"></i>
                  <span>Subcategories</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="{{ route('admin_create_coupon') }}" class="nav-link {{ Request::routeIs('admin_create_coupon') ? 'active' : '' }}">
                  <i class="fas fa-ticket-alt"></i>
                  <span>Coupons</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="{{ route('admin_create_region_tax') }}" class="nav-link {{ Request::routeIs('admin_create_region_tax') ? 'active' : '' }}">
                  <i class="fas fa-globe"></i>
                  <span>Regional Taxes</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="{{ route('admin_get_app_settings') }}" class="nav-link {{ Request::routeIs('admin_get_app_settings') ? 'active' : '' }}">
                  <i class="fas fa-percentage"></i>
                  <span>Platform Fees</span>
                </a>
              </div>
              <div class="nav-item">
                <a href="{{ route('admin_get_default_images') }}" class="nav-link {{ Request::routeIs('admin_get_default_images') ? 'active' : '' }}">
                  <i class="fas fa-image"></i>
                  <span>Default Images</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Payments & Earnings Section -->
        <div class="nav-section">
          <div class="nav-section-title">Payments & Earnings</div>
          <div class="nav-item">
            <a href="{{ route('admin_withdraw_requests') }}" class="nav-link {{ Request::routeIs('admin_withdraw_requests') ? 'active' : '' }}">
              <i class="fas fa-money-bill-wave"></i>
              <span>Withdrawal Requests</span>
            </a>
          </div>
          <div class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-history"></i>
              <span>Payout History</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Logout Section -->
      <div class="logout-section">
        <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
          @csrf
          <button type="submit" class="logout-btn" style="border: none; width: 100%; cursor: pointer;">
            <i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i>
            Logout
          </button>
        </form>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <div class="content-header">
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        <p class="page-subtitle">@yield('page-subtitle', 'Welcome back! Here\'s what\'s happening with your platform.')</p>
      </div>
      
      <div class="content-body">
        @yield('content')
      </div>
    </main>
  </div>

  <!-- Core JS -->
  <script src='{{asset("assets2/vendor/libs/jquery/jquery.js")}}'></script>
  <script src='{{asset("assets2/vendor/libs/popper/popper.js")}}'></script>
  <script src='{{asset("assets2/vendor/js/bootstrap.js")}}'></script>
  <script src='{{asset("assets2/vendor/libs/perfect-scrollbar/perfect-scrollbar.js")}}'></script>
  <script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script>
  <script src='{{asset("assets2/vendor/js/menu.js")}}'></script>
  <script src='{{asset("assets2/js/main.js")}}'></script>

  <!-- Modern Sidebar JavaScript -->
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
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('mobile-open');
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
      const sidebar = document.getElementById('sidebar');
      const toggle = document.querySelector('.mobile-menu-toggle');
      
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('mobile-open');
      }
    });
  </script>

  @stack('scripts')
</body>
</html>