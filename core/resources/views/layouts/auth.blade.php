<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --auth-primary: #90AB8B;
            --auth-secondary: #EBF4DD;
            --auth-accent: #5A7863;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background-color: var(--auth-secondary);
            color: #243024;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 1rem;
        }

        .auth-card {
            border: none;
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 18px 45px rgba(90, 120, 99, 0.25);
            backdrop-filter: blur(8px);
        }

        .auth-card .card-body {
            padding: 2.5rem;
        }

        .auth-title {
            font-weight: 600;
            color: var(--auth-accent);
            letter-spacing: 0.03em;
        }

        .auth-description {
            font-size: 0.9rem;
            color: rgba(36, 48, 36, 0.7);
        }

        .form-label {
            font-weight: 500;
            color: var(--auth-accent);
        }

        .form-control {
            border-radius: 0.85rem;
            border: 1px solid rgba(90, 120, 99, 0.25);
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 0.2rem rgba(144, 171, 139, 0.2);
        }

        .btn-auth-primary {
            background: var(--auth-primary);
            border-color: var(--auth-primary);
            border-radius: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-auth-primary:hover,
        .btn-auth-primary:focus {
            background: var(--auth-accent);
            border-color: var(--auth-accent);
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(90, 120, 99, 0.25);
        }

        .auth-link {
            color: var(--auth-accent);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .auth-link:hover {
            color: var(--auth-primary);
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .auth-card .card-body {
                padding: 2rem 1.5rem;
            }

            body::before {
                width: 260px;
                height: 260px;
                top: -140px;
                left: -120px;
            }

            body::after {
                width: 240px;
                height: 240px;
                bottom: -120px;
                right: -100px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('layouts.partials.alerts')
</body>
</html>
