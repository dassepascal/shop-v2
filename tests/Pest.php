<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature','Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createInvoiceableOrder(array $attributes = []): App\Models\Order
{
    $country = App\Models\Country::create(['name' => 'France', 'tax' => 0.2]);
    foreach ([['En attente', 'attente', 1], ['Payé', 'paye', 5], ['Annulé', 'annule', 11]] as [$name, $slug, $indice]) {
        App\Models\State::create(['name' => $name, 'slug' => $slug, 'color' => 'blue', 'indice' => $indice]);
    }

    $order = App\Models\Order::factory()->create(array_merge([
        'user_id' => App\Models\User::factory()->create()->id,
        'state_id' => App\Models\State::whereSlug('paye')->first()->id,
        'payment' => 'carte',
        'pick' => false,
        'shipping' => 10,
        'total' => 120,
        'invoice_id' => null,
        'invoice_number' => null,
    ], $attributes));

    $order->addresses()->create([
        'civility' => 'M.', 'name' => 'Dupont', 'firstname' => 'Jean', 'address' => '1 rue de la Paix',
        'postal' => '75001', 'city' => 'Paris', 'phone' => '0102030405', 'country_id' => $country->id,
    ]);
    $order->products()->create(['name' => 'Produit test', 'total_price_gross' => 120, 'quantity' => 2]);

    return $order;
}
