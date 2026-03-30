<?php

use App\Models\Order;
use App\Services\Invoice as InvoiceService;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

new #[Title('Show order')]
class extends Component {

    public Order $order;

    public function mount(Order $order): void
    {
        if(auth()->user()->id != $order->user_id) {
            abort(403);
        }

        $this->order = $order;
    }

    public function invoice()
    {
        // Si la facture n'existe pas encore, on la crée
        if (!$this->order->invoice_id) {
            $invoiceService = new InvoiceService();
            $response = $invoiceService->create($this->order, true);
            if ($response->successful()) {
                $data = $response->json();
                $this->order->invoice_id = $data['invoice_id'] ?? null;
                $this->order->invoice_number = $data['invoice_number'] ?? null;
                $this->order->save();
            } else {
                session()->flash('error', __('Invoice not available.'));
                return;
            }
        }

        // Téléchargement du PDF
        $url = config('invoice.url') . 'invoices/' . $this->order->invoice_id . '/pdf?api_token=' . config('invoice.token');
        $response = Http::get($url);

        if (!$response->successful()) {
            session()->flash('error', __('Invoice not available.'));
            return;
        }

        $name = 'facture-' . $this->order->invoice_number . '.pdf';
        Storage::disk('invoices')->put($name, $response->body());

        return response()->download(storage_path('app/invoices/' . $name))->deleteFileAfterSend();
    }

}; ?>

<div>
    <x-card class="flex justify-center items-center mt-6 bg-gray-100" title="{{ __('Details of my order') }}" shadow separator >
        <x-elements :order="$order" />
        <br>
        <x-card class="w-full sm:min-w-[50vw]" title="{{ __('State') }}" shadow separator progress-indicator >
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <p class="mb-2 sm:mb-0 sm:mr-4"><strong>@lang('Payment method') :</strong> {{ $order->payment_text }}</p>
                <x-badge value="{{ $order->state->name }}" class="p-3 bg-{{ $order->state->color }}-400 self-start sm:self-center" />
            </div>
            @if($order->state->slug === 'carte' || $order->state->slug === 'erreur')
                <br>
                <x-alert title="{!! __('You were unable to make your credit card payment.') !!}" description="{{ __('Please contact us.') }}" icon="o-exclamation-triangle" class="alert-warning" />
            @endif
            @if($order->state->slug === 'paiement_ok')
                <br>
                @if(session('error'))
                    <x-alert title="{{ session('error') }}" icon="o-exclamation-triangle" class="alert-warning mb-2" />
                @endif
                <x-button label="{{ __('Download invoice') }}" wire:click="invoice" class="btn-outline" spinner />
            @endif
        </x-card>
        <x-slot:actions>
            <x-button label="{{ __('Back to orders') }}" class="btn-outline" link="{{ route('orders') }}" icon="c-arrow-long-left" wire:/>
        </x-slot:actions>
    </x-card>
</div>
