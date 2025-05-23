<?php

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\feature>
 */
class FeatureFactory extends Factory
{

    protected $model = Feature::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        return [
            'name' => $this->faker->randomElement([
                'taille',
                'couleur',
                'matière',
                'poids',
                'diamètre',
                'peau',



            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
