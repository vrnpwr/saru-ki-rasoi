<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return redirect()->route('home')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (count($cart) == 0) {
            return redirect()->route('home')->with('error', 'Your cart is empty!');
        }

        $rules = [
            'terms' => 'accepted',
        ];

        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_phone'] = 'required|string|max:20';
        }

        $request->validate($rules);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $orderData = [
            'total_amount' => $total,
            'status' => 'pending',
        ];

        if (Auth::check()) {
            $orderData['user_id'] = Auth::id();
        } else {
            $orderData['guest_name'] = $request->guest_name;
            $orderData['guest_email'] = $request->guest_email;
            $orderData['guest_phone'] = $request->guest_phone;
        }

        $order = Order::create($orderData);

        foreach ($cart as $key => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'options' => $item['options'] ?? null,
            ]);
        }

        session()->forget('cart');

        return redirect()->route('home')->with('success', 'Order placed successfully! Order ID: #' . $order->id);
    }
}
