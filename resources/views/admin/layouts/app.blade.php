<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - MyBDSMS')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- jQuery (required for Toastr) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --primary-color: #34BD93;
            --primary-dark: #2a9a7a;
            --primary-light: #43CC84;
            --secondary-color: #FAA03C;
            --accent-color: #FDA537;
            --text-dark: #1a1a2e;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #e9ecef;
            padding: 0 20px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .sidebar-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .sidebar-logo span {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-menu {
            padding: 20px 0;
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
        }

        .menu-section {
            padding: 0 20px;
            margin-bottom: 10px;
        }

        .menu-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            padding: 0 15px;
            margin-bottom: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .menu-item:hover {
            background: var(--bg-light);
            color: var(--primary-color);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
            color: white;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .menu-item .badge {
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .menu-item .unread-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
        }

        .menu-item .unread-badge:empty {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-dark);
            cursor: pointer;
            padding: 5px;
        }

        .page-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .admin-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .admin-role {
            font-size: 12px;
            color: var(--text-light);
        }

        .btn-logout {
            background: none;
            border: 1px solid #e9ecef;
            padding: 8px 16px;
            border-radius: 8px;
            color: var(--text-light);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background: #fee;
            border-color: #fcc;
            color: #c33;
        }

        /* Page Content */
        .page-content {
            padding: 30px;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .page-content {
                padding: 20px;
            }
        }

        @media (max-width: 575px) {
            .top-header {
                padding: 0 15px;
            }

            .admin-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo">
                <span>MyBDSMS</span>
            </a>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.banners.index') }}" class="menu-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <i class="fas fa-image"></i>
                    <span>Banners</span>
                </a>
                <a href="{{ route('admin.about.index') }}" class="menu-item {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i>
                    <span>About Us</span>
                </a>
                <a href="{{ route('admin.stats.index') }}" class="menu-item {{ request()->routeIs('admin.stats.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Statistics</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-sms"></i>
                    <span>Services</span>
                </a>
                <a href="{{ route('admin.features.index') }}" class="menu-item {{ request()->routeIs('admin.features.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Features</span>
                </a>
                <a href="{{ route('admin.pricing.index') }}" class="menu-item {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Pricing</span>
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="menu-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                    <i class="fas fa-quote-left"></i>
                    <span>Testimonials</span>
                </a>
                <a href="{{ route('admin.faqs.index') }}" class="menu-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQs</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">User Management</div>
                <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                    <span class="badge bg-primary ms-auto">{{ \App\Models\User::count() }}</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Billing & Finance</div>
                <a href="{{ route('admin.invoices.index') }}" class="menu-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Invoices & Billing</span>
                    @php $adminUnpaidCount = \App\Models\Invoice::where('status', 'unpaid')->count(); @endphp
                    @if($adminUnpaidCount > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $adminUnpaidCount }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Communication</div>
                <a href="{{ route('admin.messages.index') }}" class="menu-item {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <span class="badge bg-danger ms-auto unread-badge">{{ \App\Models\ContactMessage::unread()->count() }}</span>
                </a>
                <a href="{{ route('admin.verifications.index') }}" class="menu-item {{ request()->routeIs('admin.verifications.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Verifications</span>
                    <span class="badge bg-warning ms-auto">{{ \App\Models\VerificationRequest::where('status', 'pending')->count() }}</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="menu-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    @php $unreadAdminNotifs = \App\Models\AppNotification::where('user_id', Auth::id())->unread()->count(); @endphp
                    @if($unreadAdminNotifs > 0)
                        <span class="badge bg-danger ms-auto" id="sidebarNotifBadge">{{ $unreadAdminNotifs }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Settings</div>
                <a href="{{ route('admin.settings.index') }}" class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Site Settings</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="menu-item" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="header-right d-flex align-items-center gap-3">
                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" type="button" id="notifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border: 1px solid #e9ecef;">
                        <i class="fas fa-bell text-secondary fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size: 10px;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0" aria-labelledby="notifDropdownBtn" style="width: 350px; max-width: 90vw;">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-bell me-2 text-primary"></i>Notifications</h6>
                            <button class="btn btn-sm btn-link text-primary p-0 small text-decoration-none" id="markAllReadBtn" style="font-size: 12px;">Mark all read</button>
                        </div>
                        <div class="p-0 overflow-auto" id="notifListContainer" style="max-height: 350px;">
                            <div class="p-4 text-center text-muted small" id="notifLoading">
                                <i class="fas fa-spinner fa-spin me-1"></i> Loading notifications...
                            </div>
                        </div>
                        <div class="p-2 border-top text-center bg-light rounded-bottom-4">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-link text-dark fw-semibold text-decoration-none" style="font-size: 13px;">
                                View All Notifications <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="admin-info">
                    <div class="admin-avatar">
                        {{ ucfirst(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="admin-name">{{ Auth::user()->name }}</div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Toastr Configuration -->
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    <!-- Sidebar Toggle Script -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = menuToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 992) {
                sidebar.classList.remove('show');
            }
        });

        // Fetch unread message count
        function updateUnreadCount() {
            $.get('{{ route('admin.messages.unread-count') }}', function(response) {
                console.log('Unread count response:', response);
                if (response.success) {
                    $('.unread-badge').text(response.count);
                    if (response.count > 0) {
                        $('.unread-badge').show();
                    } else {
                        $('.unread-badge').hide();
                    }
                }
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch unread count:', error);
            });
        }

        // Update unread count on page load and every 30 seconds
        $(document).ready(function() {
            console.log('Page loaded, fetching unread count...');
            updateUnreadCount();
            setInterval(updateUnreadCount, 30000);

            // Fetch notifications
            function loadNotifications() {
                $.get('{{ route('notifications.dropdown') }}', function(data) {
                    if (data) {
                        const count = data.unread_count || 0;
                        if (count > 0) {
                            $('#notifBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                            $('#sidebarNotifBadge').text(count).removeClass('d-none');
                        } else {
                            $('#notifBadge').addClass('d-none');
                            $('#sidebarNotifBadge').addClass('d-none');
                        }

                        if (data.notifications && data.notifications.length > 0) {
                            let html = '';
                            data.notifications.forEach(function(item) {
                                const readClass = item.is_read ? 'bg-white' : 'bg-light bg-opacity-75';
                                const unreadDot = !item.is_read ? '<span class="badge bg-danger p-1 rounded-circle" style="width: 8px; height: 8px;"></span>' : '';
                                const targetUrl = item.action_url ? item.action_url : 'javascript:void(0)';
                                html += `
                                    <a href="{{ url('admin/notifications') }}/${item.id}/read" class="text-decoration-none d-block p-3 border-bottom ${readClass} hover-bg transition">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="p-2 rounded-circle bg-white border shadow-sm flex-shrink-0" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                                                <i class="${item.icon} small"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="text-dark small text-truncate d-block" style="max-width: 190px;">${item.title}</strong>
                                                    <span class="text-muted" style="font-size: 10px;">${item.time_ago}</span>
                                                </div>
                                                <p class="text-secondary small mb-0 text-truncate" style="font-size: 11px;">${item.message}</p>
                                            </div>
                                            ${unreadDot}
                                        </div>
                                    </a>
                                `;
                            });
                            $('#notifListContainer').html(html);
                        } else {
                            $('#notifListContainer').html('<div class="p-4 text-center text-muted small"><i class="fas fa-bell-slash me-1 opacity-50"></i> No notifications yet</div>');
                        }
                    }
                });
            }

            loadNotifications();
            setInterval(loadNotifications, 20000);

            $('#markAllReadBtn').on('click', function(e) {
                e.preventDefault();
                $.post('{{ route('admin.notifications.mark_all_read') }}', { _token: '{{ csrf_token() }}' }, function(res) {
                    loadNotifications();
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
