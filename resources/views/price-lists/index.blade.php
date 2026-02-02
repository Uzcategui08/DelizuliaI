@extends('adminlte::page')

@section('title', 'Listas de Precios')

@section('content_header')
  <h1>Listas de Precios</h1>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="card">
        <div class="card-header">
          <span class="card-title">Seleccione una lista</span>
        </div>
        <div class="card-body">
          <div class="row">
            @foreach($lists as $l)
              <div class="col-md-4">
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3>{{ $l->code }}</h3>
                    <p>{{ $l->name }}</p>
                  </div>
                  <div class="icon"><i class="fas fa-tags"></i></div>
                  <a href="{{ route('price-lists.edit', $l) }}" class="small-box-footer">
                    Editar <i class="fas fa-arrow-circle-right"></i>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
@stop
