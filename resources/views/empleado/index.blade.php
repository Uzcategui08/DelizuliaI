@extends('adminlte::page')

@section('title', 'Empleados')

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
                                {{ __('Empleados') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('empleados.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
									<th >ID Empleado</th>
									<th >Nombre</th>
									<th >Cédula</th>
									<th >Cargo</th>
                                    <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($empleados as $empleado)
                                        <tr>
										<td >{{ $empleado->id_empleado }}</td>
										<td >{{ $empleado->nombre }}</td>
										<td >{{ $empleado->cedula }}</td>
                                        <td>
                                            @php
                                                $cargos = [
                                                    1 => 'Técnico',
                                                    2 => 'Administrativo', 
                                                    3 => 'Supervisor',
                                                    4 => 'Gerente',
                                                    5 => 'Dueño'
                                                ];
                                                echo $cargos[$empleado->cargo] ?? 'Desconocido';
                                            @endphp
                                        </td>

                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('empleados.destroy', $empleado->id_empleado) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('empleados.show', $empleado->id_empleado) }}"><i class="fa fa-fw fa-eye"></i> </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('empleados.edit', $empleado->id_empleado) }}"><i class="fa fa-fw fa-edit"></i> </a>
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
