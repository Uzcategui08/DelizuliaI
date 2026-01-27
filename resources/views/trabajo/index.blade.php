@extends('adminlte::page')

@section('title', 'Trabajos')

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
                                {{ __('Trabajos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('trabajos.create') }}" class="btn btn-secondary btn-m float-right"  data-placement="left">
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
                                    <th>ID</th>
									<th >Nombre</th>
                                    <th>Nombre en Inglés</th>
                                    <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trabajos as $trabajo)
                                        <tr>
										<td >{{ $trabajo->id_trabajo }}</td>
										<td >{{ $trabajo->nombre }}</td>
                                        
                                        <td>{{ $trabajo->getNombreEnIdioma('en') }}</td>
                                        <td>
                                            <form onsubmit="return confirmDelete(this)" action="{{ route('trabajos.destroy', $trabajo->id_trabajo) }}" method="POST" class="delete-form" style="display: flex; flex-direction: row; gap: 5px; justify-content: center;">
                                                <a class="btn btn-sm btn-primary " href="{{ route('trabajos.show', $trabajo->id_trabajo) }}"><i class="fa fa-fw fa-eye"></i></a>
                                                <a class="btn btn-sm btn-success" href="{{ route('trabajos.edit', $trabajo->id_trabajo) }}"><i class="fa fa-fw fa-edit"></i></a>
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
