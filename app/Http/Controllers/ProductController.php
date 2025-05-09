<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{      
    protected $productService;

    /**
     * @OA\Info(title="API Documentation", version="1.0.0")
     * 
     * @OA\SecurityScheme(
     *  type="http",
     *  description="Acess token obtido na autenticação",
     *  name="Authorization",
     *  in="header",
     *  scheme="bearer",
     *  bearerFormat="JWT",
     *  securityScheme="bearerToken" 
     * )
    **/

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
    * @OA\Get(
    *   tags={"products"},
    *   summary="Returns all products",
    *   path="/api/products",
    *   security={ {"bearerToken": {}} },
    *   @OA\Parameter(
    *       name="paginate",
    *       in="query",
    *       required=false,
    *       description="Choose 1 to paginate and 0 not paginate",
    *       @OA\Schema(type="integer", example=0)
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\MediaType(
    *           mediaType="application/json"
    *       )
    *   )
    * )
    **/
    public function index(Request $request){
        
        $paginate = $request->query('paginate') ? $request->query('paginate') : 0;
        
        return $this->productService->index($paginate);
    }


    /**
    * @OA\Get(
    *   tags={"products"},
    *   summary="Returns specific product by code",
    *   path="/api/products/{code}",
    *   security={ {"bearerToken": {}} },
    *   @OA\Parameter(
    *       name="code",
    *       in="path",
    *       required=true,
    *       @OA\Schema(type="integer", example=8457489)
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\MediaType(
    *           mediaType="application/json"
    *       )
    *   )
    * )
    **/
    public function show(string $code){
        
        return $this->productService->show($code);
    }


    /**
     * @OA\Put(
     *   tags={"products"},
     *   summary="Update a specific product by code",
     *   path="/api/products/{code}",
     *   security={ {"bearerToken": {}} },
     *   @OA\Parameter(
     *       name="code",
     *       in="path",
     *       description="Product code",
     *       required=true
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"status", "imported_t", "url", "creator"},
     *               @OA\Property(property="status", type="string", enum={"draft", "trash", "published"}, example="draft"),
     *               @OA\Property(property="imported_t", type="string", format="date-time", example="2025-05-08 10:00:00"),
     *               @OA\Property(property="url", type="string", format="url", example="https://example.com"),
     *               @OA\Property(property="creator", type="string", example="John Doe"),
     *               @OA\Property(property="created_t", type="integer", nullable=true, example=1623497200),
     *               @OA\Property(property="last_modified_t", type="integer", nullable=true, example=1623497250),
     *               @OA\Property(property="product_name", type="string", nullable=true, example="Product A"),
     *               @OA\Property(property="quantity", type="string", nullable=true, example="10"),
     *               @OA\Property(property="brands", type="string", nullable=true, example="Brand X"),
     *               @OA\Property(property="categories", type="string", nullable=true, example="Food"),
     *               @OA\Property(property="labels", type="string", nullable=true, example="Label Y"),
     *               @OA\Property(property="cities", type="string", nullable=true, example="City Z"),
     *               @OA\Property(property="purchase_places", type="string", nullable=true, example="Store X"),
     *               @OA\Property(property="stores", type="string", nullable=true, example="Store Y"),
     *               @OA\Property(property="ingredients_text", type="string", nullable=true, example="Water, Sugar"),
     *               @OA\Property(property="traces", type="string", nullable=true, example="Contains nuts"),
     *               @OA\Property(property="serving_size", type="string", nullable=true, example="100g"),
     *               @OA\Property(property="serving_quantity", type="number", nullable=true, example=1),
     *               @OA\Property(property="nutriscore_score", type="integer", nullable=true, example=50),
     *               @OA\Property(property="nutriscore_grade", type="string", nullable=true, example="B"),
     *               @OA\Property(property="main_category", type="string", nullable=true, example="Beverages"),
     *               @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/image.jpg")
     *           ),
     *       ),
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="OK",
     *       @OA\MediaType(
     *           mediaType="application/json"
     *       )
     *   )
     * )
     */
    public function update(Request $request, $code){

        return $this->productService->update($code, $request->all());
    }


    /**
    * @OA\Delete(
    *   tags={"products"},
    *   summary="Delete specific product",
    *   description="Delete a specific product by code, changing status to 'trash'",
    *   path="/api/products/{code}",
    *   security={ {"bearerToken": {}} },
    *   @OA\Parameter(
    *       name="code",
    *       in="path",
    *       description="Product code",
    *       required=true
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\MediaType(
    *           mediaType="application/json"),
    *       )
    * )
    **/
    public function delete(string $code){
        
        return $this->productService->delete($code);
    }


    /**
    * @OA\Post(
    *   tags={"products"},
    *   summary="Publish specific product",
    *   description="Publish a specific product by code, changing status to 'published'",
    *   path="/api/products/{code}/publish",
    *   security={ {"bearerToken": {}} },
    *   @OA\Parameter(
    *       name="code",
    *       in="path",
    *       description="Product code",
    *       required=true
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\MediaType(
    *           mediaType="application/json"),
    *       )
    * )
    **/
    public function publish(string $code){
        
        return $this->productService->publish($code);
    }
}
