@props([
    'formTitle' => '',
    'formAction' => '',
    'showForm' => true,
    'message' => '',
])

@if ($showForm)
    <x-card title="{{ $formTitle }}" shadow separator class="mt-4">
        <form wire:submit.prevent="{{ $formAction }}">
            <div class="flex flex-col gap-4">
                <x-textarea
                    wire:model="message"
                    placeholder="{{ __('Your comment...') }}"
                    rows="5"
                    class="w-full"
                    :value="$message"
                />
                @error('message')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
                <div class="flex justify-end">
                    <x-button
                        type="submit"
                        label="{{ $formAction === 'createComment' ? __('Post comment') : __('Update comment') }}"
                        class="btn-primary"
                    />
                </div>
            </div>
        </form>
    </x-card>
@endif
