@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Presupuestos') }}
                            </span>
                            <div class="float-right">
                                <a href="{{ route('presupuestos.create') }}" class="btn btn-secondary btn-m float-right" data-placement="left">
                                    {{ __('Crear Nuevo') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th>ID Presupuesto</th>
                                        @if(auth()->user()->hasRole('admin'))
                                        <th>Técnico</th>
                                        @endif
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Validez</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($presupuestos as $presupuesto)
                                        <tr>
                                            <td>{{ $presupuesto->id_presupuesto }}</td>
                                            @if(auth()->user()->hasRole('admin'))
                                            <td>{{ $presupuesto->user->name }}</td>
                                            @endif
                                            <td>{{ $presupuesto->cliente->nombre }}</td>
                                            <td>{{ $presupuesto->f_presupuesto }}</td>
                                            <td>{{ $presupuesto->validez }}</td>

                                            <td>
                                                <span class="badge badge-lg fs-6 p-2
                                                            @if($presupuesto->estado == 'aprobado') badge-success 
                                                            @elseif($presupuesto->estado == 'pendiente') badge-warning 
                                                            @elseif($presupuesto->estado == 'rechazado') badge-danger 
                                                            @else badge-secondary 
                                                            @endif">
                                                    {{ $presupuesto->estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('presupuestos.destroy', $presupuesto->id_presupuesto) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('presupuestos.show', $presupuesto->id_presupuesto) }}">
                                                        <i class="fa fa-fw fa-eye"></i> 
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('presupuestos.edit', $presupuesto->id_presupuesto) }}">
                                                        <i class="fa fa-fw fa-edit"></i> 
                                                    </a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('presupuestos.pdf', $presupuesto->id_presupuesto) }}" target="_blank">
                                                        <i class="">Es</i> 
                                                    </a>
                                                    <a class="btn btn-sm btn-info" href="{{ route('budget.pdf', $presupuesto->id_presupuesto) }}" target="_blank">
                                                        <i class="">En</i>
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"> 
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
