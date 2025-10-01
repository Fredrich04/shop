// App State
let currentUser = null;
let isLoggedIn = false;
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

// DOM Elements
const loginModal = document.getElementById('loginModal');
const mainApp = document.getElementById('mainApp');
const loginForm = document.getElementById('loginForm');
const demoLoginBtn = document.getElementById('demoLogin');
const logoutBtn = document.getElementById('logoutBtn');
const navBtns = document.querySelectorAll('.nav-btn');
const views = document.querySelectorAll('.view');
const cartBadge = document.getElementById('cartBadge');
const userGreeting = document.getElementById('userGreeting');
const adminBtn = document.getElementById('adminBtn');

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
    // Login
    loginForm.addEventListener('submit', handleLogin);
    demoLoginBtn.addEventListener('click', handleDemoLogin);
    logoutBtn.addEventListener('click', handleLogout);
    
    // Navigation
    navBtns.forEach(btn => {
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
    document.getElementById('addProductBtn').addEventListener('click', () => {
        openProductModal();
    });
    
    // Product form
    document.getElementById('productForm').addEventListener('submit', handleProductSubmit);
}

// Authentication
function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (email && password) {
        login(email, password);
    }
}

function handleDemoLogin() {
    login('demo@techstore.com', 'demo123');
}

function login(email, password) {
    const isAdmin = email === 'admin@techstore.com' || email.includes('admin');
    
    const user = {
        id: isAdmin ? 'admin-1' : 'user-1',
        name: isAdmin ? 'Admin User' : 'Jean Dupont',
        email: email,
        phone: '+33 6 12 34 56 78',
        address: isAdmin ? 'Tech Store HQ, Paris' : '123 Rue de la Paix, 75001 Paris',
        memberSince: '2023',
        totalOrders: transactions.length,
        totalSpent: transactions.reduce((sum, t) => sum + t.total, 0),
        role: isAdmin ? 'admin' : 'user'
    };
    
    currentUser = user;
    isLoggedIn = true;
    
    // Update UI
    loginModal.classList.remove('active');
    mainApp.classList.remove('hidden');
    updateUserUI();
    saveToStorage();
    
    showToast(`Bienvenue, ${user.name} ! ${isAdmin ? '(Mode Administrateur)' : ''}`, 'success');
}

function handleLogout() {
    currentUser = null;
    isLoggedIn = false;
    cartItems = [];
    transactions = [];
    currentView = 'products';
    
    mainApp.classList.add('hidden');
    loginModal.classList.add('active');
    
    clearStorage();
    updateCartUI();
    
    showToast('Déconnexion réussie', 'success');
}

function updateUserUI() {
    if (currentUser) {
        userGreeting.textContent = `Bonjour, ${currentUser.name.split(' ')[0]}`;
        
        // Show admin button if user is admin
        if (currentUser.role === 'admin') {
            adminBtn.classList.remove('hidden');
        } else {
            adminBtn.classList.add('hidden');
        }
        
        // Update profile
        updateProfileUI();
    }
}

function updateProfileUI() {
    if (currentUser) {
        document.getElementById('profileName').textContent = currentUser.name;
        document.getElementById('profileEmail').textContent = currentUser.email;
        document.getElementById('profilePhone').textContent = currentUser.phone;
        document.getElementById('profileAddress').textContent = currentUser.address;
        document.getElementById('profileMember').textContent = currentUser.memberSince;
        document.getElementById('totalOrders').textContent = transactions.length;
        document.getElementById('totalSpent').textContent = transactions.reduce((sum, t) => sum + t.total, 0).toFixed(2) + '€';
    }
}

// Navigation
function setCurrentView(view) {
    currentView = view;
    
    // Update nav buttons
    navBtns.forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-view') === view);
    });
    
    // Update views
    views.forEach(viewEl => {
        viewEl.classList.toggle('active', viewEl.id === view + 'View');
    });
    
    // Special handling for admin view
    if (view === 'admin' && currentUser?.role === 'admin') {
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
    card.className = 'product-card';
    
    card.innerHTML = `
        <div class="product-image">
            <img src="${product.image}" alt="${product.name}" loading="lazy">
        </div>
        <div class="product-content">
            <div class="product-header">
                <h3 class="product-title">${product.name}</h3>
                <span class="product-category">${product.category}</span>
            </div>
            <p class="product-description">${product.description}</p>
            <div class="product-footer">
                <span class="product-price">${product.price}€</span>
                <span class="product-stock">Stock: ${product.stock}</span>
            </div>
        </div>
        <div class="product-actions">
            <button class="btn btn-primary add-to-cart-btn" 
                    ${product.stock === 0 ? 'disabled' : ''} 
                    onclick="addToCart('${product.id}')">
                ${product.stock === 0 ? 'Rupture de stock' : 'Ajouter au panier'}
            </button>
        </div>
    `;
    
    return card;
}

// Cart
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
    const container = document.querySelector('.cart-items-list');
    container.innerHTML = '';
    
    cartItems.forEach(item => {
        const cartItem = createCartItem(item);
        container.appendChild(cartItem);
    });
}

