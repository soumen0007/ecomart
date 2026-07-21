<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    public function index()
    {
        $items = ShoppingList::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('pages.shopping-list', compact('items'));
    }

    public function add($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please login before adding products.']);
        }

        $product = Product::findOrFail($productId);

        $item = ShoppingList::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->quantity += 1;
            $item->save();
        } else {
            ShoppingList::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => 1,
                'note' => null,
                'added_at' => now(),
            ]);
        }

        return redirect()->route('shopping')
            ->with('success', 'Product added to shopping list.');
    }

    public function remove($id)
    {
        ShoppingList::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Item removed.');
    }
}