@extends('adminlte::page')

@section('title','Control de Pagos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h1 class="mb-0">Control de Pagos</h1>
  <div class="d-flex gap-2">
    <a href="{{ route('payment-controls.export', ['q' => request('q'), 'desde' => request('desde'), 'hasta' => request('hasta')]) }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar</a>
    <a href="{{ route('payment-controls.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo</a>
  </div>
</div>
@stop

@section('content')
<section class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
      <div class="card mb-3 shadow-sm border-0">
        <div class="card-body">
          <form class="row g-3 align-items-end" method="GET" action="{{ route('payment-controls.index') }}">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Buscar</label>
              <input type="text" name="q" class="form-control" placeholder="Nombre o descripción" value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Desde</label>
              <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Hasta</label>
              <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-2 d-flex gap-2 justify-content-end">
              <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
              <a class="btn btn-outline-secondary" href="{{ route('payment-controls.index') }}">Limpiar</a>
            </div>
          </form>
          <div class="mt-2 text-muted small d-flex gap-3">
            <span class="badge bg-success">Aprobados {{ $aprobados->count() }}</span>
            <span class="badge bg-secondary">No aprobados {{ $noAprobados->count() }}</span>
            <span class="badge bg-info text-dark">Largo plazo {{ $largoPlazo->count() }}</span>
            @if($usa_semana_default)
              <span class="text-muted">Filtrando semana actual (ajusta fechas para ver más)</span>
            @endif
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header pb-0 border-0 bg-white">
          <ul class="nav nav-pills" id="pc-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="aprobados-tab" data-toggle="pill" href="#aprobados-pane" role="tab">Aprobados ({{ $aprobados->count() }})</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="noaprobados-tab" data-toggle="pill" href="#noaprobados-pane" role="tab">No aprobados ({{ $noAprobados->count() }})</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="largoplazo-tab" data-toggle="pill" href="#largoplazo-pane" role="tab">Largo plazo ({{ $largoPlazo->count() }})</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content" id="pc-tabs-content">
            <div class="tab-pane fade show active" id="aprobados-pane" role="tabpanel">
              <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Nombre</th>
                      <th class="text-end">Monto</th>
                      <th>Fecha</th>
                      <th>Descripción</th>
                      <th class="text-center">Pagado</th>
                      <th class="text-center">Aprobado</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($aprobados as $p)
                      <tr class="{{ $p->pagado ? 'table-success' : '' }}">
                        <td>{{ $p->nombre }}</td>
                        <td class="text-end">${{ number_format($p->monto,2) }}</td>
                        <td>{{ optional($p->fecha)->format('Y-m-d') }}</td>
                        <td>{{ $p->descripcion }}</td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-pagado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $p->pagado ? 'btn-success' : 'btn-outline-secondary' }}">{{ $p->pagado ? 'Pagado' : 'Pendiente' }}</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-aprobado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Aprobado</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <a class="btn btn-sm btn-primary" href="{{ route('payment-controls.edit', $p) }}"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center text-muted">Sin aprobados</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="noaprobados-pane" role="tabpanel">
              <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Nombre</th>
                      <th class="text-end">Monto</th>
                      <th>Fecha</th>
                      <th>Descripción</th>
                      <th class="text-center">Pagado</th>
                      <th class="text-center">Aprobado</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($noAprobados as $p)
                      <tr class="{{ $p->pagado ? 'table-success' : '' }}">
                        <td>{{ $p->nombre }}</td>
                        <td class="text-end">${{ number_format($p->monto,2) }}</td>
                        <td>{{ optional($p->fecha)->format('Y-m-d') }}</td>
                        <td>{{ $p->descripcion }}</td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-pagado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $p->pagado ? 'btn-success' : 'btn-outline-secondary' }}">{{ $p->pagado ? 'Pagado' : 'Pendiente' }}</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-aprobado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Aprobar</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <a class="btn btn-sm btn-primary" href="{{ route('payment-controls.edit', $p) }}"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center text-muted">Sin no aprobados</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane fade" id="largoplazo-pane" role="tabpanel">
              <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Nombre</th>
                      <th class="text-end">Monto</th>
                      <th>Fecha</th>
                      <th>Descripción</th>
                      <th class="text-center">Pagado</th>
                      <th class="text-center">Aprobado</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($largoPlazo as $p)
                      <tr class="{{ $p->pagado ? 'table-success' : '' }}">
                        <td>{{ $p->nombre }}</td>
                        <td class="text-end">${{ number_format($p->monto,2) }}</td>
                        <td>{{ optional($p->fecha)->format('Y-m-d') }}</td>
                        <td>{{ $p->descripcion }}</td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-pagado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $p->pagado ? 'btn-success' : 'btn-outline-secondary' }}">{{ $p->pagado ? 'Pagado' : 'Pendiente' }}</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <form method="POST" action="{{ route('payment-controls.toggle-aprobado', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $p->aprobado ? 'btn-success' : 'btn-outline-secondary' }}">{{ $p->aprobado ? 'Aprobado' : 'Aprobar' }}</button>
                          </form>
                        </td>
                        <td class="text-center">
                          <a class="btn btn-sm btn-primary" href="{{ route('payment-controls.edit', $p) }}"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center text-muted">Sin pagos a largo plazo</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
