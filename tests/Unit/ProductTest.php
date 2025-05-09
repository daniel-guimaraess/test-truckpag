<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(){

        Product::factory()->count(5)->create();

        $response = $this->withoutMiddleware([\App\Http\Middleware\apiProtectedRoute::class])
                     ->getJson('/api/products');

        $response->assertStatus(200);

    }

    public function testShow(){

        $product = Product::factory()->create();

        $response = $this->withoutMiddleware([\App\Http\Middleware\apiProtectedRoute::class])
                     ->getJson('/api/products/'.$product->code);

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $product = Product::factory()->create();

        $updatedData = [
            'product_name' => 'Produto Atualizado',
            'quantity' => 20,
            'brands' => 'Marca Atualizada',
        ];

        $response = $this->withoutMiddleware([\App\Http\Middleware\apiProtectedRoute::class])
                        ->putJson('/api/products/'.$product->code, $updatedData);

        $response->assertStatus(200);

        $product->refresh();
        $this->assertEquals('Produto Atualizado', $product->product_name);
        $this->assertEquals(20, $product->quantity);
        $this->assertEquals('Marca Atualizada', $product->brands);
    }
}