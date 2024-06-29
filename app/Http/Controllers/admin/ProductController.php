<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create(){
        $data=[];
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
         
        $data['categories']=$categories;
        $data['brands']=$brands;
        return view('admin.product.create',$data);
    }




    public function store(Request $request){
        \Log::info('Request data: ', $request->all());
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'required|exists:categories,id',
            'sub_category' => 'nullable|exists:sub__categories,id',
            'brand' => 'nullable|exists:brands,id',
            'is_featured' => 'required|in:Yes,No',
            'sku' => 'required|string|max:255|unique:products',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'required|in:Yes,No',
            'status' => 'required|integer|in:0,1',
        ];
        if(!empty($request->track_qty &&$request->track_qty=='Yes')){
             $rules['qty']= 'required|integer|min:0';
        }
        $validator = Validator::make($request->all(),$rules);

        if( $validator->passes()){

            $product=new Product();
            $product->title= $request->title;
            $product->slug= $request->slug;
            $product->description= $request->description;
            $product->price= $request->price;
            $product->compare_price= $request->compare_price;
            $product->barcode= $request->barcode;
            $product->is_featured= $request->is_featured;
            $product->sku= $request->sku;
            $product->category_id= $request->category;
            $product->brand_id= $request->brand;
            $product->sub__category_id= $request->sub_category;
            $product->track_qty= $request->track_qty;
            $product->status= $request->status;
            $product->save();

        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=> $validator->errors(),
                'request(is_featured)'=>$request->is_featured
            ]);
        }
    }
}