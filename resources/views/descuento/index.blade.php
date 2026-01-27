@extends('adminlte::page')

@section('title', 'Descuentos')

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
                                {{ __('Descuentos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('descuentos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
									<th >ID Descuentos</th>
									<th >Empleado</th>
									<th >Concepto</th>
									<th >Valor</th>
									<th >Fecha</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($descuentos as $descuento)
                                        <tr>
										<td >{{ $descuento->id_descuentos }}</td>
										<td >{{ $descuento->empleado->nombre }}</td>
										<td >{{ $descuento->concepto }}</td>
										<td >{{ $descuento->valor }}</td>
										<td >{{ \Carbon\Carbon::parse($descuento->d_fecha)->format('m/d/Y') }}</td>

                                            <td>
                                                <form onsubmit="return confirmDelete(this)" action="{{ route('descuentos.destroy', $descuento->id_descuentos) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('descuentos.show', $descuento->id_descuentos) }}"><i class="fa fa-fw fa-eye"></i> </a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('descuentos.edit', $descuento->id_descuentos) }}"><i class="fa fa-fw fa-edit"></i> </a>
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