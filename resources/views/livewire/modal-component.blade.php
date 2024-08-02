<div
    class="transition-all ease-in absolute bg-[rgba(0,0,0,0.5)] backdrop-blur top-0 left-0 w-full p-2 z-50 h-screen overflow-y-scroll">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-gray-950 p-4 rounded-lg w-full max-w-2xl">
            <!-- Contenido del modal para agregar -->
            <form wire:submit.prevent="{{ $route }}" enctype="multipart/form-data">
                <legend class="text-2xl font-bold text-white text-center mb-2">{{ $formTitle }}</legend>
                {{ $slot }}
            </form>
        </div>
    </div>
</div>
