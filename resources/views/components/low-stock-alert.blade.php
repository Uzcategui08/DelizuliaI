@props(['unreadCount'])

@if($unreadCount > 0)
    <div class="fixed bottom-4 right-4 z-50">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">¡Atención!</p>
                    <p class="text-sm">Tienes {{ $unreadCount }} {{ Str::plural('producto', $unreadCount) }} con stock bajo.</p>
                    <a href="{{ route('notifications.index') }}" class="text-red-600 hover:text-red-800 text-sm font-semibold">Ver notificaciones</a>
                </div>
            </div>
        </div>
    </div>
@endif
