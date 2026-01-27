@extends('layouts.app')

@section('template_title')
    {{ $nempleado->name ?? __('Show') . " " . __('Nempleado') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Nempleado</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('nempleados.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Nempleado:</strong>
                                    {{ $nempleado->id_nempleado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Pnomina:</strong>
                                    {{ $nempleado->id_pnomina }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Empleado:</strong>
                                    {{ $nempleado->id_empleado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Descuentos:</strong>
                                    {{ $nempleado->total_descuentos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Abonos:</strong>
                                    {{ $nempleado->total_abonos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Prestamos:</strong>
                                    {{ $nempleado->total_prestamos }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Total Pagado:</strong>
                                    {{ $nempleado->total_pagado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
