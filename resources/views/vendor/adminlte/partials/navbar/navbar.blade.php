@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        @auth
        @php
            try {
                $unreadCount = auth()->user()->unreadNotifications()
                    ->where('type', 'App\\Notifications\\LowStockNotification')
                    ->count();

                $notifications = auth()->user()->unreadNotifications()
                    ->where('type', 'App\\Notifications\\LowStockNotification')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                \Log::info('Notificaciones encontradas: ' . $notifications->count());
                \Log::info('Notificaciones no leídas: ' . $unreadCount);
            } catch (\Exception $e) {
                \Log::error('Error al cargar notificaciones: ' . $e->getMessage());
                $notifications = collect();
                $unreadCount = 0;
            }
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{ $unreadCount }} Notificaciones sin leer</span>
                <div class="dropdown-divider"></div>
                
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="dropdown-item" @if(!$notification->read_at) style="background-color: #f8f9fa;" @endif>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="text-dark flex-grow-1">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-sm mb-0">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                                            <p class="text-sm text-muted mb-0">
                                                <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="ml-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-success p-0" title="Marcar como leída">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="dropdown-divider my-0"></div>
                    @endforeach
                    <div class="dropdown-footer p-0">
                        <div class="d-flex justify-content-between">
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-dark w-100 text-left">
                                        <i class="fas fa-check-double mr-1"></i> Marcar todas como leídas
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('notifications.index') }}" class="btn btn-link text-dark w-100 text-left">
                                    <i class="fas fa-list mr-1"></i> Ver todas las notificaciones
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <span class="dropdown-item">No hay notificaciones recientes</span>
                @endif
            </div>
        </li>
        @endauth
        
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
