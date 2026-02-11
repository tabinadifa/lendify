<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lendify - Sistem Peminjaman Alat')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/uploads/icon/Lendify.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-green: #90AB8B;
            --light-green: #E8F5E9;
            --dark-gray: #5A7863;
        }

        body {
            background-color: #F5F7FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            transition: margin-left 0.3s ease;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
            z-index: 998;
        }

        body.sidebar-open .sidebar-backdrop {
            opacity: 1;
            visibility: visible;
        }

        body.sidebar-open .sidebar {
            transform: translateX(0);
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        .metric-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            height: 100%;
        }

        .metric-card.green {
            background: linear-gradient(135deg, #2D6F4E 0%, #1E4D35 100%);
            color: white;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .metric-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .chart-bar {
            background-color: var(--primary-green);
            border-radius: 0.25rem;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-green) 41%, #E5E7EB 0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-circle::before {
            content: '';
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .progress-text {
            position: relative;
            z-index: 1;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-gray);
        }

        @media (max-width: 991.98px) {
            .content-wrapper {
                padding: 1.25rem;
            }

            .main-content {
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="app-layout">
        <!-- Sidebar Component -->
        <x-sidebar :role="session('user_role', 'admin')" />

        <!-- Main Content -->
        <div class="main-content">
            <x-header />
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <div class="sidebar-backdrop d-lg-none" onclick="toggleSidebar(false)"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar(forceState = null) {
            const body = document.body;
            const isOpen = body.classList.contains('sidebar-open');
            const shouldOpen = forceState ?? !isOpen;
            body.classList.toggle('sidebar-open', shouldOpen);
        }
        window.toggleSidebar = toggleSidebar;

        function confirmLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2D6F4E',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        document.addEventListener('submit', function(e) {
            const form = e.target;

            if (form.classList.contains('form-hapus')) {
                e.preventDefault();

                const title = form.dataset.title ?? 'Yakin ingin menghapus?';
                const text = form.dataset.text ?? 'Data ini akan dihapus secara permanen.';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                document.body.classList.remove('sidebar-open');
            }
        });
    </script>

    @include('layouts.partials.alerts')

    @stack('scripts')
</body>

</html>
