<?php

use App\Models\Product;
use App\Models\Feature;
use App\Models\ProductImages;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a product', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 99.99,
        'unique_id' => 'TEST123',
    ]);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Test Product')
        ->and($product->price)->toBe(99.99)
        ->and($product->unique_id)->toBe('TEST123');

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'unique_id' => 'TEST123',
    ]);
});

it('has a features relationship', function () {
    $product = Product::factory()->create();
    $feature = Feature::factory()->create(['name' => 'Couleur']);
    $product->features()->attach($feature->id, ['value' => 'Blue']);

    expect($product->features)->toHaveCount(1)
        ->and($product->features->first()->id)->toBe($feature->id)
        ->and($product->features->first()->name)->toBe('Couleur')
        ->and($product->features->first()->pivot->value)->toBe('Blue');

    $this->assertDatabaseHas('feature_product', [
        'product_id' => $product->id,
        'feature_id' => $feature->id,
        'value' => 'Blue',
    ]);
});

it('has an images relationship with at least one image', function () {
    $product = Product::factory()->create(['unique_id' => 'TEST123']);

    expect($product->images->count())->toBeGreaterThanOrEqual(1) // Accepte 1 ou 2 images
        ->and($product->images->first()->image)->toBe($product->image);
});

it('may have an alternative image', function () {
    $product = Product::factory()->create(['unique_id' => 'TEST123']);

    // Forcer la création d'une image alternative
    ProductImages::create([
        'product_unique_id' => 'TEST123',
        'image' => $product->name . '_alt.png',
    ]);

    expect($product->images->count())->toBeGreaterThanOrEqual(2) // Vérifie au moins 2 images
        ->and($product->images->last()->image)->toBe($product->name . '_alt.png');
});
