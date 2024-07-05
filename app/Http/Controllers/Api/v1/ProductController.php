<?php

namespace App\Http\Controllers\Api\v1;

//import model Product
use App\Models\Product;

use App\Http\Controllers\Controller;

//import resource ProductResource
use App\Http\Resources\ProductResource;

//import Http request
use Illuminate\Http\Request;

//import facade Validator
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all Products
        $Products = Product::latest()->paginate(5);

        //return collection of Products as a resource
        return new ProductResource(true, 'List Data Products', $Products);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'         => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create Product
        $Product = Product::create([
                'image'         => $image->hashName(),
                'title'         => $request->title,
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock
        ]);

        //return response
        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $Product);
    }

     public function show($id)
    {
        //find Product by ID
        $Product = Product::find($id);

        //return single Product as a resource
        return new ProductResource(true, 'Detail Data Product!', $Product);
    }
}