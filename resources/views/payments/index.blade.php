@extends('adminlte::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Pagos y obligaciones')

@section('content_header')
<h1>Pagos y obligaciones</h1>
@stop

@section('content')
    @php
        $pendingCount = $pendingPayments->count();
        $completedCount = $completedPayments->count();
        $pendingTotal = $pendingPayments->sum('amount');
        $completedTotal = $completedPayments->sum('amount');
        $overdueCount = $pendingPayments->filter(fn ($payment) => $payment->scheduled_for && $payment->scheduled_for->isPast())->count();
    @endphp

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('payments.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i> Registrar pago
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingCount }}</h3>
                        <p>Pagos pendientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <a href="#payments-pendientes" class="small-box-footer" data-toggle="tab" role="tab">
                        Ver lista <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $overdueCount }}</h3>
                        <p>Pagos vencidos</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="#payments-pendientes" class="small-box-footer" data-toggle="tab" role="tab">
                        Priorizar <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>Bs. {{ number_format($pendingTotal, 2, ',', '.') }}</h3>
                        <p>Monto pendiente</p>
                    </div>
                    <div class="icon"><i class="fas fa-coins"></i></div>
                    <a href="#payments-pendientes" class="small-box-footer" data-toggle="tab" role="tab">
                        Ver detalle <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Bs. {{ number_format($completedTotal, 2, ',', '.') }}</h3>
                        <p>Total pagado</p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt"></i></div>
                    <a href="#payments-realizados" class="small-box-footer" data-toggle="tab" role="tab">
                        Historial <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-outline card-success">
            <div class="card-header border-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Pagos y obligaciones
                        </h3>
                        <span class="text-muted small">Controla tus pagos pendientes y revisa el historial en un solo lugar</span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('payees.index') }}" class="btn btn-outline-secondary">Destinatarios</a>
                        <a href="{{ route('payments.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i> Nuevo pago
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills" id="paymentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="payments-pendientes-tab" data-toggle="tab" href="#payments-pendientes" role="tab" aria-controls="payments-pendientes" aria-selected="true">
                            Pendientes (<span>{{ $pendingCount }}</span>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payments-realizados-tab" data-toggle="tab" href="#payments-realizados" role="tab" aria-controls="payments-realizados" aria-selected="false">
                            Realizados (<span>{{ $completedCount }}</span>)
                        </a>
                    </li>
                </ul>
                <div class="tab-content pt-3" id="paymentTabsContent">
                    <div class="tab-pane fade show active" id="payments-pendientes" role="tabpanel" aria-labelledby="payments-pendientes-tab">
                        <div class="list-group list-group-flush">
                            @forelse($pendingPayments as $payment)
                                @include('payments.partials.item', ['payment' => $payment, 'isCompleted' => false])
                            @empty
                                <div class="list-group-item text-center text-muted py-5">
                                    No hay pagos pendientes. ¡Buen trabajo!
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="payments-realizados" role="tabpanel" aria-labelledby="payments-realizados-tab">
                        <div class="list-group list-group-flush">
                            @forelse($completedPayments as $payment)
                                @include('payments.partials.item', ['payment' => $payment, 'isCompleted' => true])
                            @empty
                                <div class="list-group-item text-center text-muted py-5">
                                    Todavía no registras pagos completados.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
