@extends('adminlte::page')

@section('title', 'Abonos')

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
                                {{ __('Abonos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('abonos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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

									<th >ID de Abono</th>
									<th >Empleado</th>
									<th >Concepto</th>
									<th >Valor</th>
									<th >Fecha</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($abonos as $abono)
                                        <tr>
                                                                                    
										<td >{{ $abono->id_abonos }}</td>
										<td >{{ $abono->empleado->nombre}}</td>
										<td >{{ $abono->concepto }}</td>
										<td >{{ $abono->valor }}</td>
                                        <td>{{ \Carbon\Carbon::parse($abono->a_fecha)->format('m/d/Y') }}</td>

                                            <td>
                                                <div style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('abonos.destroy', $abono->id_abonos) }}" method="POST" class="delete-form" >
                                                    <a class="btn btn-sm btn-primary " href="{{ route('abonos.show', $abono->id_abonos) }}"><i class="fa fa-fw fa-eye"></i> </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('abonos.edit', $abono->id_abonos) }}"><i class="fa fa-fw fa-edit"></i> </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>                                                
                                                </form>
                                                </div>
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
