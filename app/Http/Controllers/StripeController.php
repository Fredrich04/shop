<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;

class StripeController extends Controller
{
    public function createPayment(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Panier vide');
        }

        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart)) * 100; // en cents

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkout_session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => array_values(array_map(function ($item) {
                return [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                        'unit_amount' => $item['price'] * 100,
                    ],
                    'quantity' => $item['quantity'],
                ];
            }, $cart)),
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);

        // Créer une transaction en attente
        Transaction::create([
            'order_id' => null,
            'status' => 'pending',
            'payment_method' => 'stripe',
            'transaction_ref' => $checkout_session->id,
            'amount' => $total / 100, // en euros
        ]);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session_id = $request->get('session_id');
        if (!$session_id) {
            return redirect()->route('shop.index')->with('error', 'Session Stripe manquante.');
        }

        $session = StripeSession::retrieve($session_id);

        if ($session && $session->payment_status === 'paid') {
            $cart = session()->get('cart', []);
            $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'paid',
                'customer_info' => ['session_id' => session()->getId()]
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Update transaction
            Transaction::where('transaction_ref', $session->id)->update([
                'order_id' => $order->id,
                'status' => 'success',
            ]);

            session()->forget('cart');

            return redirect()->route('shop.index')->with('success', 'Paiement réussi et commande validée !');
        }

        return redirect()->route('dashboard')->with('error', 'Paiement non validé.');
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error', 'Paiement annulé.');
    }
}
