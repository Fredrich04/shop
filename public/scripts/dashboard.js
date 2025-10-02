// public/js/dashboard.js
document.addEventListener('DOMContentLoaded', () => {
    // ============ Utils ============
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastContainer = document.getElementById('toastContainer');

    function showToast(message, type = 'success') {
        const el = document.createElement('div');
        el.className = `toast ${type}`;
        el.style.padding = '0.75rem 1rem';
        el.style.borderRadius = '8px';
        el.style.boxShadow = '0 8px 20px rgba(0,0,0,0.08)';
        el.style.background = type === 'success' ? '#ecfdf5' : '#fff1f2';
        el.style.borderLeft = type === 'success' ? '4px solid #10B981' : '4px solid #ef4444';
        el.innerText = message;
        toastContainer.appendChild(el);
        setTimeout(() => el.remove(), 3000);
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
    async function refreshCart() {
        try {
            const url = (window.routes && window.routes.apiCartGet) ? window.routes.apiCartGet : '/api/cart';
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Erreur récupération panier');
            const data = await res.json();

            // data.cart expected structure from your controller
            const items = data.cart || data.items || [];
            const count = data.cart_count ?? items.reduce((s,i)=>s+i.quantity,0);

            const badge = document.getElementById('cartBadge');
            const cartCountEl = document.getElementById('cartCount');

            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
            if (cartCountEl) cartCountEl.textContent = `${count} article(s) dans votre panier`;
        } catch (err) {
            console.error(err);
        }
    }

    // Attach add-to-cart buttons
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = btn.dataset.id;
            try {
                const res = await fetch((window.routes && window.routes.apiCartAdd) ? window.routes.apiCartAdd : '/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ product_id: id, quantity: 1 })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    showToast(data.message || 'Ajouté au panier');
                    await refreshCart();
                } else {
                    showToast(data.message || 'Erreur', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Erreur serveur', 'error');
            }
        });
    });

    // initial cart load
    refreshCart();

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
