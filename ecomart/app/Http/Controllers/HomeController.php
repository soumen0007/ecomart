<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $query = Product::with('category');

        if (request()->has('category') && request('category') != '') {
            $query->whereHas('category', function ($q) {
                $q->where('slug', request('category'));
            });
        }

        $products = $query->latest()->take(8)->get();

        return view('pages.home', compact('categories', 'products'));
    }
}