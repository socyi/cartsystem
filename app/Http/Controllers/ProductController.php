<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {

        $products = Product::with('media')->paginate();
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $storeProductRequest
     * @return ProductResource
     */
    public function store(StoreProductRequest $storeProductRequest)
    {

      if ($storeProductRequest->url != null) {
          $url = $storeProductRequest->url;

          return ProductResource::make(Product::create($storeProductRequest->validated())
          )->addMedia($url)->toMediaCollection();
      }
      else{
          return ProductResource::make(Product::create($storeProductRequest->validated()));
      }

    }

    /**
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        return ProductResource::make($product);
    }

    /**
     * @param UpdateProductRequest $updateProductRequest
     * @param Product $product
     * @return ProductResource
     */
    public function update(UpdateProductRequest $updateProductRequest, Product $product)
    {
      $product->update($updateProductRequest->validated());

      return ProductResource::make($product);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }


    public function uploadImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the validation rules as needed
        ]);

        if ($request->hasFile('image')) {
            $product = new Product(); // Replace with your product model class

            $image = $request->file('image');

            // Store the uploaded image using Spatie Media Library
            $media = $product->addMedia($image)->toMediaCollection('images');

            // Get the URL of the uploaded image
            $imageUrl = $media->getUrl();

            return response()->json(['url' => $imageUrl], 201);
        }

        return response()->json(['error' => 'Image upload failed'], 500);
    }


}
