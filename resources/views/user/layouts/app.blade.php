<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User Portal - MyBDSMS')</title>

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

    <!-- jQuery -->
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
            margin-bottom: 15px;
        }

        .menu-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            padding: 0 15px;
            margin-bottom: 8px;
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
            box-shadow: 0 4px 12px rgba(52, 189, 147, 0.25);
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
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

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .user-role {
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

            .user-name, .user-role {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('user.dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo">
                <span>MyBDSMS</span>
            </a>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="{{ route('user.dashboard') }}" class="menu-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">SMS & Packages</div>
                <a href="{{ route('user.packages') }}" class="menu-item {{ request()->routeIs('user.packages') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Packages</span>
                </a>
                <a href="{{ route('user.buy_package') }}" class="menu-item {{ request()->routeIs('user.buy_package') ? 'active' : '' }}">
                    <i class="fas fa-cart-plus"></i>
                    <span>Buy Package</span>
                </a>
                <a href="{{ route('user.orders') }}" class="menu-item {{ request()->routeIs('user.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Orders</span>
                </a>
                <a href="{{ route('user.invoices.index') }}" class="menu-item {{ request()->routeIs('user.invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Invoices</span>
                    @php
                        $userUnpaidCount = \App\Models\Invoice::where('user_id', Auth::id())->where('status', 'unpaid')->count();
                    @endphp
                    @if($userUnpaidCount > 0)
                        <span class="badge bg-warning ms-auto">{{ $userUnpaidCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.payment_history') }}" class="menu-item {{ request()->routeIs('user.payment_history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Payment History</span>
                </a>
                <a href="{{ route('user.notifications.index') }}" class="menu-item {{ request()->routeIs('user.notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                    @php $userUnreadNotifs = \App\Models\AppNotification::where('user_id', Auth::id())->unread()->count(); @endphp
                    @if($userUnreadNotifs > 0)
                        <span class="badge bg-danger ms-auto" id="userSidebarNotifBadge">{{ $userUnreadNotifs }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Settings</div>
                <a href="{{ route('user.account') }}" class="menu-item {{ request()->routeIs('user.account') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>My Account</span>
                </a>
                <a href="{{ route('user.profile') }}" class="menu-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i>
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('user.logout') }}" class="d-inline">
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

            <div class="header-right d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1 d-none d-sm-inline-flex" target="_blank">
                    <i class="fas fa-globe me-1"></i> Visit Website
                </a>

                <!-- User Notification Bell Dropdown -->
                <div class="dropdown me-2">
                    <button class="btn btn-light rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" type="button" id="userNotifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border: 1px solid #e9ecef;">
                        <i class="fas fa-bell text-secondary fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="userNotifBadge" style="font-size: 10px;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0" aria-labelledby="userNotifDropdownBtn" style="width: 350px; max-width: 90vw;">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-bell me-2 text-primary"></i>Notifications</h6>
                            <button class="btn btn-sm btn-link text-primary p-0 small text-decoration-none" id="userMarkAllReadBtn" style="font-size: 12px;">Mark all read</button>
                        </div>
                        <div class="p-0 overflow-auto" id="userNotifListContainer" style="max-height: 350px;">
                            <div class="p-4 text-center text-muted small" id="userNotifLoading">
                                <i class="fas fa-spinner fa-spin me-1"></i> Loading notifications...
                            </div>
                        </div>
                        <div class="p-2 border-top text-center bg-light rounded-bottom-4">
                            <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-link text-dark fw-semibold text-decoration-none" style="font-size: 13px;">
                                View All Notifications <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role text-capitalize">{{ Auth::user()->role }}</div>
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

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = menuToggle.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 992) {
                sidebar.classList.remove('show');
            }
        });

        // User Notifications Loader
        $(document).ready(function() {
            function loadUserNotifications() {
                $.get('{{ route('notifications.dropdown') }}', function(data) {
                    if (data) {
                        const count = data.unread_count || 0;
                        if (count > 0) {
                            $('#userNotifBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                            $('#userSidebarNotifBadge').text(count).removeClass('d-none');
                        } else {
                            $('#userNotifBadge').addClass('d-none');
                            $('#userSidebarNotifBadge').addClass('d-none');
                        }

                        if (data.notifications && data.notifications.length > 0) {
                            let html = '';
                            data.notifications.forEach(function(item) {
                                const readClass = item.is_read ? 'bg-white' : 'bg-light bg-opacity-75';
                                const unreadDot = !item.is_read ? '<span class="badge bg-danger p-1 rounded-circle" style="width: 8px; height: 8px;"></span>' : '';
                                const targetUrl = item.action_url ? item.action_url : 'javascript:void(0)';
                                html += `
                                    <a href="{{ url('user/notifications') }}/${item.id}/read" class="text-decoration-none d-block p-3 border-bottom ${readClass} hover-bg transition">
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
                            $('#userNotifListContainer').html(html);
                        } else {
                            $('#userNotifListContainer').html('<div class="p-4 text-center text-muted small"><i class="fas fa-bell-slash me-1 opacity-50"></i> No notifications yet</div>');
                        }
                    }
                });
            }

            loadUserNotifications();
            setInterval(loadUserNotifications, 20000);

            $('#userMarkAllReadBtn').on('click', function(e) {
                e.preventDefault();
                $.post('{{ route('user.notifications.mark_all_read') }}', { _token: '{{ csrf_token() }}' }, function(res) {
                    loadUserNotifications();
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

