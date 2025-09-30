<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->active()
            ->orderBy('name')
            ->get();

        return view('shop.index', compact('products'));
    }

    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant'
                ], 422);
            }

            $cart[$product->id]['quantity'] = $newQuantity;
            $message = 'Quantité mise à jour dans le panier';
        } else {
            if ($quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant'
                ], 422);
            }

            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'stock' => $product->stock
            ];
            $message = 'Produit ajouté au panier';
        }

        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => $cartCount
        ]);
    }

    public function getCart(): JsonResponse
    {
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return response()->json([
            'cart' => array_values($cart),
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    public function updateCartItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = $request->quantity;

        if (!isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé dans le panier'
            ], 404);
        }

        $product = Product::findOrFail($productId);

        if ($quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant'
            ], 422);
        }

        $cart[$productId]['quantity'] = $quantity;
        session()->put('cart', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier',
            'cart_count' => $cartCount
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Le panier est vide'
            ], 422);
        }

        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        // Créer la commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'completed',
            'customer_info' => [
                'session_id' => session()->getId()
            ]
        ]);


        // Créer les éléments de commande
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            // Décrémenter le stock
            $product = Product::find($item['id']);
            if (!$product) {
                throw new \Exception("Produit introuvable (ID {$item['id']})");
            }

            if ($item['quantity'] > $product->stock) {
                throw new \Exception("Stock insuffisant pour {$product->name}");
            }
            if ($product) {
                $product->decrement('stock', $item['quantity']);
            }
        }

        // Vider le panier
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Commande passée avec succès !',
            'order_id' => $order->id
        ]);
    }

    public function orders(): JsonResponse
    {
        $userId = auth()->id();

        $orders = Order::with(['orderItems.product.category'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->id, // ou $order->order_number si tu as une colonne
                    'date' => $order->created_at->format('Y-m-d H:i:s'),
                    'total' => $order->total,
                    'status' => $order->status,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'subtotal' => $item->price * $item->quantity,
                            'image_url' => $item->product->image_url,
                            'category' => $item->product->category->name
                        ];
                    })
                ];
            });

        return response()->json($orders);
    }
}
