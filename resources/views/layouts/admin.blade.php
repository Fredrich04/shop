<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles for the dashboard */
        .view {
            display: none;
        }
        .view.active {
            display: block;
        }

        .nav-btn.active {
            background-color: var(--primary);
            color: var(--primary-foreground);
        }

        .product-image img:hover {
            transform: scale(1.05);
        }

        .cart-badge {
            position: absolute;
            top: -0.5rem;
            right: -0.5rem;
            background-color: var(--destructive);
            color: var(--destructive-foreground);
            border-radius: 50%;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .hidden {
            display: none !important;
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toast {
            background-color: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            min-width: 20rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.3s ease-out;
        }

        .toast.success {
            border-left: 4px solid #10b981;
        }

        .toast.error {
            border-left: 4px solid var(--destructive);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Modal styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .modal.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    @yield('content')

    <script src="{{ asset('scripts/dashboard.js') }}"></script>
    @yield('scripts')
</body>
</html>
