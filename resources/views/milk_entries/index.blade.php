@extends('adminlte::page')

@section('title', 'Leche (litros por día)')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Leche (litros por día)</h1>
        <a href="{{ route('milk-entries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Cargar leche
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Filtro</span>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('milk-entries.index') }}" class="form-inline" style="gap: 0.75rem;">
                            <label for="mode" class="mb-0">Vista</label>
                            <select id="mode" name="mode" class="form-control" onchange="this.form.submit()">
                                <option value="week" @selected(($mode ?? 'week') === 'week')>Semana</option>
                                <option value="year" @selected(($mode ?? 'week') === 'year')>Año</option>
                            </select>

                            <div id="weekFilter" style="display: none;">
                                <label for="week_end" class="mb-0 ml-2">Semana (cierre martes)</label>
                                <select id="week_end" name="week_end" class="form-control" onchange="this.form.submit()">
                                    @foreach($availableWeeks as $weekEnd)
                                        <option value="{{ $weekEnd }}" @selected(($selectedWeekEnd ?? '') === $weekEnd)>
                                            {{ \Carbon\Carbon::parse($weekEnd)->format('d/m/Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="yearFilter" style="display: none;">
                                <label for="year" class="mb-0 ml-2">Año</label>
                                <select id="year" name="year" class="form-control" onchange="this.form.submit()">
                                    @foreach($availableYears as $y)
                                        <option value="{{ $y }}" @selected((int)($selectedYear ?? 0) === (int)$y)>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <a href="{{ route('milk-entries.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sync-alt mr-1"></i> Hoy
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(($mode ?? 'week') === 'year')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Resumen anual {{ (int)($selectedYear ?? 0) }}</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Semana</th>
                                            <th>Cierre (martes)</th>
                                            <th class="text-right">Total litros</th>
                                            <th class="text-right">Total monto</th>
                                            <th class="text-right">Registros</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($yearSummaries as $w)
                                            @php
                                                $weekEnd = \Carbon\Carbon::parse($w->week_end);
                                                $weekStart = $weekEnd->copy()->subWeek();
                                            @endphp
                                            <tr>
                                                <td>{{ $weekStart->format('d/m/Y') }} - {{ $weekEnd->format('d/m/Y') }}</td>
                                                <td>{{ $weekEnd->format('d/m/Y') }}</td>
                                                <td class="text-right">{{ number_format((float)$w->total_liters, 2, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format((float)$w->total_amount, 2, ',', '.') }}</td>
                                                <td class="text-right">{{ (int)$w->entries_count }}</td>
                                                <td class="text-right">
                                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('milk-entries.index', ['mode' => 'week', 'week_end' => $weekEnd->toDateString()]) }}">
                                                        Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No hay registros en este año.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(!empty($yearSummaries) && $yearSummaries->count() > 0)
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-right">Total año</th>
                                                <th class="text-right">{{ number_format((float)($yearTotals->total_liters ?? 0), 2, ',', '.') }}</th>
                                                <th class="text-right">{{ number_format((float)($yearTotals->total_amount ?? 0), 2, ',', '.') }}</th>
                                                <th class="text-right">{{ (int)($yearTotals->entries_count ?? 0) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @php
                $selectedEnd = \Carbon\Carbon::parse($selectedWeekEnd);
                $selectedStart = $selectedEnd->copy()->subWeek();
            @endphp
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Semana {{ $selectedStart->format('d/m/Y') }} - {{ $selectedEnd->format('d/m/Y') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-tint"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Litros (semana)</span>
                                            <span class="info-box-number">{{ number_format((float)($selectedWeekTotals->total_liters ?? 0), 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Monto (semana)</span>
                                            <span class="info-box-number">{{ number_format((float)($selectedWeekTotals->total_amount ?? 0), 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-secondary">
                                        <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Registros (semana)</span>
                                            <span class="info-box-number">{{ (int)($selectedWeekTotals->entries_count ?? 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th class="text-right">Litros</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedWeekEntries as $e)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($e->date)->format('d/m/Y') }}</td>
                                                <td>{{ $e->payee->name ?? $e->payee_name ?? '-' }}</td>
                                                <td class="text-right">{{ number_format((float)$e->liters, 2, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format((float)($e->amount ?? 0), 2, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No hay registros para esta semana.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modeEl = document.getElementById('mode');
        const weekFilter = document.getElementById('weekFilter');
        const yearFilter = document.getElementById('yearFilter');

        const applyMode = () => {
            const mode = modeEl ? modeEl.value : 'week';
            if (weekFilter) {
                weekFilter.style.display = mode === 'week' ? '' : 'none';
            }
            if (yearFilter) {
                yearFilter.style.display = mode === 'year' ? '' : 'none';
            }
        };

        applyMode();
        if (modeEl) {
            modeEl.addEventListener('change', applyMode);
        }
    });
</script>
@endpush
