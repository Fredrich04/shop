@extends('layouts.admin')

@section('title', 'TechStore')

@section('content')
    <!-- Header -->
    <header class="border-b bg-card sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-store text-2xl text-primary"></i>
                <h1 class="text-2xl font-bold">TechStore</h1>
            </div>

            <nav class="flex items-center gap-4">
                <button class="nav-btn active px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="products">
                    Produits
                </button>

                <button class="nav-btn px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="transactions">
                    Commandes
                </button>

                <button class="nav-btn relative px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="cart">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Panier
                    <span id="cartBadge" class="cart-badge hidden">0</span>
                </button>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <button class="nav-btn px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="admin">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span class="hidden sm:inline">Admin</span>
                        </button>
                    @endif

                    <button class="nav-btn px-4 py-2 rounded-lg transition-colors hover:bg-muted" data-view="profile">
                        <i class="fas fa-user mr-2"></i>
                        <span class="hidden sm:inline">{{ auth()->user()->name ?? 'Profil' }}</span>
                    </button>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main id="mainContent">
        <!-- Products View -->
        <div id="productsView" class="view active">
            <div class="container mx-auto px-4 py-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2">Nos Produits</h2>
                    <p class="text-muted-foreground">Découvrez notre sélection de produits high-tech</p>
                </div>
                <div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
            </div>
        </div>

        <!-- Cart View -->
        <div id="cartView" class="view">
            <div class="container mx-auto px-4 py-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2">Votre Panier</h2>
                    <p id="cartCount" class="text-muted-foreground">0 article(s) dans votre panier</p>
                </div>
                <div id="cartContent">
                    <div id="emptyCart" class="text-center py-16">
                        <i class="fas fa-shopping-bag text-6xl text-muted-foreground mb-4"></i>
                        <h3 class="text-2xl font-bold mb-2">Votre panier est vide</h3>
                        <p class="text-muted-foreground">Ajoutez des produits pour commencer vos achats</p>
                    </div>
                    <div id="cartItems" class="hidden">
                        <div class="grid lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-2">
                                <div id="cartItemsList" class="space-y-4"></div>
                            </div>
                            <div class="lg:col-span-1">
                                <div class="bg-card border rounded-lg p-6 sticky top-24">
                                    <h3 class="text-xl font-bold mb-4">Résumé de la commande</h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between">
                                            <span>Sous-total</span>
                                            <span id="subtotal">0€</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Livraison</span>
                                            <span>Gratuite</span>
                                        </div>
                                        <div class="border-t pt-4">
                                            <div class="flex justify-between font-bold text-lg">
                                                <span>Total</span>
                                                <span id="total">0€</span>
                                            </div>
                                        </div>
                                        <button id="checkoutBtn" class="w-full bg-primary text-primary-foreground px-4 py-3 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                            Passer la commande
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions View -->
        <div id="transactionsView" class="view">
            <div class="container mx-auto px-4 py-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2">Historique des Commandes</h2>
                    <p class="text-muted-foreground">Consultez vos achats précédents</p>
                </div>
                <div id="transactionsList"></div>
            </div>
        </div>

        <!-- Profile View -->
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

        <!-- Admin View -->
        @auth
            @if(auth()->user()->role === 'admin')
                <div id="adminView" class="view">
                    <div class="container mx-auto px-4 py-8">
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold mb-2">Administration</h2>
                            <p class="text-muted-foreground">Gestion des produits et utilisateurs</p>
                        </div>
                        <div class="border-b mb-6">
                            <nav class="flex space-x-8">
                                <button class="tab-btn py-2 px-1 border-b-2 border-primary text-primary font-medium" data-tab="products">
                                    Produits
                                </button>
                                <button class="tab-btn py-2 px-1 border-b-2 border-transparent text-muted-foreground hover:text-foreground" data-tab="categories">
                                    Catégories
                                </button>
                                <button class="tab-btn py-2 px-1 border-b-2 border-transparent text-muted-foreground hover:text-foreground" data-tab="users">
                                    Utilisateurs
                                </button>
                            </nav>
                        </div>
                        <div class="admin-content">
                            <div id="adminProducts" class="tab-content">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold">Gestion des Produits</h3>
                                    <button id="addProductBtn" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ajouter un produit
                                    </button>
                                </div>
                                <div id="adminProductsList" class="bg-card border rounded-lg overflow-hidden"></div>
                            </div>
                            <div id="adminCategories" class="tab-content hidden">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold">Gestion des Catégories</h3>
                                    <button id="addCategoryBtn" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Ajouter une catégorie
                                    </button>
                                </div>
                                <div id="adminCategoriesList" class="bg-card border rounded-lg overflow-hidden"></div>
                            </div>
                            <div id="adminUsers" class="tab-content hidden">
                                <div class="mb-6">
                                    <h3 class="text-xl font-bold">Gestion des Utilisateurs</h3>
                                </div>
                                <div id="adminUsersList" class="bg-card border rounded-lg overflow-hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </main>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="bg-card rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="productModalTitle" class="text-xl font-bold">Ajouter un produit</h3>
                <button class="modal-close text-muted-foreground hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="productForm" class="space-y-4">
                @csrf
                <div>
                    <label for="productName" class="block text-sm font-medium mb-1">Nom du produit</label>
                    <input type="text" id="productName" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                </div>
                <div>
                    <label for="productPrice" class="block text-sm font-medium mb-1">Prix (€)</label>
                    <input type="number" id="productPrice" step="0.01" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                </div>
                <div>
                    <label for="productCategory" class="block text-sm font-medium mb-1">Catégorie</label>
                    <select id="productCategory" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                        <option value="Smartphone">Smartphone</option>
                        <option value="Ordinateur">Ordinateur</option>
                        <option value="Audio">Audio</option>
                        <option value="Wearable">Wearable</option>
                        <option value="Tablette">Tablette</option>
                        <option value="Photo">Photo</option>
                    </select>
                </div>
                <div>
                    <label for="productDescription" class="block text-sm font-medium mb-1">Description</label>
                    <textarea id="productDescription" rows="3" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
                </div>
                <div>
                    <label for="productImage" class="block text-sm font-medium mb-1">URL de l'image</label>
                    <input type="url" id="productImage" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                </div>
                <div>
                    <label for="productStock" class="block text-sm font-medium mb-1">Stock</label>
                    <input type="number" id="productStock" min="0" class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="button" class="modal-cancel flex-1 border border-border text-foreground px-4 py-2 rounded-md hover:bg-muted transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 bg-primary text-primary-foreground px-4 py-2 rounded-md hover:bg-primary/90 transition-colors">
                        Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
