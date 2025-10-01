// Variables globales
let currentView = 'products';
let products = [];
let cartItems = [];
let transactions = [];

// Initialisation
document.addEventListener('DOMContentLoaded', async function() {
    await initializeApp();
    setupEventListeners();
});

async function initializeApp() {
    await loadProductsFromDB();
    await loadCartFromDB();
}

// Configuration CSRF pour les requêtes API Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function makeRequest(url, options = {}) {
    const defaults = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    };
    return fetch(url, { ...defaults, ...options });
}


// Event listeners
function setupEventListeners() {
    // Navigation
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const view = btn.getAttribute('data-view');
            setCurrentView(view);
        });
    });

    // Cart
    document.getElementById('checkoutBtn').addEventListener('click', handleCheckoutDB);

    // Admin
    setupAdminEventListeners();

    // Modal close
    document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
        btn.addEventListener('click', closeModals);
    });

    // Click outside modal
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) closeModals();
    });
}

// Admin events
function setupAdminEventListeners() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.getAttribute('data-tab');
            setActiveTab(tab);
        });
    });

    const addProductBtn = document.getElementById('addProductBtn');
    if (addProductBtn) addProductBtn.addEventListener('click', () => openProductModal());

    const productForm = document.getElementById('productForm');
    if (productForm) productForm.addEventListener('submit', handleProductSubmitDB);
}

// Navigation
function setCurrentView(view) {
    currentView = view;

    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-view') === view);
    });

    document.querySelectorAll('.view').forEach(viewEl => {
        viewEl.classList.toggle('active', viewEl.id === view + 'View');
    });

    if (view === 'admin') renderAdminContent();
}

// ----------------- PRODUITS -----------------
async function loadProductsFromDB() {
    try {
        const res = await fetch('/api/products');
        products = await res.json();
        renderProducts();
    } catch (err) {
        console.error('Erreur chargement produits :', err);
        showToast('Impossible de charger les produits', 'error');
    }
}

function renderProducts() {
    const grid = document.getElementById('productGrid');
    if (!grid) return;
    grid.innerHTML = '';
    products.forEach(product => {
        grid.appendChild(createProductCard(product));
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
                    onclick="addToCartDB('${product.id}')">
                ${product.stock === 0 ? 'Rupture de stock' : 'Ajouter au panier'}
            </button>
        </div>
    `;
    return card;
}

// ----------------- PANIER -----------------
async function addToCartDB(productId, quantity = 1) {
    try {
        await fetch('/api/cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productId, quantity })
        });
        cartItems = await (await fetch('/api/cart')).json();
        updateCartUI();
        showToast('Produit ajouté au panier', 'success');
    } catch (err) {
        console.error(err);
        showToast('Erreur ajout au panier', 'error');
    }
}

async function updateQuantityDB(productId, quantity) {
    try {
        await fetch(`/api/cart/${productId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity })
        });
        cartItems = await (await fetch('/api/cart')).json();
        updateCartUI();
    } catch (err) {
        console.error(err);
        showToast('Erreur mise à jour quantité', 'error');
    }
}

async function removeFromCartDB(productId) {
    try {
        await fetch(`/api/cart/${productId}`, { method: 'DELETE' });
        cartItems = await (await fetch('/api/cart')).json();
        updateCartUI();
        showToast('Produit retiré du panier', 'success');
    } catch (err) {
        console.error(err);
        showToast('Erreur suppression du panier', 'error');
    }
}

function updateCartUI() {
    const itemCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
    const total = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);

    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        if (itemCount > 0) {
            cartBadge.textContent = itemCount;
            cartBadge.classList.remove('hidden');
        } else cartBadge.classList.add('hidden');
    }

    const cartCount = document.getElementById('cartCount');
    if (cartCount) cartCount.textContent = `${cartItems.length} article(s) dans votre panier`;

    const emptyCart = document.getElementById('emptyCart');
    const cartItemsContainer = document.getElementById('cartItems');
    if (!emptyCart || !cartItemsContainer) return;

    if (cartItems.length === 0) {
        emptyCart.classList.remove('hidden');
        cartItemsContainer.classList.add('hidden');
    } else {
        emptyCart.classList.add('hidden');
        cartItemsContainer.classList.remove('hidden');
        renderCartItems();
        document.getElementById('subtotal').textContent = total.toFixed(2) + '€';
        document.getElementById('total').textContent = total.toFixed(2) + '€';
    }
}

