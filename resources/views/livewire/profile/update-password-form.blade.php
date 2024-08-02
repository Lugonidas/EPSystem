<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-4xl font-black text-gray-600">
            {{ "Actualice su contraseña"}}
        </h2>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-2">
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="hidden" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" placeholder="Contraseña Actual" class="mt-1 block w-full text-gray-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="hidden" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" placeholder="Nueva contraseña" class="mt-1 block w-full text-gray-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="hidden" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" placeholder="Confirmar contraseña" class="mt-1 block w-full text-gray-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                {{ __('Guardado.') }}
            </x-action-message>
        </div>
    </form>
</section>
