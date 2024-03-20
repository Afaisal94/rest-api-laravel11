<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Http\Resources\MainResource;

class ProductController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
    
    public function index(){
        $products = Product::with(['category'])->get();
        return new MainResource(true, 'Product', $products);
    }
    public function show($id)
    {
        $product = Product::findOrfail($id);
        
        return new MainResource(true, 'Product By Id', $product);
    }    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'name'          => 'required',
            'price'         => 'required',
            'category_id'   => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $image = $request->file('image');
        $path = str_replace("public", "storage", $image->storeAs('public/products', $image->hashName()));
        
        $product = Product::create([
            'name'          => $request->name,
            'price'         => $request->price,
            'image'         => $image->hashName(),
            'image_url'     => $path,
            'category_id'   => $request->category_id
        ]);        
        return new MainResource(true, 'Product Created', $product);
    }    
    
    public function update(Request $request, $id)
    {       
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'price'         => 'required',
            'category_id'   => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $path = str_replace("public", "storage", $image->storeAs('public/products', $image->hashName()));

            //delete old image
            Storage::delete('public/products/'.$product->image);

            $product->update([
                'name'          => $request->name,
                'price'         => $request->price,
                'image'         => $image->hashName(),
                'image_url'     => $path,
                'category_id'   => $request->category_id
            ]);

        } else {

            //update product without image
            $product->update([
                'name'          => $request->name,
                'price'         => $request->price,
                'category_id'   => $request->category_id
            ]);
        }
        return new MainResource(true, 'Product Updated', $product);        
    }    
    
    public function destroy($id)
    {
        $product = Product::findOrfail($id);
        $product->delete();
        return new MainResource(true, 'Product Deleted', $product);
    }
}
