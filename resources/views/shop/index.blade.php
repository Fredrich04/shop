@extends('layouts.app')

@section('title', 'TechStore - Boutique en ligne')

@section('content')
<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                🏪 TechStore
            </div>
            <nav class="nav">
                <button class="nav-btn active" data-view="products">Produits</button>
                <button class="nav-btn" data-view="transactions">Commandes</button>
                <button class="nav-btn" data-view="cart">
                    🛒 Panier
                    <span class="cart-badge" id="cart-badge" style="display: none;">0</span>
                </button>
                <button class="nav-btn px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="profile">
                    <i class="fas fa-user mr-2"></i>
                    <span class="hidden sm:inline">{{ auth()->user()->name ?? 'Profil' }}</span>
                </button>
            </nav>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main">
    <div class="container">
        <!-- Products View -->
        <div class="view active" id="products-view">
            <h2 class="section-title">Nos Produits</h2>
            <p class="section-subtitle">Découvrez notre sélection de produits high-tech</p>
            <div class="products-grid" id="products-grid">
                @foreach($products as $product)
                <div class="product-card">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
                    <div class="product-info">
                        <div class="product-header">
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <span class="product-category">{{ $product->category->name }}</span>
                        </div>
                        <p class="product-description">{{ $product->description }}</p>
                        <div class="product-footer">
                            <span class="product-price">{{ $product->formatted_price }}</span>
                            <span class="product-stock">Stock: {{ $product->stock }}</span>
                        </div>
                        <button class="btn add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                {{ $product->stock === 0 ? 'disabled' : '' }}>
                            {{ $product->stock === 0 ? 'Rupture de stock' : 'Ajouter au panier' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cart View -->
        <div class="view" id="cart-view">
            <h2 class="section-title">Votre Panier</h2>
            <div id="cart-content"></div>
        </div>

        <!-- Transactions View -->
        <div class="view" id="transactions-view">
            <h2 class="section-title">Mes Commandes</h2>
            <p class="section-subtitle">Historique de vos achats et commandes</p>
            <div id="transactions-content"></div>
        </div>

        <div id="profileView" class="view">
            <div class="container mx-auto px-4 py-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2">Mon Profil</h2>
                    <p class="text-muted-foreground">Gérez vos informations personnelles</p>
                </div>
                <div class="max-w-2xl mx-auto">
                    <div class="bg-card border rounded-lg p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-2xl text-muted-foreground"></i>
                            </div>
                            <div>
                                <h3 id="profileName" class="text-xl font-bold">{{ auth()->user()->name ?? 'Utilisateur' }}</h3>
                                <p id="profileEmail" class="text-muted-foreground">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between py-3 border-b">
                                <span class="font-medium text-muted-foreground">Téléphone</span>
                                <span id="profilePhone">{{ auth()->user()->phone ?? '+33 6 12 34 56 78' }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b">
                                <span class="font-medium text-muted-foreground">Adresse</span>
                                <span id="profileAddress">{{ auth()->user()->address ?? '123 Rue de la Paix, 75001 Paris' }}</span>
                            </div>
                            <div class="flex justify-between py-3 border-b">
                                <span class="font-medium text-muted-foreground">Membre depuis</span>
                                <span id="profileMember">{{ auth()->user()->created_at?->format('Y') ?? '2024' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-4 bg-muted rounded-lg">
                                <div id="totalOrders" class="text-2xl font-bold text-primary">0</div>
                                <div class="text-sm text-muted-foreground">Commandes</div>
                            </div>
                            <div class="text-center p-4 bg-muted rounded-lg">
                                <div id="totalSpent" class="text-2xl font-bold text-primary">0€</div>
                                <div class="text-sm text-muted-foreground">Total dépensé</div>
                            </div>
                        </div>
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full border border-border text-foreground px-4 py-2 rounded-lg font-medium hover:bg-muted transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Se déconnecter
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@section('scripts')
<script>
    let currentView = 'products';
    let cart = [];
    let transactions = [];

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', () => switchView(btn.dataset.view));
        });

        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = e.target.dataset.productId;
                addToCart(productId);
            });
        });

        updateCartBadge();
    });

    function switchView(viewName) {
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === viewName) {
                btn.classList.add('active');
            }
        });

        document.querySelectorAll('.view').forEach(view => {
            view.classList.remove('active');
        });
        document.getElementById(`${viewName}-view`).classList.add('active');

        currentView = viewName;

        if (viewName === 'cart') {
            loadCart();
        } else if (viewName === 'transactions') {
            loadTransactions();
        }
    }

    async function addToCart(productId) {
        try {
            const response = await makeRequest('/api/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message);
                updateCartBadge(data.cart_count);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur lors de l\'ajout au panier', 'error');
        }
    }

    async function updateCartBadge(count = null) {
        try {
            if (count === null) {
                const response = await makeRequest('/api/cart');
                const data = await response.json();
                count = data.cart_count;
            }

            const badge = document.getElementById('cart-badge');
            if (count > 0) {
                badge.style.display = 'flex';
                badge.textContent = count;
            } else {
                badge.style.display = 'none';
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour du badge:', error);
        }
    }

    async function loadCart() {
        try {
            const response = await makeRequest('/api/cart');
            const data = await response.json();
            cart = data.cart;
            renderCart(data);
        } catch (error) {
            showToast('Erreur lors du chargement du panier', 'error');
        }
    }

    function renderCart(data) {
        const cartContent = document.getElementById('cart-content');

        if (data.cart.length === 0) {
            cartContent.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <h3>Votre panier est vide</h3>
                    <p>Ajoutez des produits pour commencer vos achats</p>
                </div>
            `;
            return;
        }

        cartContent.innerHTML = `
            <p class="section-subtitle">${data.cart.length} article(s) dans votre panier</p>
            <div class="cart-layout">
                <div class="cart-items">
                    ${data.cart.map(item => `
                        <div class="cart-item">
                            <div class="cart-item-content">
                                <img src="${item.image_url}" alt="${item.name}" class="cart-item-image">
                                <div class="cart-item-info">
                                    <div class="cart-item-header">
                                        <h3 class="cart-item-name">${item.name}</h3>
                                        <button class="remove-btn" onclick="removeFromCart(${item.id})">🗑️</button>
                                    </div>
                                    <span class="product-category">${item.category}</span>
                                    <div class="cart-item-controls">
                                        <div class="quantity-controls">
                                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                            <span class="quantity-display">${item.quantity}</span>
                                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                                        </div>
                                        <div class="cart-item-price">
                                            <div class="cart-item-total">${(item.price * item.quantity).toFixed(2)}€</div>
                                            <div class="cart-item-unit">${item.price}€ / unité</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="cart-summary">
                    <h3>Résumé de la commande</h3>
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span>${data.cart_total.toFixed(2)}€</span>
                    </div>
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span>Gratuite</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>${data.cart_total.toFixed(2)}€</span>
                    </div>
                    <a href="{{ route('stripe.checkout') }}" class="btn" style="text-decoration:none;">Passer la commande</a>
                </div>
            </div>
        `;
    }

    async function updateQuantity(productId, quantity) {
        if (quantity <= 0) return;

        try {
            const response = await makeRequest('/api/cart/update', {
                method: 'PUT',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                updateCartBadge(data.cart_count);
                loadCart();
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la mise à jour', 'error');
        }
    }

    async function removeFromCart(productId) {
        try {
            const response = await makeRequest('/api/cart/remove', {
                method: 'DELETE',
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message);
                updateCartBadge(data.cart_count);
                loadCart();
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la suppression', 'error');
        }
    }

    async function checkout() {
        try {
            const response = await makeRequest('/api/checkout', {
                method: 'POST'
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message);
                updateCartBadge(0);
                switchView('transactions');
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur lors de la commande', 'error');
        }
    }

    async function loadTransactions() {
        try {
            const response = await makeRequest('/api/orders');
            const data = await response.json();

            console.log("Commandes reçues:", data);
            transactions = data;
            renderTransactions(data);
        } catch (error) {
            showToast('Erreur lors du chargement des commandes', 'error');
        }
    }


    function getStatusInfo(status) {
        switch (status) {
            case 'pending':
                return { text: 'En attente', color: 'bg-yellow-100 text-yellow-700' };
            case 'completed':
                return { text: 'Terminée', color: 'bg-green-100 text-green-700' };
            case 'cancelled':
                return { text: 'Annulée', color: 'bg-red-100 text-red-700' };
            default:
                return { text: status, color: 'bg-gray-100 text-gray-700' };
        }
    }

    function renderTransactions(transactions) {
        const transactionsContent = document.getElementById('transactions-content');

        if (!transactions || transactions.length === 0) {
            transactionsContent.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>Aucune commande</h3>
                    <p>Vous n'avez pas encore passé de commande</p>
                </div>
            `;
            return;
        }

        transactionsContent.innerHTML = transactions.map(transaction => {
            const statusInfo = getStatusInfo(transaction.status);

            return `
                <div class="transaction-card">
                    <div class="transaction-header">
                        <div>
                            <div class="transaction-date">📅 ${new Date(transaction.date).toLocaleDateString('fr-FR')}</div>
                        </div>
                        <div style="text-align: right;">
                            <span class="transaction-status px-2 py-1 rounded ${statusInfo.color}">
                                ${statusInfo.text}
                            </span>
                            <div class="transaction-total">${Number(transaction.total).toFixed(2)}€</div>
                        </div>
                    </div>
                    <div class="transaction-items">
                        <h4 style="margin-bottom: 0.5rem;">Articles commandés:</h4>
                        ${transaction.items.map(item => `
                            <div class="transaction-item">
                                <div>
                                    <span style="font-weight: 500;">${item.product_name}</span>
                                    <span style="color: #666; margin-left: 0.5rem;">x${item.quantity}</span>
                                </div>
                                <span style="font-weight: 500;">${Number(item.subtotal).toFixed(2)}€</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

</script>
@endsection
