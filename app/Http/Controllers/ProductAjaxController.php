<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductAjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
          
            $products = Product::orderBy('id', 'asc')->latest()->get();
            return response()->json($products);
        }

        return view('productAjax');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'id' => 'nullable|exists:products,id',
            'title' => 'required|string',
            'description' => 'required|string',
        ],
        [
            'title.required' => 'Title field is required.',
            'description.required' => 'Description field is required.'
            
        ]);

        
        Product::updateOrCreate(
            ['id' => $validatedData['id']],
            [
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
            ]
        );

        return response()->json(['success' => 'Details Saved Successfully']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $products = Product::find($id);
        return response()->json($products);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Product::find($id)->delete();
        return response()->json(['success' => 'Product Deleted Successfully']);
    }
}
