<?php

namespace App\Http\Controllers;

use App\Models\Products;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api',['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Products::all();
        return response()->json([
            'status' => 'success',
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validators = Validator::make($request->all(),[
            'product_name' => 'required|string|max:255|unique:products',
            'product_desc' => 'required|string',
            'product_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'invalid data',
                'errors' => $validators->errors()
            ]);
        }

        $validated = $validators->validated();

        Products::create([
            'product_name' => $request['product_name'],
            'product_desc' => $request['product_desc'],
            'product_amount' => $request['product_amount']
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'product added successfully',
            'product' => $validated
        ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Products::find($id);

        if($product == ''){
            return response()->json([
                'status' => 'failed',
                'message' => 'Product does not exist'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Products::find($id);
        if($product){
            $validator = Validator::make($request->all(),[
                'product_name' => 'required|string|max:255|unique:products',
                'product_desc' => 'required|string',
                'product_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'invalid data',
                    'errors' => $validator->errors()
                ]);
            }

            $product['product_name'] = $request['product_name'];
            $product['product_desc'] = $request['product_desc'];
            $product['product_amount'] = $request['product_amount'];

            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product data updated',
                'product' => $validator->validated()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Products $products)
    {
        //
    }
}
