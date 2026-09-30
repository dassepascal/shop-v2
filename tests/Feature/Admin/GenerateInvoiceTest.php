<?php

use App\Models\{Shop, User};
use Illuminate\Support\Facades\{Http, View};
use Livewire\Volt\Volt;

beforeEach(function () {
    config(['invoice.url' => 'https://factures.test/api/']);
    // Partagé par AppServiceProvider hors console uniquement
    View::share('shop', Shop::factory()->create());
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

it('stores the invoice id and number when the invoice is generated', function () {
    Http::fake(['*' => Http::response(['id' => 42, 'number' => 'F-001'])]);
    $order = createInvoiceableOrder();

    Volt::test('admin.shop.orders.show', ['order' => $order])
        ->call('generateInvoice');

    expect($order->fresh())
        ->invoice_id->toBe(42)
        ->invoice_number->toBe('F-001');
});

it('shows an error and leaves the order untouched when the invoicing service is down', function () {
    Http::fake(['*' => Http::failedConnection()]);
    $order = createInvoiceableOrder();

    $component = Volt::test('admin.shop.orders.show', ['order' => $order])
        ->call('generateInvoice');

    expect(json_encode($component->effects['xjs'] ?? []))
        ->toContain('alert-error')
        ->and($order->fresh())
        ->invoice_id->toBeNull()
        ->invoice_number->toBeNull();
});
