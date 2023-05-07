<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController
{
    public function getProducts()
    {
        return Inertia::render('Data/Products')->with([
            'products' => Product::all()
        ]);
    }

    public function postProduct(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $product = null;

        if ($request->input('id') >= 0) {
            $product = Product::where('id', $request->input('id'))->first();
        }

        if ($product == null) {
            $product = new Product;
        }

        $product->name = $request->input('name');
        $product->size = $request->input('size');
        $product->save();

        return to_route('data.products');
    }

    public function postDelete(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $product = Product::findOrFail($request->input('id'));
        $product->delete();

        return to_route('data.products');
    }
}
