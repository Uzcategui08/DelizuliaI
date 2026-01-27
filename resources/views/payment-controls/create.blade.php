@extends('adminlte::page')

@section('title','Nuevo Pago')

@section('content_header')
<h1>Nuevo Pago</h1>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-10 mx-auto">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('payment-controls.store') }}">
            @include('payment-controls.form')
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
