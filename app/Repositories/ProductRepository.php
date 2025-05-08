<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{   
    public function index(int|string $paginate){        

        return $paginate ? Product::paginate(20) : Product::all();
    }

    public function show(string $code){      

        return Product::where("code", $code)->first();
    }

    public function update(Product $product, $request){

        $product->update([
            "url" => $request['url'],
            "creator" => $request['creator'],
            "created_t" => intval($request['created_t']),
            "last_modified_t" => intval($request['last_modified_t']),
            "product_name" => $request['product_name'],
            "quantity" => $request['quantity'],
            "brands" => $request['brands'],
            "categories" => $request['categories'],
            "labels" => $request['labels'],
            "cities" => $request['cities'],
            "purchase_places" => $request['purchase_places'],
            "stores" => $request['stores'],
            "ingredients_text" => $request['ingredients_text'],
            "traces" => $request['traces'],
            "serving_size" => $request['serving_size'],
            "serving_quantity" => floatval($request['serving_quantity']),
            "nutriscore_score" => intval($request['nutriscore_score']),
            "nutriscore_grade" => $request['nutriscore_grade'],
            "main_category" => $request['main_category'],
            "image_url" => $request['image_url'],
        ]);
    }

    public function delete(Product $product) {   

        $product->delete();
    }
}
