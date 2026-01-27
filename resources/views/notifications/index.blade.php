@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
<h1>Notificaciones</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex mb-3 align-items-center">
            <form method="GET" action="{{ route('notifications.index') }}" class="form-inline">
                <div class="input-group mr-2" style="min-width:220px; max-width:60vw;">
                    <input type="text" name="q" class="form-control" placeholder="Buscar..." value="{{ request('q', $q ?? '') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                    </div>
                </div>

                <div class="form-group mr-2">
                    <select name="status" class="form-control">
                        <option value="all" {{ (request('status', $status ?? 'all') === 'all') ? 'selected' : '' }}>Todas</option>
                        <option value="unread" {{ (request('status', $status ?? '') === 'unread') ? 'selected' : '' }}>No leídas</option>
                        <option value="read" {{ (request('status', $status ?? '') === 'read') ? 'selected' : '' }}>Leídas</option>
                    </select>
                </div>
            </form>

            <div class="ml-auto mb-0">
                <button id="mark-all-btn" class="btn btn-outline-secondary" data-action="{{ route('notifications.markAllRead') }}">Marcar todas como leídas</button>
            </div>
        </div>

        @if($notifications->count() === 0)
            <div class="alert alert-info">No hay notificaciones que mostrar.</div>
        @else
            <div class="list-group">
                @foreach($notifications as $notification)
                    <div class="list-group-item {{ is_null($notification->read_at) ? 'bg-light' : '' }} d-flex align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $notification->data['title'] ?? ($notification->data['message'] ?? 'Notificación') }}</strong>
                                    <div class="text-muted small">{{ $notification->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="ml-3 text-right">
                                    <div class="small text-muted">{{ class_basename($notification->type) }}</div>
                                    @if(is_null($notification->read_at))
                                        <form class="mark-read-form d-inline-block" action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-primary">Marcar leída</button>
                                        </form>
                                    @else
                                        <span class="badge badge-secondary">Leída</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2">
                                {!! nl2br(e($notification->data['message'] ?? '')) !!}
                                @if(!empty($notification->data['almacen']) || !empty($notification->data['id_llave']))
                                    <div class="text-muted small mt-2">
                                        @if(!empty($notification->data['almacen']))
                                            <span>Almacén: {{ $notification->data['almacen'] }}</span>
                                        @endif
                                        @if(!empty($notification->data['almacen']) && !empty($notification->data['id_llave']))
                                            <span class="mx-2">|</span>
                                        @endif
                                        @if(!empty($notification->data['id_llave']))
                                            <span>ID Llave: {{ $notification->data['id_llave'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 notifications-pagination">
                <div class="d-flex justify-content-center">
                    <div class="w-100" style="max-width: 100%; overflow-x: auto;">
                        <div style="min-width: 320px;">
                            {{-- fallback to bootstrap paginator to avoid missing view errors --}}
                            {{ $notifications->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // mark single notification as read via AJAX (only forms with .mark-read-form)
    document.querySelectorAll('form.mark-read-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var action = form.getAttribute('action');
            var token = form.querySelector('input[name="_token"]').value;

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (res) {
                return res.json();
            }).then(function (data) {
                if (data.success) {
                    // hide or style the parent list-group-item
                    var item = form.closest('.list-group-item');
                    if (item) {
                        item.classList.remove('bg-light');
                        var badge = item.querySelector('.badge');
                        if (!badge) {
                            var span = document.createElement('span');
                            span.className = 'badge badge-secondary';
                            span.textContent = 'Leída';
                            form.parentNode.replaceChild(span, form);
                        } else {
                            badge.textContent = 'Leída';
                        }
                    }

                    // try to decrement bell unread badge if present
                    var bellBadge = document.querySelector('.navbar-badge');
                    if (bellBadge) {
                        var count = parseInt(bellBadge.textContent || '0', 10) - 1;
                        if (count <= 0) {
                            bellBadge.remove();
                        } else {
                            bellBadge.textContent = count;
                        }
                    }
                }
            }).catch(function (err) {
                console.error('Error marking notification read', err);
                window.location.reload();
            });
        });
    });

    // mark-all-as-read button (standalone)
    var markAllBtn = document.getElementById('mark-all-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var action = markAllBtn.getAttribute('data-action');
            var token = window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : '{{ csrf_token() }}';

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    // remove unread styling and replace forms with badges
                    document.querySelectorAll('.list-group-item.bg-light').forEach(function (item) {
                        item.classList.remove('bg-light');
                        var forms = item.querySelectorAll('form');
                        forms.forEach(function (f) { f.remove(); });
                        if (!item.querySelector('.badge')) {
                            var span = document.createElement('span');
                            span.className = 'badge badge-secondary';
                            span.textContent = 'Leída';
                            item.appendChild(span);
                        }
                    });

                    // remove bell badge
                    var bellBadge = document.querySelector('.navbar-badge');
                    if (bellBadge) bellBadge.remove();
                }
            }).catch(function () { window.location.reload(); });
        });
    }
});
</script>
@stop

@section('css')
<style>
/* Page-scoped pagination and icon fixes for notifications page */
.notifications-pagination .pagination {
    display: inline-flex;
    flex-wrap: nowrap;
    align-items: center;
    margin: 0;
}
.notifications-pagination .pagination .page-item { margin: 0 2px; }
.notifications-pagination .pagination .page-link {
    padding: .35rem .5rem;
    min-width: 36px;
    height: 36px;
    line-height: 1;
    font-size: .9rem;
    border-radius: .35rem;
    overflow: hidden;
}
.notifications-pagination .pagination .page-link svg,
.notifications-pagination .pagination .page-link i {
    width: 1em !important;
    height: 1em !important;
    font-size: 1rem !important;
    display: inline-block;
    vertical-align: middle;
}
/* Ensure very large icons are capped */
.notifications-pagination .page-link > * {
    max-width: 1.6rem;
    max-height: 1.6rem;
}
/* Make the pager responsive: allow horizontal scroll but keep small on mobile */
@media (max-width: 576px) {
    .notifications-pagination .pagination .page-link { min-width: 34px; padding: .25rem .4rem; }
}
</style>
@stop
