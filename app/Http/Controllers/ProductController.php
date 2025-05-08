<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{      
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request){
        
        $paginate = $request->query('paginate') ? $request->query('paginate'): 0;

        return $this->productService->index($paginate);
    }

    public function show(string $code){
        
        return $this->productService->show($code);
    }

    public function update(Request $request, $code){
        
        try {
            Validator::make($request->all(), [
                'code' => 'required|string',
                'status' => 'required|string|in:draft,trash,published',
                'imported_t' => 'required|date_format:Y-m-d H:i:s',
                'url' => 'required|url',
                'creator' => 'required|string',
                'created_t' => 'nullable|integer',
                'last_modified_t' => 'nullable|integer',
                'product_name' => 'nullable|string',
                'quantity' => 'nullable|string',
                'brands' => 'nullable|string',
                'categories' => 'nullable|string',
                'labels' => 'nullable|string',
                'cities' => 'nullable|string',
                'purchase_places' => 'nullable|string',
                'stores' => 'nullable|string',
                'ingredients_text' => 'nullable|string',
                'traces' => 'nullable|string',
                'serving_size' => 'nullable|string',
                'serving_quantity' => 'nullable|numeric',
                'nutriscore_score' => 'nullable|integer',
                'nutriscore_grade' => 'nullable|string',
                'main_category' => 'nullable|string',
                'image_url' => 'nullable|string'
            ])->validate();
            
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'The data provided is invalid',
                'errors' => $e->errors()
            ], 422);
        }

        return $this->productService->update($code, $request);
    }

    public function delete(string $code){
        
        return $this->productService->delete($code);
    }
}
