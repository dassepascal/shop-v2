<?php

namespace App\Services;

use App\Models\Order;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Invoice
{
    public function create(Order $order, $paid = false)
    {
        $order->load('addresses', 'products', 'addresses.country', 'user');

        $addressOrder = $order->addresses->first();

        // Adresse client
        $address = $addressOrder->address;
        if ($addressOrder->addressbis) {
            $address .= ' ' . $addressOrder->addressbis;
        }
        if ($addressOrder->bp) {
            $address .= ' ' . $addressOrder->bp;
        }

        // Nom client
        if ($addressOrder->professionnal && $addressOrder->company) {
            $clientName = $addressOrder->company;
        } else {
            $clientName = trim(($addressOrder->firstname ?? '') . ' ' . ($addressOrder->name ?? ''));
            if (!$clientName) {
                $clientName = trim($order->user->firstname . ' ' . $order->user->name);
            }
        }

        // TVA
        if ($order->pick) {
            $tvaRate = 20;
        } else {
            $deliveryAddress = $order->addresses->count() === 2 ? $order->addresses->get(1) : $addressOrder;
            $tvaRate = ($deliveryAddress->country->tax ?? 0) * 100;
        }

        // Produits
        $items = [];
        foreach ($order->products as $product) {
            $unitPriceHt = $tvaRate > 0
                ? round($product->total_price_gross / $product->quantity / (1 + $tvaRate / 100), 2)
                : round($product->total_price_gross / $product->quantity, 2);

            $items[] = [
                'description'   => $product->name,
                'quantity'      => $product->quantity,
                'unit_price_ht' => $unitPriceHt,
            ];
        }

        // Frais de port
        if ($order->shipping > 0) {
            $items[] = [
                'description'   => "Frais d'expédition",
                'quantity'      => 1,
                'unit_price_ht' => round($order->shipping, 2),
            ];
        }

        $invoice = [
            'client_name'        => $clientName,
            'client_address'     => $address,
            'client_postal_code' => $addressOrder->postal,
            'client_city'        => $addressOrder->city,
            'payment_method'     => $order->payment_text ?? $order->payment,
            'tva_rate'           => $tvaRate,
            'items'              => $items,
        ];

        // Sans application de facturation joignable, on renvoie un échec plutôt que de bloquer la commande
        if (!config('invoice.url')) {
            return new Response(new Psr7Response(503));
        }

        try {
            return Http::post(config('invoice.url') . 'invoices.json', [
                'api_token' => config('invoice.token'),
                'invoice'   => $invoice,
            ]);
        } catch (ConnectionException $e) {
            Log::warning('Facturation injoignable : ' . $e->getMessage());

            return new Response(new Psr7Response(503));
        }
    }
}
