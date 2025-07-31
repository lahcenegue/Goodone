<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Panel - Preview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
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
        }

        .nav-item.has-submenu.open > .nav-link::after {
            transform: rotate(90deg);
        }

        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .nav-item.has-submenu.open .nav-submenu {
            max-height: 500px;
        }

        /* Logout button */
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
        }

        /* Main content area */
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
        }

        .content-body {
            padding: 2rem;
        }

        /* Responsive */
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

        /* Preview message styles */
        .preview-notice {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <!-- Preview Notice -->
    <div class="preview-notice">
        🎨 STEP 1 PREVIEW: Modern Admin Sidebar Design - This is how your new admin panel will look!
    </div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">Goodone Admin</div>
                <div class="subtitle">Management Panel</div>
            </div>

            <div class="nav-menu">
                <!-- Dashboard Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Analytics</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Platform Statistics</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Revenue Reports</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-pie"></i>
                            <span>Service Performance</span>
                        </a>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">User Management</div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                            <span class="nav-badge">24</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <span>Service Providers</span>
                        </a>
                    </div>
                </div>

                <!-- Order Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Order Management</div>
                    <div class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>All Orders</span>
                        </a>
                        <div class="nav-submenu">
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-clock"></i>
                                    <span>Pending Orders</span>
                                    <span class="nav-badge">12</span>
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
                                    <span class="nav-badge">3</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Service Management</div>
                    <div class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-cogs"></i>
                            <span>All Services</span>
                        </a>
                        <div class="nav-submenu">
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-flag"></i>
                                    <span>Reported Services</span>
                                    <span class="nav-badge">5</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Flagged Content</span>
                                    <span class="nav-badge">2</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- App Configuration Section -->
                <div class="nav-section">
                    <div class="nav-section-title">App Configuration</div>
                    <div class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Configuration</span>
                        </a>
                        <div class="nav-submenu">
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-folder"></i>
                                    <span>Categories</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Subcategories</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>Coupons</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-globe"></i>
                                    <span>Regional Taxes</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-percentage"></i>
                                    <span>Platform Fees</span>
                                </a>
                            </div>
                            <div class="nav-item">
                                <a href="#" class="nav-link">
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
                        <a href="#" class="nav-link">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Send Earnings to Providers</span>
                            <span class="nav-badge">8</span>
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
                <a href="#" class="logout-btn">
                    <i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i>
                    Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title">Dashboard Preview</h1>
                <p class="page-subtitle">Step 1: Modern sidebar design preview - This is how your new admin panel will look!</p>
            </div>
            
            <div class="content-body">
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h2 style="color: #2d3748; margin-bottom: 1rem;">✅ Step 1 Complete: Modern Sidebar Preview</h2>
                    <p style="color: #718096; line-height: 1.6;">
                        This is a preview of your new modern admin sidebar. You can see all the navigation sections organized properly with:
                    </p>
                    <ul style="color: #718096; line-height: 1.8; margin-top: 1rem;">
                        <li>🎨 Modern gradient design with professional styling</li>
                        <li>📱 Responsive design that works on mobile devices</li>
                        <li>🔄 Smooth animations and hover effects</li>
                        <li>📊 Organized sections: Dashboard, Analytics, User Management, etc.</li>
                        <li>🔔 Notification badges for pending items</li>
                        <li>📂 Collapsible submenus for better organization</li>
                    </ul>
                    <div style="background: #e6fffa; border: 1px solid #38b2ac; border-radius: 8px; padding: 1rem; margin-top: 1.5rem;">
                        <p style="color: #234e52; margin: 0; font-weight: 600;">
                            🎯 Next: Once you confirm this design looks good, we'll integrate it into your Laravel admin panel in Step 2.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Handle submenu toggles
        document.addEventListener('DOMContentLoaded', function() {
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

            // Handle active states
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.parentElement.classList.contains('has-submenu')) {
                        // Remove active class from all links
                        navLinks.forEach(l => l.classList.remove('active'));
                        // Add active class to clicked link
                        this.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>