<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('pages.product', compact('product'));
    }
}