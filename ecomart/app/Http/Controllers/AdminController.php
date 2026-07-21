<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        return Session::has('admin') ? null : redirect()->route('admin.login');
    }

    public function login()
    {
        return Session::has('admin')
            ? redirect()->route('admin.dashboard')
            : view('admin.login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password_hash)) {
            Session::put('admin', $admin);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

    public function dashboard()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        return view('admin.dashboard', [
            'products' => Product::count(),
            'categories' => Category::count(),
            'users' => User::count(),
            'messages' => ContactMessage::count(),
            'lowStock' => Product::where('stock', '<=', 10)->count(),
            'categoryStats' => Category::withCount('products')->orderByDesc('products_count')->get(),
            'recentProducts' => Product::with('category')->latest()->take(6)->get(),
        ]);
    }

    public function products()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $products = Product::with('category')->orderBy('name')->get();

        return view('admin.products', compact('products'));
    }

    public function createProduct()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $categories = Category::orderBy('name')->get();

        return view('admin.create-product', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('assets/images/products'), $imageName);

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => $imageName,
            'is_organic' => $request->has('is_organic') ? 1 : 0
        ]);

        return redirect()->route('admin.products')->with('success', 'Product added successfully.');
    }

    public function editProduct($id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('admin.edit-product', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = $product->image;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('assets/images/products'), $imageName);
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => $imageName,
            'is_organic' => $request->has('is_organic') ? 1 : 0
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function categories()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories', compact('categories'));
    }

    public function users()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.users', compact('users'));
    }

    public function messages()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $messages = ContactMessage::orderBy('created_at', 'desc')->get();

        return view('admin.messages', compact('messages'));
    }

    public function logout()
    {
        Session::forget('admin');

        return redirect()->route('admin.login');
    }
}