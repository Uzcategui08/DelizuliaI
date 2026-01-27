@extends('adminlte::page')

@section('title', 'Editar Inventario')

@section('content_header')
<h1>Registro</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Inventario</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('inventarios.update', $inventario->id_inventario) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Producto</label>
                            <input type="text" class="form-control" value="{{ $inventario->producto->item }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Almacén</label>
                            <input type="text" class="form-control" value="{{ $inventario->almacene->nombre }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="cantidad">Nueva Cantidad</label>
                            <input type="number" class="form-control" name="cantidad" 
                                   value="{{ $inventario->cantidad }}" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection