<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    /**
     * Log the current user out of the application.
     */

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-gray-950 fixed top-0 w-full z-10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-1">
        <div class="flex justify-around">
            <div class="flex w-full justify-start gap-2">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="text-white text-2xl" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden w-full lg:flex lg:justify-center">
                    @if (auth()->user()->rol === 1)
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <i class="fa-solid fa-chart-pie "></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('moduloPOS')" :active="request()->routeIs('moduloPOS')">
                            <i class="fa-solid fa-cart-plus"></i>
                            {{ __('Módulo POS') }}
                        </x-nav-link>
                        <x-nav-link :href="route('productos')" :active="request()->routeIs('productos')">
                            <i class="fa-solid fa-gifts"></i>
                            {{ __('Productos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('categorias')" :active="request()->routeIs('categorias')">
                            <i class="fa-solid fa-gifts"></i>
                            {{ __('Categorias') }}
                        </x-nav-link>
                        <x-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                            <i class="fa-solid fa-user-group"></i>
                            {{ __('Empleados') }}
                        </x-nav-link>
                        <x-nav-link :href="route('clientes')" :active="request()->routeIs('clientes')">
                            <i class="fa-solid fa-users"></i>
                            {{ __('Clientes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('proveedores')" :active="request()->routeIs('proveedores')">
                            <i class="fa-solid fa-truck-field-un"></i>
                            {{ __('Proveedores') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ventas')" :active="request()->routeIs('ventas')">
                            <i class="fa-solid fa-file-export"></i>
                            {{ __('Ventas') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <i class="fa-solid fa-chart-pie "></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('moduloPOS')" :active="request()->routeIs('moduloPOS')">
                            <i class="fa-solid fa-cart-plus"></i>
                            {{ __('Módulo POS') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ventas')" :active="request()->routeIs('ventas')">
                            <i class="fa-solid fa-file-export"></i>
                            {{ __('Ventas') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="capitalize inline-flex items-center px-3 py-2 border border-transparent text-base leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150">
                            <div>
                                {{-- {{auth()->user()}} --}}
                                <span>{{ auth()->user()->usuario }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('moduloPOS')" :active="request()->routeIs('moduloPOS')">
                {{ __('Módulo POS') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('productos')" :active="request()->routeIs('productos')">
                {{ __('Productos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('usuarios')" :active="request()->routeIs('usuarios')">
                {{ __('Empleados') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clientes')" :active="request()->routeIs('clientes')">
                {{ __('Clientes') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ventas')" :active="request()->routeIs('ventas')">
                {{ __('Ventas') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" {{ auth()->user()->name }} x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
