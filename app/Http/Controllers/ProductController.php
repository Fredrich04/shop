<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'stock' => 'required|integer|min:0'
        ]);

        Product::create($request->all());

        return redirect()->back()->with('success', 'Produit ajouté avec succès');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'stock' => 'required|integer|min:0'
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Produit supprimé');
    }
}
