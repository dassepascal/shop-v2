<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImages;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $names = ['Laptop', 'Phone', 'Tablet', 'Monitor', 'Keyboard', 'Mouse', 'Headphones', 'Speaker', 'Camera', 'Printer'];
        $name = fake()->randomElement($names);

        return [
            'name' => $name,
            'price' => $this->faker->randomFloat(2, 10, 1500),
            'weight' => $this->faker->randomFloat(2, 0.3, 3),
            'active' => true,
            'quantity' => 100,
            'quantity_alert' => 10,
            'image' => strtolower($name) . '.png',
            'description' => fake()->sentence(),
            'unique_id' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            ProductImages::create([
                'product_unique_id' => $product->unique_id,
                'image' => $product->image,
            ]);

            if (rand(0, 1)) {
                ProductImages::create([
                    'product_unique_id' => $product->unique_id,
                    'image' => $product->name . '_alt.png',
                ]);
            }
        });
    }
}
