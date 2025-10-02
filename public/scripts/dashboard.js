// public/js/dashboard.js
document.addEventListener('DOMContentLoaded', () => {
    // ============ Utils ============
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastContainer = document.getElementById('toastContainer');

    function makeRequest(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        };
        return fetch(url, { ...defaults, ...options });
    }

    function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

    // ============ NAVIGATION VUES ============
    const navButtons = document.querySelectorAll('.nav-btn');
    const views = document.querySelectorAll('.view');

    navButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetName = btn.getAttribute('data-view') + 'View';
            // toggle active
            navButtons.forEach(b => b.classList.remove('active'));
            views.forEach(v => v.classList.remove('active'));
            btn.classList.add('active');
            const target = document.getElementById(targetName);
            if (target) target.classList.add('active');
            if (targetName === 'cartView') {
                console.log("Chargement du panier...");
                loadCart();
            }

            // on view admin => nothing de spécial (les contenus admins sont rendus par blade)
        });
    });

    // ============ ADMIN TABS ============
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.getAttribute('data-tab'); // ex: 'products' => adminProducts id
            // reset classes
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-primary', 'text-primary');
                b.classList.add('border-transparent');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

            btn.classList.add('border-primary', 'text-primary');
            btn.classList.remove('border-transparent');
            const content = document.getElementById('admin' + tab.charAt(0).toUpperCase() + tab.slice(1));
            if (content) content.classList.remove('hidden');
        });
    });

    // ============ PANIER : refresh & actions ============

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const productId = e.target.dataset.productId;
            addToCart(productId);
        });
    });

    updateCartBadge();

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

    async function loadCart() {
        try {
            const response = await makeRequest('/api/cart');
            const data = await response.json();

            console.log("Réponse API /cart :", data); // debug

            // compatibilité avec plusieurs formats
            const items = data.cart || data.items || [];
            const total = data.cart_total ?? data.total ?? 0;

            renderCart({ cart: items, cart_total: total });
        } catch (error) {
            showToast("Erreur lors du chargement du panier", "error");
            console.error(error);
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

    function renderCart(data) {

        const cartContent = document.getElementById('cart-content');

        if (!data.cart || data.cart.length === 0) {
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
                                    <span class="product-category">${item.category || ''}</span>
                                    <div class="cart-item-controls">
                                        <div class="quantity-controls">
                                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                            <span class="quantity-display">${item.quantity}</span>
                                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
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
                    <a href="{{ route('paypal.payment') }}" class="btn">Passer la commande</a>
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

    // ============ MODAL CREATE PRODUCT (AJAX) ============
    const productModal = document.getElementById('productModal');
    const addProductBtn = document.getElementById('addProductBtn');
    const productForm = document.getElementById('productForm');

    if (addProductBtn) {
        addProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // open modal
            if (productModal) productModal.classList.add('active');
            // reset form
            if (productForm) productForm.reset();
        });
    }

    // close modal buttons
    document.querySelectorAll('.modal-close, .modal-cancel').forEach(b => {
        b.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
        });
    });

    if (productForm) {
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            // collect inputs
            const payload = {
                name: document.getElementById('productName').value,
                price: document.getElementById('productPrice').value,
                stock: document.getElementById('productStock').value,
                category_id: document.getElementById('productCategory').value,
                description: document.getElementById('productDescription').value,
                image_url: document.getElementById('productImage').value
            };

            const storeUrl = (window.routes && window.routes.adminProductsStore) ? window.routes.adminProductsStore : '/admin/products';

            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });

                const data = await res.json();
                if (res.ok) {
                    showToast(data.message || 'Produit créé', 'success');
                    // reload pour rafraîchir la liste serveur-side
                    setTimeout(() => { window.location.reload(); }, 800);
                } else {
                    // validation errors
                    const msg = data.message || 'Erreur création produit';
                    showToast(Array.isArray(data.errors) ? Object.values(data.errors).flat().join(', ') : msg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Erreur serveur', 'error');
            }
        });
    }

    // ============ Ajout rapide catégorie (prompt -> POST) ============
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const name = prompt('Nom de la catégorie :');
            if (!name) return;

            const url = (window.routes && window.routes.adminCategoriesStore) ? window.routes.adminCategoriesStore : '/admin/categories';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ name })
                });
                const data = await res.json();
                if (res.ok) {
                    showToast(data.message || 'Catégorie ajoutée');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Erreur', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Erreur serveur', 'error');
            }
        });
    }

    // ============ Confirm deletes (progressive enhancement) ============
    document.querySelectorAll('form[method="POST"]').forEach(f => {
        // for forms that delete, ask confirmation
        const method = f.querySelector('input[name="_method"]')?.value ?? '';
        if (method.toUpperCase() === 'DELETE') {
            f.addEventListener('submit', (e) => {
                if (!confirm('Confirmer la suppression ?')) e.preventDefault();
            });
        }
    });

}); // DOMContentLoaded
