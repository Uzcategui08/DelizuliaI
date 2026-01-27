@extends('adminlte::page')

@section('title', 'Home')

@section('content_header')
<h2>Panel de Inventario</h2>
<hr>
@stop

@section('content')

<head>
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
</head>


<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $productos }}</h3>
                        <p>Productos en catálogo</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stockTotal }}</h3>
                        <p>Stock total (todas las ubicaciones)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stockBajo }}</h3>
                        <p>Ubicaciones con stock bajo</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-alert"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $sinStock }}</h3>
                        <p>Ubicaciones sin stock</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="card-title mb-0"><i class="fas fa-exchange-alt mr-2"></i>Ajustes de inventario este mes</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Total de ajustes registrados este mes: <strong>{{ $ajustesMes }}</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <style>
        /* Ajustes básicos del panel */
        @media (max-width: 768px) {
            .card-header h3 { font-size: 1rem; }
        }
        </style>
    </div>
</section>

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('/build/assets/admin/admin.css') }}">
@stop

@section('js')
{{-- No hay scripts del dashboard por ahora --}}
@stop