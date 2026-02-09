<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\ShoppingCart;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PetOwner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function authenticateUser()
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $user = User::where('username', $username)->first();
        if (!$user || $user->role !== 'pet_owner') {
            return redirect('/login')->with('error', 'Access denied');
        }

        return $user;
    }

    /**
     * Display list of available products for customers.
     */
    public function index()
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        view()->share('user', $user);

        $products = InventoryItem::query()
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        // Get user's cart for displaying cart count
        $cart = ShoppingCart::getOrCreateForUser($user->id);
        $cartItemCount = $cart->total_items;

        return view('customer.products.index', compact('products', 'cartItemCount'));
    }

    /**
     * Handle a simple order request from the dashboard or products page.
     * This method now redirects to cart for consistency.
     */
    public function order(Request $request, $productId): RedirectResponse
    {
        // For consistency, redirect to add to cart functionality
        return $this->addToCart($request, $productId);
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request, $productId): RedirectResponse
    {
        $user = $this->authenticateUser();
        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = InventoryItem::findOrFail($productId);

        if ($product->requires_prescription) {
            return back()->with('error', 'This product requires a prescription. Please consult your veterinarian.');
        }

        if ($product->quantity <= 0) {
            return back()->with('error', 'This product is currently out of stock.');
        }

        $cart = ShoppingCart::getOrCreateForUser($user->id);

        // Check if item already exists in cart
        $existingItem = $cart->cartItems()
            ->where('inventory_item_id', $product->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $validated['quantity'];
            
            // Only check if product is completely out of stock, not quantity limits
            if ($product->quantity <= 0) {
                return back()->with('error', 'This product is currently out of stock.');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->unit_price,
            ]);

            return back()->with('success', 'Item quantity updated in cart!');
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $product->name . '.' . $image->getClientOriginalExtension();
            
            // Validate image
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png,gif|max:2048'
            ]);
            
            // Move image to public directory
            $image->move(public_path('images/products'), $imageName);
            
            // Update product with image path
            $product->update(['image_path' => 'images/products/' . $imageName]);
        }

        // Add new item to cart
        CartItem::create([
            'cart_id' => $cart->id,
            'inventory_item_id' => $product->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->unit_price,
            'total' => $product->unit_price * $validated['quantity'],
        ]);

        return back()->with('success', 'Item added to cart!');
    }
}