function createCartItem(item) {
    const div = document.createElement('div');
    div.className = 'cart-item';
    
    div.innerHTML = `
        <div class="cart-item-content">
            <div class="cart-item-image">
                <img src="${item.product.image}" alt="${item.product.name}">
            </div>
            <div class="cart-item-info">
                <div class="cart-item-header">
                    <h3 class="cart-item-title">${item.product.name}</h3>
                    <button class="cart-item-remove" onclick="removeFromCart('${item.product.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <span class="cart-item-category">${item.product.category}</span>
                <div class="cart-item-footer">
                    <div class="quantity-controls">
                        <button class="quantity-btn" 
                                onclick="updateQuantity('${item.product.id}', ${item.quantity - 1})"
                                ${item.quantity <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantity-display">${item.quantity}</span>
                        <button class="quantity-btn" 
                                onclick="updateQuantity('${item.product.id}', ${item.quantity + 1})"
                                ${item.quantity >= item.product.stock ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-price">
                        <div class="cart-item-total">${(item.product.price * item.quantity).toFixed(2)}€</div>
                        <div class="cart-item-unit">${item.product.price}€ / unité</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function handleCheckout() {
    if (cartItems.length === 0) return;
    
    if (!isLoggedIn) {
        showToast('Veuillez vous connecter pour passer commande', 'error');
        return;
    }
    
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
    updateProfileUI();
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
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h3>Aucune commande</h3>
                <p>Vous n'avez pas encore passé de commande</p>
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
    div.className = 'transaction-card';
    
    const date = new Date(transaction.date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const itemsHTML = transaction.items.map(item => 
        `<div class="transaction-item">
            <span>${item.product.name} x${item.quantity}</span>
            <span>${(item.product.price * item.quantity).toFixed(2)}€</span>
        </div>`
    ).join('');
    
    div.innerHTML = `
        <div class="transaction-header">
            <span class="transaction-id">${transaction.id}</span>
            <span class="transaction-status">Terminée</span>
        </div>
        <div class="transaction-date">${date}</div>
        <div class="transaction-items">
            ${itemsHTML}
        </div>
        <div class="transaction-total">Total: ${transaction.total.toFixed(2)}€</div>
    `;
    
    return div;
}

// Admin
function renderAdminContent() {
    renderAdminProducts();
    renderAdminCategories();
    renderAdminUsers();
}

function setActiveTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
    });
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.toggle('active', content.id === 'admin' + tab.charAt(0).toUpperCase() + tab.slice(1));
    });
}

function renderAdminProducts() {
    const container = document.getElementById('adminProductsList');
    container.innerHTML = `
        <div class="admin-table-header">
            <span>Nom</span>
            <span>Prix</span>
            <span>Stock</span>
            <span>Actions</span>
        </div>
    `;
    
    products.forEach(product => {
        const row = document.createElement('div');
        row.className = 'admin-table-row';
        row.innerHTML = `
            <span>${product.name}</span>
            <span>${product.price}€</span>
            <span>${product.stock}</span>
            <div class="admin-actions">
                <button class="btn btn-outline" onclick="editProduct('${product.id}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-destructive" onclick="deleteProduct('${product.id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });
}

function renderAdminCategories() {
    const categories = [...new Set(products.map(p => p.category))];
    const container = document.getElementById('adminCategoriesList');
    container.innerHTML = `
        <div class="admin-table-header">
            <span>Nom</span>
            <span>Produits</span>
            <span>Créée le</span>
            <span>Actions</span>
        </div>
    `;
    
    categories.forEach(category => {
        const productCount = products.filter(p => p.category === category).length;
        const row = document.createElement('div');
        row.className = 'admin-table-row';
        row.innerHTML = `
            <span>${category}</span>
            <span>${productCount} produits</span>
            <span>2024</span>
            <div class="admin-actions">
                <button class="btn btn-outline">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-destructive">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });
}

function renderAdminUsers() {
    const users = [
        { id: '1', name: 'Jean Dupont', email: 'jean@example.com', role: 'user', orders: 5 },
        { id: '2', name: 'Marie Martin', email: 'marie@example.com', role: 'user', orders: 2 },
        { id: '3', name: 'Admin User', email: 'admin@techstore.com', role: 'admin', orders: 0 }
    ];
    
    const container = document.getElementById('adminUsersList');
    container.innerHTML = `
        <div class="admin-table-header">
            <span>Nom</span>
            <span>Email</span>
            <span>Rôle</span>
            <span>Actions</span>
        </div>
    `;
    
    users.forEach(user => {
        const row = document.createElement('div');
        row.className = 'admin-table-row';
        row.innerHTML = `
            <span>${user.name}</span>
            <span>${user.email}</span>
            <span>${user.role}</span>
            <div class="admin-actions">
                <button class="btn btn-outline">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-destructive">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });
}

// Product Modal
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

// Utilities
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
    
    toast.innerHTML = `
        <i class="${icon} toast-icon"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Storage
function saveToStorage() {
    if (currentUser) {
        localStorage.setItem('techstore_user', JSON.stringify(currentUser));
    }
    localStorage.setItem('techstore_transactions', JSON.stringify(transactions));
    localStorage.setItem('techstore_products', JSON.stringify(products));
}

function loadFromStorage() {
    const savedUser = localStorage.getItem('techstore_user');
    const savedTransactions = localStorage.getItem('techstore_transactions');
    const savedProducts = localStorage.getItem('techstore_products');
    
    if (savedUser) {
        currentUser = JSON.parse(savedUser);
        isLoggedIn = true;
        loginModal.classList.remove('active');
        mainApp.classList.remove('hidden');
        updateUserUI();
    }
    
    if (savedTransactions) {
        transactions = JSON.parse(savedTransactions);
        updateTransactionsUI();
    }
    
    if (savedProducts) {
        products = JSON.parse(savedProducts);
        renderProducts();
    }
}

function clearStorage() {
    localStorage.removeItem('techstore_user');
    localStorage.removeItem('techstore_transactions');
}