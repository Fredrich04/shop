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

    <script>
        // App State
        let currentView = 'products';
        let cartItems = [];
        let transactions = [];
        let products = [
            {
                id: '1',
                name: 'iPhone 15 Pro',
                price: 1299,
                image: 'https://images.unsplash.com/photo-1640948612546-3b9e29c23e98?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBzbWFydHBob25lJTIwdGVjaG5vbG9neXxlbnwxfHx8fDE3NTg3NzkyMDR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Le dernier iPhone avec puce A17 Pro et appareil photo révolutionnaire',
                category: 'Smartphone',
                stock: 15
            },
            {
                id: '2',
                name: 'MacBook Air M3',
                price: 1499,
                image: 'https://images.unsplash.com/photo-1643290369779-c6bec760cf18?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsYXB0b3AlMjBjb21wdXRlciUyMGVsZWN0cm9uaWNzfGVufDF8fHx8MTc1ODgxMDYxMnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Ordinateur portable ultra-fin avec puce M3 pour des performances exceptionnelles',
                category: 'Ordinateur',
                stock: 8
            },
            {
                id: '3',
                name: 'AirPods Pro 2',
                price: 299,
                image: 'https://images.unsplash.com/photo-1632200004922-bc18602c79fc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx3aXJlbGVzcyUyMGhlYWRwaG9uZXMlMjBhdWRpb3xlbnwxfHx8fDE3NTg4NTkwODZ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Écouteurs sans fil avec réduction de bruit active de nouvelle génération',
                category: 'Audio',
                stock: 25
            },
            {
                id: '4',
                name: 'Apple Watch Series 9',
                price: 449,
                image: 'https://images.unsplash.com/photo-1665860455418-017fa50d29bc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzbWFydHdhdGNoJTIwZml0bmVzcyUyMHRyYWNrZXJ8ZW58MXx8fHwxNzU4ODM1NTgzfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Montre connectée avec GPS, monitoring santé et écran Always-On',
                category: 'Wearable',
                stock: 12
            },
            {
                id: '5',
                name: 'iPad Pro 12.9"',
                price: 1199,
                image: 'https://images.unsplash.com/photo-1681178519367-32c366c98867?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx0YWJsZXQlMjBkZXZpY2UlMjBkaWdpdGFsfGVufDF8fHx8MTc1ODg1NDY2Mnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Tablette professionnelle avec puce M2 et écran Liquid Retina XDR',
                category: 'Tablette',
                stock: 6
            },
            {
                id: '6',
                name: 'Canon EOS R6 Mark II',
                price: 2399,
                image: 'https://images.unsplash.com/photo-1729857037662-221cc636782a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYW1lcmElMjBwaG90b2dyYXBoeSUyMGVxdWlwbWVudHxlbnwxfHx8fDE3NTg3NzQ0MzB8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                description: 'Appareil photo hybride haute performance pour les professionnels',
                category: 'Photo',
                stock: 3
            }
        ];

        // Initialize App
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            setupEventListeners();
            loadFromStorage();
        });

        function initializeApp() {
            renderProducts();
            updateCartUI();
            updateTransactionsUI();
        }

        function setupEventListeners() {
            // Navigation
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const view = btn.getAttribute('data-view');
                    setCurrentView(view);
                });
            });

            // Cart
            document.getElementById('checkoutBtn').addEventListener('click', handleCheckout);

            // Admin
            setupAdminEventListeners();

            // Modal close
            document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
                btn.addEventListener('click', closeModals);
            });

            // Click outside modal to close
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModals();
                }
            });
        }

        function setupAdminEventListeners() {
            // Admin tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.getAttribute('data-tab');
                    setActiveTab(tab);
                });
            });

            // Add product
            const addProductBtn = document.getElementById('addProductBtn');
            if (addProductBtn) {
                addProductBtn.addEventListener('click', () => {
                    openProductModal();
                });
            }

            // Product form
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.addEventListener('submit', handleProductSubmit);
            }
        }

        // Navigation
        function setCurrentView(view) {
            currentView = view;

            // Update nav buttons
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-view') === view);
            });

            // Update views
            document.querySelectorAll('.view').forEach(viewEl => {
                viewEl.classList.toggle('active', viewEl.id === view + 'View');
            });

            // Special handling for admin view
            if (view === 'admin') {
                renderAdminContent();
            }
        }

        // Products
        function renderProducts() {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';

            products.forEach(product => {
                const productCard = createProductCard(product);
                grid.appendChild(productCard);
            });
        }

        function createProductCard(product) {
            const card = document.createElement('div');
            card.className = 'bg-card border rounded-lg overflow-hidden hover:shadow-lg transition-shadow';

            card.innerHTML = `
                <div class="product-image aspect-square overflow-hidden">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover transition-transform duration-200" loading="lazy">
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold line-clamp-2 flex-1 mr-2">${product.name}</h3>
                        <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded text-xs font-medium whitespace-nowrap">${product.category}</span>
                    </div>
                    <p class="text-muted-foreground text-sm mb-3 line-clamp-2">${product.description}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-bold">${product.price}€</span>
                        <span class="text-sm text-muted-foreground">Stock: ${product.stock}</span>
                    </div>
                    <button class="w-full bg-primary text-primary-foreground px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                            ${product.stock === 0 ? 'disabled' : ''}
                            onclick="addToCart('${product.id}')">
                        ${product.stock === 0 ? 'Rupture de stock' : 'Ajouter au panier'}
                    </button>
                </div>
            `;

            return card;
        }

        // Cart functions
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            const existingItem = cartItems.find(item => item.product.id === productId);

            if (existingItem) {
                if (existingItem.quantity >= product.stock) {
                    showToast('Stock insuffisant', 'error');
                    return;
                }
                existingItem.quantity += 1;
                showToast('Quantité mise à jour dans le panier', 'success');
            } else {
                cartItems.push({ product, quantity: 1 });
                showToast('Produit ajouté au panier', 'success');
            }

            updateCartUI();
            saveToStorage();
        }

        function updateQuantity(productId, quantity) {
            if (quantity <= 0) return;

            const item = cartItems.find(item => item.product.id === productId);
            if (item) {
                item.quantity = quantity;
                updateCartUI();
                saveToStorage();
            }
        }

        function removeFromCart(productId) {
            cartItems = cartItems.filter(item => item.product.id !== productId);
            updateCartUI();
            saveToStorage();
            showToast('Produit retiré du panier', 'success');
        }

        function updateCartUI() {
            const itemCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            const total = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);

            // Update badge
            const cartBadge = document.getElementById('cartBadge');
            if (itemCount > 0) {
                cartBadge.textContent = itemCount;
                cartBadge.classList.remove('hidden');
            } else {
                cartBadge.classList.add('hidden');
            }

            // Update cart count
            document.getElementById('cartCount').textContent = `${cartItems.length} article(s) dans votre panier`;

            // Update cart content
            const emptyCart = document.getElementById('emptyCart');
            const cartItemsContainer = document.getElementById('cartItems');

            if (cartItems.length === 0) {
                emptyCart.classList.remove('hidden');
                cartItemsContainer.classList.add('hidden');
            } else {
                emptyCart.classList.add('hidden');
                cartItemsContainer.classList.remove('hidden');
                renderCartItems();

                // Update totals
                document.getElementById('subtotal').textContent = total.toFixed(2) + '€';
                document.getElementById('total').textContent = total.toFixed(2) + '€';
            }
        }

        function renderCartItems() {
            const container = document.getElementById('cartItemsList');
            container.innerHTML = '';

            cartItems.forEach(item => {
                const cartItem = createCartItem(item);
                container.appendChild(cartItem);
            });
        }

        function createCartItem(item) {
            const div = document.createElement('div');
            div.className = 'bg-card border rounded-lg p-6';

            div.innerHTML = `
                <div class="flex gap-4">
                    <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="${item.product.image}" alt="${item.product.name}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold">${item.product.name}</h3>
                            <button class="text-destructive hover:bg-destructive/10 p-1 rounded" onclick="removeFromCart('${item.product.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded text-xs font-medium inline-block mb-2">${item.product.category}</span>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <button class="border border-border w-8 h-8 rounded flex items-center justify-center hover:bg-muted ${item.quantity <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                        onclick="updateQuantity('${item.product.id}', ${item.quantity - 1})"
                                        ${item.quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                                <span class="font-medium w-8 text-center">${item.quantity}</span>
                                <button class="border border-border w-8 h-8 rounded flex items-center justify-center hover:bg-muted ${item.quantity >= item.product.stock ? 'opacity-50 cursor-not-allowed' : ''}"
                                        onclick="updateQuantity('${item.product.id}', ${item.quantity + 1})"
                                        ${item.quantity >= item.product.stock ? 'disabled' : ''}>
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">${(item.product.price * item.quantity).toFixed(2)}€</div>
                                <div class="text-sm text-muted-foreground">${item.product.price}€ / unité</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return div;
        }

        function handleCheckout() {
            if (cartItems.length === 0) return;

            const total = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
            const newTransaction = {
                id: `TXN-${Date.now()}`,
                date: new Date().toISOString(),
                items: [...cartItems],
                total,
                status: 'completed'
            };

            transactions.unshift(newTransaction);
            cartItems = [];

            updateCartUI();
            updateTransactionsUI();
            saveToStorage();

            setCurrentView('transactions');
            showToast('Commande passée avec succès !', 'success');
        }

        // Transactions
        function updateTransactionsUI() {
            const container = document.getElementById('transactionsList');
            container.innerHTML = '';

            if (transactions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-16">
                        <i class="fas fa-receipt text-6xl text-muted-foreground mb-4"></i>
                        <h3 class="text-2xl font-bold mb-2">Aucune commande</h3>
                        <p class="text-muted-foreground">Vous n'avez pas encore passé de commande</p>
                    </div>
                `;
                return;
            }

            transactions.forEach(transaction => {
                const transactionCard = createTransactionCard(transaction);
                container.appendChild(transactionCard);
            });
        }

        function createTransactionCard(transaction) {
            const div = document.createElement('div');
            div.className = 'bg-card border rounded-lg p-6 mb-4';

            const date = new Date(transaction.date).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const itemsHTML = transaction.items.map(item =>
                `<div class="flex justify-between text-sm">
                    <span>${item.product.name} x${item.quantity}</span>
                    <span>${(item.product.price * item.quantity).toFixed(2)}€</span>
                </div>`
            ).join('');

            div.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold">${transaction.id}</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Terminée</span>
                </div>
                <div class="text-sm text-muted-foreground mb-4">${date}</div>
                <div class="space-y-2 mb-4">
                    ${itemsHTML}
                </div>
                <div class="text-right font-bold text-lg text-primary">Total: ${transaction.total.toFixed(2)}€</div>
            `;

            return div;
        }

        // Admin functions
        function renderAdminContent() {
            renderAdminProducts();
        }

        function setActiveTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                const isActive = btn.getAttribute('data-tab') === tab;
                btn.classList.toggle('border-primary', isActive);
                btn.classList.toggle('text-primary', isActive);
                btn.classList.toggle('border-transparent', !isActive);
                btn.classList.toggle('text-muted-foreground', !isActive);
            });

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                const isActive = content.id === 'admin' + tab.charAt(0).toUpperCase() + tab.slice(1);
                content.classList.toggle('hidden', !isActive);
            });
        }

        function renderAdminProducts() {
            const container = document.getElementById('adminProductsList');
            if (!container) return;

            container.innerHTML = `
                <div class="grid grid-cols-4 gap-4 p-4 bg-muted font-medium border-b">
                    <span>Nom</span>
                    <span>Prix</span>
                    <span>Stock</span>
                    <span>Actions</span>
                </div>
            `;

            products.forEach(product => {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-4 gap-4 p-4 border-b hover:bg-muted/50 items-center';
                row.innerHTML = `
                    <span>${product.name}</span>
                    <span>${product.price}€</span>
                    <span>${product.stock}</span>
                    <div class="flex gap-2">
                        <button class="border border-border text-foreground px-2 py-1 rounded text-sm hover:bg-muted" onclick="editProduct('${product.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="bg-destructive text-destructive-foreground px-2 py-1 rounded text-sm hover:bg-destructive/90" onclick="deleteProduct('${product.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
            });
        }

        // Product Modal functions
        function openProductModal(productId = null) {
            const modal = document.getElementById('productModal');
            const title = document.getElementById('productModalTitle');
            const form = document.getElementById('productForm');

            if (productId) {
                const product = products.find(p => p.id === productId);
                title.textContent = 'Modifier le produit';

                document.getElementById('productName').value = product.name;
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productCategory').value = product.category;
                document.getElementById('productDescription').value = product.description;
                document.getElementById('productImage').value = product.image;
                document.getElementById('productStock').value = product.stock;

                form.setAttribute('data-product-id', productId);
            } else {
                title.textContent = 'Ajouter un produit';
                form.removeAttribute('data-product-id');
                form.reset();
            }

            modal.classList.add('active');
        }

        function editProduct(productId) {
            openProductModal(productId);
        }

        function deleteProduct(productId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                products = products.filter(p => p.id !== productId);
                renderProducts();
                renderAdminProducts();
                showToast('Produit supprimé', 'success');
                saveToStorage();
            }
        }

        function handleProductSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const productId = form.getAttribute('data-product-id');

            const productData = {
                name: document.getElementById('productName').value,
                price: parseFloat(document.getElementById('productPrice').value),
                category: document.getElementById('productCategory').value,
                description: document.getElementById('productDescription').value,
                image: document.getElementById('productImage').value,
                stock: parseInt(document.getElementById('productStock').value)
            };

            if (productId) {
                // Update existing product
                const productIndex = products.findIndex(p => p.id === productId);
                products[productIndex] = { ...products[productIndex], ...productData };
                showToast('Produit modifié', 'success');
            } else {
                // Add new product
                const newProduct = {
                    id: Date.now().toString(),
                    ...productData
                };
                products.push(newProduct);
                showToast('Produit ajouté', 'success');
            }

            renderProducts();
            renderAdminProducts();
            closeModals();
            saveToStorage();
        }

        // Utility functions
        function closeModals() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';

            toast.innerHTML = `
                <i class="${icon} ${iconColor}"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Storage functions
        function saveToStorage() {
            localStorage.setItem('techstore_transactions', JSON.stringify(transactions));
            localStorage.setItem('techstore_products', JSON.stringify(products));
            localStorage.setItem('techstore_cart', JSON.stringify(cartItems));
        }

        function loadFromStorage() {
            const savedTransactions = localStorage.getItem('techstore_transactions');
            const savedProducts = localStorage.getItem('techstore_products');
            const savedCart = localStorage.getItem('techstore_cart');

            if (savedTransactions) {
                transactions = JSON.parse(savedTransactions);
                updateTransactionsUI();
            }

            if (savedProducts) {
                products = JSON.parse(savedProducts);
                renderProducts();
            }

            if (savedCart) {
                cartItems = JSON.parse(savedCart);
                updateCartUI();
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
