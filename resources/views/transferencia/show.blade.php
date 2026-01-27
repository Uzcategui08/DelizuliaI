@extends('layouts.app')

@section('template_title')
    {{ $transferencia->name ?? __('Show') . " " . __('Transferencia') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Transferencia</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('transferencias.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="form-group mb-2 mb20">
                            <strong>Id Transferencia:</strong>
                            {{ $transferencia->id_transferencia }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Id Producto:</strong>
                            {{ $transferencia->id_producto }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Id Almacen Origen:</strong>
                            {{ $transferencia->id_almacen_origen }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Id Almacen Destino:</strong>
                            {{ $transferencia->id_almacen_destino }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Cantidad:</strong>
                            {{ $transferencia->cantidad }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>User Id:</strong>
                            {{ $transferencia->user_id }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Observaciones:</strong>
                            {{ $transferencia->observaciones }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
