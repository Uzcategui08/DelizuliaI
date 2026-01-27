@extends('adminlte::page')

@section('title','Editar Pago')

@section('content_header')
<h1>Editar Pago</h1>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-10 mx-auto">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('payment-controls.update', $pago) }}">
            @method('PUT')
            @include('payment-controls.form')
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
