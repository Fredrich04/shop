<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;

class PayPalController extends Controller
{
    public function createPayment(Request $request)
    {
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Panier vide');
        }

        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));

        $response = $paypal->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "EUR",
                    "value" => $total
                ]
            ]],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success'),
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            Transaction::create([
                'order_id' => null, // pas encore de commande
                'status' => 'pending',
                'payment_method' => 'paypal',
                'transaction_ref' => $response['id'], // l'ID de l'ordre PayPal
                'amount' => $total,
            ]);

            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('shop.index')->with('error', 'Impossible de démarrer le paiement PayPal.');
    }


    public function success(Request $request)
    {
        $paypal = new PayPalClient;
        $paypal->setApiCredentials(config('paypal'));
        $token = $paypal->getAccessToken();
        $paypal->setAccessToken($token);

        $response = $paypal->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
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
                    'price' => $item['price']
                ]);

                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $transaction = \App\Models\Transaction::where('transaction_ref', $response['id'])->first();
            if ($transaction) {
                $transaction->update([
                    'order_id' => $order->id,
                    'status' => 'success',
                ]);
            }

            session()->forget('cart');

            return redirect()->route('shop.index')->with('success', 'Paiement réussi et commande validée !');
        }

        $transaction = \App\Models\Transaction::where('transaction_ref', $request['token'])->update([
            'status' => 'cancel'
        ]);

        return redirect()->route('shop.index')->with('error', 'Erreur lors du paiement.');
    }

    public function cancel()
    {
        return redirect()->route('shop.index')->with('error', 'Paiement annulé.');
    }
}
