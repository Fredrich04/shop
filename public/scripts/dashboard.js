document.addEventListener("DOMContentLoaded", () => {
    // ======================
    // NAVBAR (Produits, Commandes, Panier, Profil, Admin)
    // ======================
    const navButtons = document.querySelectorAll(".nav-btn");
    const views = document.querySelectorAll(".view");

    navButtons.forEach(button => {
        button.addEventListener("click", () => {
            const target = button.getAttribute("data-view");

            // désactiver toutes les vues
            views.forEach(v => v.classList.remove("active"));
            navButtons.forEach(b => b.classList.remove("active"));

            // activer la vue sélectionnée
            button.classList.add("active");
            document.getElementById(target + "View").classList.add("active");
        });
    });

    // ======================
    // ADMIN TABS (Produits, Catégories, Utilisateurs)
    // ======================
    const adminTabButtons = document.querySelectorAll(".tab-btn");
    const adminContents = document.querySelectorAll(".tab-content");

    adminTabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.getAttribute("data-tab");

            // reset
            adminTabButtons.forEach(b => {
                b.classList.remove("border-primary", "text-primary");
                b.classList.add("border-transparent", "text-muted-foreground");
            });
            adminContents.forEach(c => c.classList.add("hidden"));

            // activer le bon
            btn.classList.add("border-primary", "text-primary");
            btn.classList.remove("border-transparent", "text-muted-foreground");
            document.getElementById("admin" + capitalize(target)).classList.remove("hidden");
        });
    });

    // ======================
    // PANIER
    // ======================
    const cartBadge = document.getElementById("cartBadge");
    const cartCount = document.getElementById("cartCount");
    const cartContent = document.getElementById("cartContent");

    async function refreshCart() {
        try {
            const response = await fetch("/api/cart");
            const data = await response.json();

            if (!data.items || data.items.length === 0) {
                cartContent.innerHTML = `
                    <div id="emptyCart" class="text-center py-16">
                        <i class="fas fa-shopping-bag text-6xl text-muted-foreground mb-4"></i>
                        <h3 class="text-2xl font-bold mb-2">Votre panier est vide</h3>
                        <p class="text-muted-foreground">Ajoutez des produits pour commencer vos achats</p>
                    </div>`;
                cartBadge.classList.add("hidden");
                cartCount.textContent = "0 article(s) dans votre panier";
                return;
            }

            let html = "";
            let totalItems = 0;

            data.items.forEach(item => {
                totalItems += item.quantity;
                html += `
                    <div class="order border p-4 rounded mb-4 flex justify-between items-center">
                        <div>
                            <h4>${item.product.name}</h4>
                            <span>${item.quantity} x ${item.product.price}€ = ${(item.quantity * item.product.price).toFixed(2)}€</span>
                        </div>
                        <form method="POST" action="#" onsubmit="event.preventDefault(); removeFromCart(${item.product.id});">
                            <button class="bg-red-500 text-white px-2 py-1 rounded">Supprimer</button>
                        </form>
                    </div>`;
            });

            cartContent.innerHTML = html;
            cartBadge.textContent = totalItems;
            cartBadge.classList.remove("hidden");
            cartCount.textContent = `${totalItems} article(s) dans votre panier`;
        } catch (error) {
            console.error("Erreur refreshCart:", error);
        }
    }

    // Ajouter au panier
    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id = btn.dataset.id;
            try {
                const response = await fetch("/api/cart/add", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: id, quantity: 1 })
                });

                const data = await response.json();
                if (data.success) {
                    showToast("Produit ajouté au panier !");
                    refreshCart();
                } else {
                    showToast("Erreur lors de l'ajout au panier", "error");
                }
            } catch (error) {
                console.error(error);
                showToast("Erreur serveur", "error");
            }
        });
    });

    // Supprimer du panier
    window.removeFromCart = async (id) => {
        try {
            const response = await fetch("/api/cart/remove", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: id })
            });

            const data = await response.json();
            if (data.success) {
                showToast("Produit supprimé du panier !");
                refreshCart();
            } else {
                showToast("Erreur lors de la suppression", "error");
            }
        } catch (error) {
            console.error(error);
            showToast("Erreur serveur", "error");
        }
    };

    // Init panier
    refreshCart();

    // ======================
    // TOAST SYSTEM
    // ======================
    function showToast(message, type = "success") {
        const container = document.getElementById("toastContainer");
        const toast = document.createElement("div");

        toast.className = `toast ${type} bg-${type === "success" ? "green" : "red"}-500 text-white px-4 py-2 rounded mb-2 shadow`;
        toast.textContent = message;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("opacity-0", "transition-opacity");
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // Si message Laravel en flash
    const flashMessage = document.querySelector("meta[name='flash-message']");
    if (flashMessage && flashMessage.content) {
        showToast(flashMessage.content, "success");
    }

    // ======================
    // HELPER
    // ======================
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
