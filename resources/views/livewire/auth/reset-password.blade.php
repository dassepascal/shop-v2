<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\{Hash, Password, Session};
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\{Layout, Locked};
use Livewire\Volt\Component;
use App\Rules\StrongPassword;

// Définition du composant avec l'attribut de mise en page
new class extends Component {
	#[Locked]
	public string $token = '';

	public string $email                 = '';
	public string $password              = '';
	public string $password_confirmation = '';

	// Méthode pour initialiser le composant avec le jeton et l'email
	public function mount(string $token): void
	{
		$this->token = $token;

		$this->email = request()->input('email');
	}

	// Méthode pour réinitialiser le mot de passe
	public function resetPassword(): void
	{
		// Validation des données du formulaire
		$this->validate([
			'token'    => ['required'],
			'email'    => ['required', 'string', 'email'],
			'password' => ['required', 'string', 'min:8', 'confirmed', new StrongPassword()],
		]);

		// Réinitialisation du mot de passe
		$status = Password::reset(
			$this->only('email', 'password', 'password_confirmation', 'token'),
			function ($user) {
				$user->forceFill([
					'password'       => Hash::make($this->password),
					'remember_token' => Str::random(60),
				])->save();

				event(new PasswordReset($user));
			}
		);

		// Gestion du statut de la réinitialisation du mot de passe
		if (Password::PASSWORD_RESET != $status) {
			$this->addError('email', __($status));

			return;
		}

		// Affichage du statut de la réinitialisation
		Session::flash('status', __($status));

		// Redirection vers la page de connexion
		$this->redirectRoute('login', navigate: true);
	}
}; ?>

<div>
    <x-card 
		class="flex justify-center items-center mt-6" 
		title="{{__('Reset Password')}}" 
		shadow 
		separator 
		progress-indicator
	>
        <x-session-status class="mb-4" :status="session('status')" />
        <x-form wire:submit="resetPassword">
            <x-input 
				label="{{__('E-mail')}}" 
				wire:model="email" 
				icon="o-envelope"				
				readonly
			/>
            <x-input 
				label="{{__('Password')}}" 
				wire:model="password" 
				type="password" 
				icon="o-key"
                required
				autofocus
			/>
            <x-input 
				label="{{__('Confirm Password')}}" 
				wire:model="password_confirmation" 
				type="password" 
				icon="o-key" 
				required 
				autocomplete="new-password" 
			/>
			<p class="text-xs text-gray-500"><span class="text-red-600">*</span> @lang('Required information')</p>
            <x-slot:actions>
               <x-button 
					label="{{ __('Reset Password') }}" 
					type="submit" 
					icon="o-paper-airplane" 
					class="btn-primary" 
			   />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>