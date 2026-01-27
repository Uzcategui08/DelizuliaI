@extends('adminlte::page')

@section('title', 'Detalle Lote')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">Lote: {{ $lote->nombre }}</h1>
            <small class="text-muted">Fecha inicio: {{ optional($lote->fecha_inicio)->format('Y-m-d') ?? 'N/A' }}</small>
        </div>
        <div class="d-flex" style="gap: 8px;">
            <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Día 1 - Cantidades iniciales</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-right">Cantidad inicial</th>
                        <th class="text-right">Kg/unidad</th>
                        <th class="text-right">Kg inicial</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalKgInicial = 0; @endphp
                    @foreach($productos as $p)
                        @php
                            $kpu = (float)($p['kilos_por_unidad'] ?? 1);
                            $kgInicial = ((float)$p['cantidad_inicial']) * $kpu;
                            $totalKgInicial += $kgInicial;
                        @endphp
                        <tr>
                            <td>{{ $p['item'] }} @if($p['marca'])<span class="text-muted">({{ $p['marca'] }})</span>@endif</td>
                            <td class="text-right">{{ $p['cantidad_inicial'] }}</td>
                            <td class="text-right">{{ number_format($kpu, 3) }}</td>
                            <td class="text-right">{{ number_format($kgInicial, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Kg inicial</th>
                        <th class="text-right">{{ number_format($totalKgInicial, 3) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if($lote->dias->count() === 0)
        <div class="alert alert-info">
            Este lote no tiene días configurados para merma.
        </div>
    @else
        <form action="{{ route('lotes.mermas.update', $lote) }}" method="POST">
            @csrf

            @foreach($lote->dias->sortBy('dia_numero') as $dia)
                @php
                    $totalKgDia = 0;
                    foreach ($productos as $p) {
                        $pidTmp = $p['id_producto'];
                        $kgTmp = (float)($mermas[$dia->dia_numero][$pidTmp] ?? 0);
                        $totalKgDia += $kgTmp;
                    }
                @endphp
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Día {{ $dia->dia_numero }} - Merma por producto</strong>
                            <span class="badge badge-info day-total-kg" data-day="{{ $dia->dia_numero }}">
                                Total: {{ number_format($totalKgDia, 3) }} kg
                            </span>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th style="width: 260px" class="text-right">Merma (kg)</th>
                                    <th class="text-right">Merma (cant)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $p)
                                    @php
                                        $pid = $p['id_producto'];
                                        $kpu = (float)($p['kilos_por_unidad'] ?? 1);
                                        $kgVal = (float)($mermas[$dia->dia_numero][$pid] ?? 0);
                                        $cantDerivada = $kpu > 0 ? ($kgVal / $kpu) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $p['item'] }} @if($p['marca'])<span class="text-muted">({{ $p['marca'] }})</span>@endif</td>
                                        <td>
                                            <div class="input-group justify-content-end">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary btn-dec">-</button>
                                                </div>
                                                <input type="number"
                                                       class="form-control text-right merma-input"
                                                       name="mermas[{{ $dia->dia_numero }}][{{ $pid }}]"
                                                       value="{{ number_format($kgVal, 3, '.', '') }}"
                                                       min="0" step="0.001"
                                                       data-day="{{ $dia->dia_numero }}"
                                                       data-kpu="{{ number_format($kpu, 3, '.', '') }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-inc">+</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="text-muted" style="font-size: 12px;">{{ number_format($kpu, 3) }} kg/u</div>
                                            <span class="badge badge-light">{{ number_format($cantDerivada, 2) }} u</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-end mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar mermas
                </button>
            </div>
        </form>
    @endif
@stop

@section('js')
<script>
    function recomputeDayTotal(dayNumero) {
        const inputs = document.querySelectorAll(`input.merma-input[data-day="${dayNumero}"]`);
        let totalKg = 0;
        inputs.forEach(input => {
            const kg = parseFloat(input.value || '0');
            const kpu = parseFloat(input.dataset.kpu || '1');
            if (!Number.isFinite(kg) || !Number.isFinite(kpu)) {
                return;
            }
            totalKg += kg;
        });

        const badge = document.querySelector(`.day-total-kg[data-day="${dayNumero}"]`);
        if (badge) {
            badge.textContent = `Total: ${totalKg.toFixed(3)} kg`;
        }
    }

    document.querySelectorAll('input.merma-input').forEach(input => {
        input.addEventListener('input', () => {
            recomputeDayTotal(input.dataset.day);
        });
    });

    document.querySelectorAll('.btn-dec').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group')?.querySelector('input.merma-input');
            if (!input) {
                return;
            }
            input.value = Math.max(0, (parseFloat(input.value || '0') - 0.1)).toFixed(3);
            recomputeDayTotal(input.dataset.day);
        });
    });
    document.querySelectorAll('.btn-inc').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group')?.querySelector('input.merma-input');
            if (!input) {
                return;
            }
            input.value = (parseFloat(input.value || '0') + 0.1).toFixed(3);
            recomputeDayTotal(input.dataset.day);
        });
    });
</script>
@stop
