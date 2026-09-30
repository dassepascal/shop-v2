<?php

use App\Services\Invoice;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['invoice.url' => 'https://factures.test/api/', 'invoice.token' => 'secret']);
});

it('sends the invoice to the invoicing application', function () {
    Http::fake(['*' => Http::response(['id' => 42, 'number' => 'F-001'])]);

    $response = (new Invoice)->create(createInvoiceableOrder());

    expect($response->successful())->toBeTrue();
    Http::assertSent(fn ($request) => $request->url() === 'https://factures.test/api/invoices.json'
        && $request['api_token'] === 'secret'
        && $request['invoice']['client_name'] === 'Jean Dupont'
        && $request['invoice']['tva_rate'] == 20
        && $request['invoice']['items'] === [
            ['description' => 'Produit test', 'quantity' => 2, 'unit_price_ht' => 50.0],
            ['description' => "Frais d'expédition", 'quantity' => 1, 'unit_price_ht' => 10.0],
        ]);
});

it('returns a 503 response without calling the service when no url is configured', function () {
    config(['invoice.url' => null]);
    Http::fake();

    $response = (new Invoice)->create(createInvoiceableOrder());

    expect($response->status())->toBe(503);
    Http::assertNothingSent();
});

it('returns a 503 response instead of throwing when the service is unreachable', function () {
    Http::fake(['*' => Http::failedConnection()]);

    $response = (new Invoice)->create(createInvoiceableOrder());

    expect($response->status())->toBe(503);
});
