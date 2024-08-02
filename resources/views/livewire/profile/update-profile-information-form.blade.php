<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $imagen = '';
    public string $rol = '';
    public string $usuario = '';
    public string $numero_identificacion = '';
    public string $estado = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->imagen = Auth::user()->imagen;
        $this->rol = Auth::user()->rol;
        $this->usuario = Auth::user()->usuario;
        $this->numero_identificacion = Auth::user()->numero_identificacion;
        $this->estado = Auth::user()->estado;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'numero_identificacion' => ['required', 'numeric', Rule::unique('users', 'numero_identificacion')->ignore($user->id)],
            'rol' => ['required', 'boolean'],
            'usuario' => ['required', 'string', Rule::unique('users', 'usuario')->ignore($user->id)],
            'estado' => ['required', 'boolean'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $path = session('url.intended', RouteServiceProvider::HOME);

            $this->redirect($path);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-4xl font-black text-gray-600">
            {{ 'Actualice su perfil' }}
        </h2>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-2  ">
        <div class="mb-2">
            <x-input-label for="imagen_nueva" :value="__('Imagen')" class="text-indigo-500" />
            @if ($imagen)
                <img src="{{ asset('storage/uploads/imagenes_usuarios/' . $imagen) }}" alt="Imagen del usuario"
                    class="object-cover my-2 w-32" />
            @endif
            <x-text-input wire:model="imagen_nueva" id="imagen_nueva" class="border-none p-0 text-xs block mt-1 w-full"
                type="file" name="imagen_nueva" accept="image/*" />
            <x-input-error :messages="$errors->get('imagen_nueva')" class="mt-2" />
        </div>

        <div class="flex gap-2">
            <div class="w-full">
                <x-input-label for="name" :value="__('Nombre')" class="hidden" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full capitalize text-gray-500"
                    type="text" name="name" autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="w-full">
                <x-input-label for="usuario" :value="__('Nombre Usuario')" class="hidden" />
                <x-text-input wire:model="usuario" id="usuario" class="block mt-1 w-full capitalize text-gray-500"
                    type="text" name="usuario" autocomplete="usuario" />
                <x-input-error :messages="$errors->get('usuario')" class="mt-2" />
            </div>
        </div>

        <div class="flex gap-2">
            <div class="w-full">
                <x-input-label for="email" :value="__('Email')" class="hidden" />
                <x-text-input wire:model="email" id="email" name="email" type="email"
                    class="mt-1 block w-full text-gray-500" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button wire:click.prevent="sendVerification"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="w-full">
                <x-input-label for="numero_identificacion" :value="__('No. Identiifación')" class="hidden" />
                <x-text-input wire:model="numero_identificacion" id="numero_identificacion"
                    class="block mt-1 w-full text-gray-500" type="text" name="numero_identificacion"
                    autocomplete="numero_identificacion" />
                <x-input-error :messages="$errors->get('numero_identificacion')" class="mt-2" />
            </div>
        </div>

        <div class="flex gap-2">
            <div class="w-full">
                <x-input-label for="rol" :value="__('Rol')" class="hidden" />
                <select wire:model="rol" id="rol" name="rol"
                    class="p-1 ring-0 text-gray-500 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                    <option value="1">Admin</option>
                    <option value="0">Cajero</option>
                </select>
                <x-input-error :messages="$errors->get('rol')" class="mt-2" />
            </div>
            <div class="w-full">
                <x-input-label for="estado" :value="__('Estado')" class="hidden" />
                <select wire:model="estado" id="estado" name="estado"
                    class="text-gray-500 p-1 ring-0 border-2 border-gray-200 transition-border focus:border-gray-100 focus:ring-0 block mt-1 w-full capitalize">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            <x-action-message class=" text-indigo-800" on="profile-updated">
                {{ __('Guardando...') }}
            </x-action-message>
        </div>
    </form>
</section>
