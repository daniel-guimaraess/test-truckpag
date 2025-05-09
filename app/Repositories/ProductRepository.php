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

        $product->update($request);
    }

    public function delete(Product $product) {   

        $product->update(['status' => 'trash']);
    }

    public function publish(Product $product) {   

        $product->update(['status' => 'published']);
    }
}
