<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index(int|string $paginate){

        try {
            return $this->productRepository->index($paginate);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to show products'
            ], 400);
        }
    }

    public function show(string $code){
        
        try {
            $product = $this->productRepository->show($code);

            if(!$product){
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            return $product;

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to show product'
            ], 400);
        }
    }

    public function update(string $code, Request $request){
        
        try {
            $product = $this->productRepository->show($code);

            if(!$product){
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            $this->productRepository->update($product, $request);

            return response()->json([
                'message' => 'Product updated successfully'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to update product'
            ], 400);
        }
    }

    public function delete(string $code){
        
        try {
            $product = $this->productRepository->show($code);

            if(!$product){
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            $this->productRepository->delete($product);

            return response()->json([
                'message' => 'Product removed successfully'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to remove product'
            ], 400);
        }
    }
}
