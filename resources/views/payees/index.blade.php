@extends('adminlte::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Destinatarios de pago')

@section('content_header')
<h1>Destinatarios</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Lista de destinatarios</span>
                        <a href="{{ route('payees.create') }}" class="btn btn-secondary">Nuevo destinatario</a>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Alias</th>
                                        <th>Contacto</th>
                                        <th>Notas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payees as $payee)
                                        <tr>
                                            <td>{{ $payee->name }}</td>
                                            <td>{{ $payee->alias ?? '—' }}</td>
                                            <td>{{ $payee->contact_info ?? '—' }}</td>
                                            <td>{{ Str::limit($payee->notes, 80) }}</td>
                                            <td class="d-flex gap-2">
                                                <a href="{{ route('payees.edit', $payee) }}" class="btn btn-sm btn-primary mr-1">Editar</a>
                                                <form action="{{ route('payees.destroy', $payee) }}" method="POST" onsubmit="return confirm('¿Eliminar este destinatario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No hay destinatarios registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
