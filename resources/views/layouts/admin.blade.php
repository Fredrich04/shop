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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .nav {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s;
            position: relative;
        }

        .nav-btn:hover {
            background: #f0f0f0;
        }

        .nav-btn.active {
            background: #333;
            color: white;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main {
            padding: 2rem 0;
        }

        .view {
            display: none;
        }

        .view.active {
            display: block;
        }

        /* Products Grid */
        .section-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .section-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 1rem;
        }

        .product-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .product-category {
            background: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .product-stock {
            font-size: 0.9rem;
            color: #666;
        }

        .btn {
            background: #333;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
            width: 100%;
        }

        .btn:hover {
            background: #555;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Cart */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .cart-item-content {
            display: flex;
            gap: 1rem;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .cart-item-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .cart-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:disabled {
            background: #f8f9fa;
            cursor: not-allowed;
        }

        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }

        .cart-item-price {
            text-align: right;
        }

        .cart-item-total {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .cart-item-unit {
            font-size: 0.9rem;
            color: #666;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
        }

        .remove-btn:hover {
            background: #f8d7da;
        }

        .cart-summary {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-total {
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Transactions */
        .transaction-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .transaction-id {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .transaction-date {
            color: #666;
            font-size: 0.9rem;
        }

        .transaction-status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .transaction-total {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .transaction-items {
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.error {
            background: #dc3545;
        }

        .toast.success {
            background: #28a745;
        }

        /* Loading */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .nav {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    @yield('content')

    <script>
        window.routes = {
            adminProductsStore: "{{ route('admin.products.store') }}",
            adminCategoriesStore: "{{ route('admin.categories.store') }}",
            apiCartGet: "{{ route('api.cart.get') }}",
            apiCartAdd: "{{ route('api.cart.add') }}"
        };
    </script>
    <script src="{{ asset('scripts/dashboard.js') }}" defer></script>
    @yield('scripts')
</body>
</html>