function renderCartItems() {
    const container = document.getElementById('cartItemsList');
    if (!container) return;
    container.innerHTML = '';
    cartItems.forEach(item => container.appendChild(createCartItem(item)));
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
                    <button class="text-destructive hover:bg-destructive/10 p-1 rounded" onclick="removeFromCartDB('${item.product.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded text-xs font-medium inline-block mb-2">${item.product.category}</span>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <button class="border border-border w-8 h-8 rounded flex items-center justify-center hover:bg-muted ${item.quantity <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                onclick="updateQuantityDB('${item.product.id}', ${item.quantity - 1})"
                                ${item.quantity <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus text-sm"></i>
                        </button>
                        <span class="font-medium w-8 text-center">${item.quantity}</span>
                        <button class="border border-border w-8 h-8 rounded flex items-center justify-center hover:bg-muted ${item.quantity >= item.product.stock ? 'opacity-50 cursor-not-allowed' : ''}"
                                onclick="updateQuantityDB('${item.product.id}', ${item.quantity + 1})"
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

// ----------------- TRANSACTIONS -----------------
async function loadTransactionsFromDB() {
    try {
        const res = await fetch('/api/transactions');
        transactions = await res.json();
        updateTransactionsUI();
    } catch (err) {
        console.error(err);
        showToast('Impossible de charger les transactions', 'error');
    }
}

async function handleCheckoutDB() {
    if (cartItems.length === 0) return;
    try {
        const res = await fetch('/api/transactions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: cartItems })
        });
        const newTransaction = await res.json();
        transactions.unshift(newTransaction);
        cartItems = [];
        updateCartUI();
        updateTransactionsUI();
        setCurrentView('transactions');
        showToast('Commande passée avec succès !', 'success');
    } catch (err) {
        console.error(err);
        showToast('Erreur lors du paiement', 'error');
    }
}

function updateTransactionsUI() {
    const container = document.getElementById('transactionsList');
    if (!container) return;
    container.innerHTML = '';
    if (transactions.length === 0) {
        container.innerHTML = `<div class="text-center py-16"><i class="fas fa-receipt text-6xl text-muted-foreground mb-4"></i><h3 class="text-2xl font-bold mb-2">Aucune commande</h3><p class="text-muted-foreground">Vous n'avez pas encore passé de commande</p></div>`;
        return;
    }
    transactions.forEach(tx => container.appendChild(createTransactionCard(tx)));
}

function createTransactionCard(transaction) {
    const div = document.createElement('div');
    div.className = 'bg-card border rounded-lg p-6 mb-4';
    const date = new Date(transaction.date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    const itemsHTML = transaction.items.map(item => `<div class="flex justify-between text-sm"><span>${item.product.name} x${item.quantity}</span><span>${(item.product.price * item.quantity).toFixed(2)}€</span></div>`).join('');
    div.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <span class="font-semibold">${transaction.id}</span>
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Terminée</span>
        </div>
        <div class="text-sm text-muted-foreground mb-4">${date}</div>
        <div class="space-y-2 mb-4">${itemsHTML}</div>
        <div class="text-right font-bold text-lg text-primary">Total: ${transaction.total.toFixed(2)}€</div>
    `;
    return div;
}

// ----------------- ADMIN -----------------
function renderAdminContent() {
    renderAdminProducts();
}

function setActiveTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        const isActive = btn.getAttribute('data-tab') === tab;
        btn.classList.toggle('border-primary', isActive);
        btn.classList.toggle('text-primary', isActive);
        btn.classList.toggle('border-transparent', !isActive);
        btn.classList.toggle('text-muted-foreground', !isActive);
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        const isActive = content.id === 'admin' + tab.charAt(0).toUpperCase() + tab.slice(1);
        content.classList.toggle('hidden', !isActive);
    });
}

function renderAdminProducts() {
    const container = document.getElementById('adminProductsList');
    if (!container) return;
    container.innerHTML = `<div class="grid grid-cols-4 gap-4 p-4 bg-muted font-medium border-b"><span>Nom</span><span>Prix</span><span>Stock</span><span>Actions</span></div>`;
    products.forEach(product => {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-4 gap-4 p-4 border-b hover:bg-muted/50 items-center';
        row.innerHTML = `
            <span>${product.name}</span>
            <span>${product.price}€</span>
            <span>${product.stock}</span>
            <div class="flex gap-2">
                <button class="border border-border text-foreground px-2 py-1 rounded text-sm hover:bg-muted" onclick="openProductModal('${product.id}')"><i class="fas fa-edit"></i></button>
                <button class="bg-destructive text-destructive-foreground px-2 py-1 rounded text-sm hover:bg-destructive/90" onclick="deleteProductFromDB('${product.id}')"><i class="fas fa-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    });
}

// ----------------- MODAL PRODUIT -----------------
function openProductModal(productId = null) {
    const modal = document.getElementById('productModal');
    const title = document.getElementById('productModalTitle');
    const form = document.getElementById('productForm');

    if (productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;
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

async function loadCartFromDB() {
    try {
        const res = await makeRequest('/api/cart', { method: 'GET' });
        const data = await res.json();

        // Adapter selon la structure renvoyée par ton API
        cartItems = data.items || data || [];

        updateCartUI();
    } catch (err) {
        console.error('Erreur chargement panier :', err);
    }
}



async function handleProductSubmitDB(e) {
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

    try {
        if (productId) {
            await fetch(`/api/products/${productId}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(productData) });
            const index = products.findIndex(p => p.id === productId);
            products[index] = { ...products[index], ...productData };
            showToast('Produit modifié', 'success');
        } else {
            const res = await fetch('/api/products', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(productData) });
            const newProduct = await res.json();
            products.push(newProduct);
            showToast('Produit ajouté', 'success');
        }
        renderProducts();
        renderAdminProducts();
        closeModals();
    } catch (err) {
        console.error(err);
        showToast('Erreur gestion produit', 'error');
    }
}

// ----------------- UTILITAIRES -----------------
function closeModals() {
    document.querySelectorAll('.modal').forEach(modal => modal.classList.remove('active'));
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';
    toast.innerHTML = `<i class="${icon} ${iconColor}"></i><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
