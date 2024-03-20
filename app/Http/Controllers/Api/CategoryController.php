<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Http\Resources\MainResource;

class CategoryController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
    
    public function index(){
        $categories = Category::all();
        return new MainResource(true, 'Categories', $categories);
    }

    public function show($id)
    {
        $category = Category::findOrfail($id);
        return new MainResource(true, 'Category By Id', $category);
    }    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::create([
            'name'     => $request->name
        ]);
        return new MainResource(true, 'Category Created', $category);
    }
    
    
    public function update(Request $request, $id)
    {     
        $category = Category::findOrfail($id);        
        
        $validator = Validator::make($request->all(), [
            'name'   => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $category->update([
            'name'     => $request->name
        ]);
        return new MainResource(true, 'Category Updated', $category);
    }
    
    
    public function destroy($id)
    {
        $category = Category::findOrfail($id);        
        $category->delete();
        return new MainResource(true, 'Category Deleted', $category);
    }
}
