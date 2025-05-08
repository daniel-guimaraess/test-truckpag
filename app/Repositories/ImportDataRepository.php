<?php

namespace App\Repositories;

use App\Models\Product;

class ImportDataRepository
{
    public function create(array $products){

        foreach($products as $product){
            
            Product::create([
                "code" => ltrim($product['code'], '"'),
                "status" => "published",
                "imported_t" => now()->format('Y-m-d H:i:s'),
                "url" => $product['url'],
                "creator" => $product['creator'],
                "created_t" => intval($product['created_t']),
                "last_modified_t" => intval($product['last_modified_t']),
                "product_name" => $product['product_name'] ?? null,
                "quantity" => $product['quantity'] ?? null,
                "brands" => $product['brands'] ?? null,
                "categories" => $product['categories'] ?? null,
                "labels" => $product['labels'] ?? null,
                "cities" => $product['cities'] ?? null,
                "purchase_places" => $product['purchase_places'] ?? null,
                "stores" => $product['stores'] ?? null,
                "ingredients_text" => $product['ingredients_text'] ?? null,
                "traces" => $product['traces'] ?? null,
                "serving_size" => $product['serving_size'] ?? null,
                "serving_quantity" => floatval($product['serving_quantity']) ?? null,
                "nutriscore_score" => intval($product['nutriscore_score']) ?? null,
                "nutriscore_grade" => $product['nutriscore_grade'] ?? null,
                "main_category" => $product['main_category'] ?? null,
                "image_url" => $product['image_url'] ?? null,
            ]);
        }
    }
}
