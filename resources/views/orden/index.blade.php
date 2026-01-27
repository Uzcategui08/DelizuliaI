@extends('adminlte::page')

@section('title', 'Órdenes')

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
                                {{ __('Órdenes') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('ordens.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
                                  {{ __('Crear Nuevo') }}
                                </a>
                              </div>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable">
                                <thead class="thead">
                                    <tr> 
									<th >ID Orden</th>
									<th >Fecha</th>
									<th >Descripción</th>
									<th >Técnico</th>
                                    <th >Estado</th>
                                    <th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordens as $orden)
                                        <tr>
                                            <td >{{ $orden->id_orden }}</td>
                                            <td >{{ $orden->f_orden }}</td>
                                            <td>
                                                @php
                                                    $items = is_array($orden->items) ? $orden->items : (is_string($orden->items) ? json_decode($orden->items, true) : []);
                                                @endphp
                                                @if(is_array($items) && count($items) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach ($items as $item)
                                                            <li>{{ $item['descripcion'] ?? 'Descripción no disponible' }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Sin descripciones</span>
                                                @endif
                                            </td>
                                            <td >{{ $orden->empleado->nombre }}</td>
                                            <td> 
                                                <span class="badge badge-lg fs-6 p-2
                                                @if($orden->estado == 'completado') badge-success
                                                @elseif($orden->estado == 'en_proceso') badge-warning
                                                @elseif($orden->estado == 'cancelado') badge-danger
                                                @else badge-secondary
                                                @endif">
                                                {{ $orden->estado }}
                                                </span>
                                                </td>                                        

                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('ordens.destroy', $orden->id_orden) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('ordens.show', $orden->id_orden) }}">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('ordens.edit', $orden->id_orden) }}">
                                                        <i class="fa fa-fw fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('ordens.pdf', $orden->id_orden) }}" target="_blank">
                                                        <i class="fa fa-fw fa-print"></i>
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