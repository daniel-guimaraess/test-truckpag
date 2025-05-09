<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'status' => $this->faker->randomElement(['draft', 'trash', 'published']),
            'imported_t' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'url' => $this->faker->url,
            'creator' => $this->faker->userName,
            'created_t' => $this->faker->optional()->unixTime(),
            'last_modified_t' => $this->faker->optional()->unixTime(),
            'product_name' => $this->faker->word(),
            'quantity' => $this->faker->optional()->randomDigit() . ' units',
            'brands' => $this->faker->optional()->company,
            'categories' => $this->faker->optional()->word,
            'labels' => $this->faker->optional()->word,
            'cities' => $this->faker->optional()->city,
            'purchase_places' => $this->faker->optional()->city,
            'stores' => $this->faker->optional()->company,
            'ingredients_text' => $this->faker->optional()->text(100),
            'traces' => $this->faker->optional()->word,
            'serving_size' => $this->faker->optional()->randomDigitNotNull() . 'g',
            'serving_quantity' => $this->faker->optional()->randomFloat(2, 10, 500),
            'nutriscore_score' => $this->faker->optional()->numberBetween(-15, 40),
            'nutriscore_grade' => $this->faker->optional()->randomElement(['a', 'b', 'c', 'd', 'e']),
            'main_category' => $this->faker->optional()->word,
            'image_url' => $this->faker->optional()->imageUrl(),
        ];
    }
}
