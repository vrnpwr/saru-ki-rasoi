<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Fetch full item details for the modal
        $itemIds = array_column($cart, 'id');
        $items = Item::with('variations.options')->whereIn('id', $itemIds)->get()->keyBy('id');

        return view('cart.index', compact('cart', 'total', 'items'));
    }

    public function add(Request $request, Item $item)
    {
        // Load relationships if not already loaded
        $item->load('variations.options');

        $selectedOptions = $request->input('options', []);
        $cartOptions = [];
        $additionalPrice = 0;

        // Validate and Process Options
        foreach ($item->variations as $variation) {
            if ($variation->required && !isset($selectedOptions[$variation->id])) {
                return redirect()->back()->with('error', 'Please select required options for ' . $item->name);
            }

            if (isset($selectedOptions[$variation->id])) {
                $chosen = $selectedOptions[$variation->id];
                // Simplify: Handle single ID (radio/select) or array of IDs (checkbox)
                $chosenIds = is_array($chosen) ? $chosen : [$chosen];

                foreach ($chosenIds as $optId) {
                    $optionObj = $variation->options->where('id', $optId)->first();
                    if ($optionObj) {
                        $cartOptions[] = [
                            'variation_name' => $variation->name, // store name at time of add
                            'option_name' => $optionObj->name,
                            'price' => $optionObj->price,
                        ];
                        $additionalPrice += $optionObj->price;
                    }
                }
            }
        }

        // Generate Unique Cart ID based on Item ID and Options
        if (empty($cartOptions)) {
            $cartId = $item->id;
        } else {
            // Serialize options locally to create a signature
            // Sorting options to ensure order doesn't matter for signature
            usort($cartOptions, function ($a, $b) {
                return strcmp($a['option_name'], $b['option_name']);
            });
            $signature = md5(json_encode($cartOptions));
            $cartId = $item->id . '-' . $signature;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$cartId])) {
            $cart[$cartId]['quantity']++;
        } else {
            $cart[$cartId] = [
                'id' => $item->id, // Original Item ID
                'cart_id' => $cartId, // Unique Key
                'name' => $item->name,
                'price' => $item->price + $additionalPrice, // Unit price including options
                'base_price' => $item->price,
                'quantity' => 1,
                'image_path' => $item->image_path,
                'options' => $cartOptions
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function decrease(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        // $id here maps to the key in the cart array (which is now cartId)
        if (isset($cart[$id])) {
            $cart[$id]['quantity']--;

            if ($cart[$id]['quantity'] <= 0) {
                unset($cart[$id]);
                $message = 'Item removed from cart!';
            } else {
                $message = 'Item quantity updated!';
            }

            session()->put('cart', $cart);
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Item not found in cart!');
    }

    public function increase(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Item quantity updated!');
        }

        return redirect()->back()->with('error', 'Item not found in cart!');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed successfully!');
    }
}
