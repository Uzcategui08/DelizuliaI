@php
    use Illuminate\Support\Str;

    $isCompleted = $isCompleted ?? false;
    $scheduleBadge = 'badge-secondary';
    $scheduleLabel = $isCompleted ? 'Pagado' : 'Sin programación';

    if (! $isCompleted && $payment->scheduled_for) {
        if ($payment->scheduled_for->isPast()) {
            $scheduleBadge = 'badge-danger';
            $scheduleLabel = 'Vencido ' . $payment->scheduled_for->diffForHumans();
        } elseif ($payment->scheduled_for->isToday()) {
            $scheduleBadge = 'badge-warning';
            $scheduleLabel = 'Para hoy';
        } else {
            $scheduleBadge = 'badge-info';
            $scheduleLabel = 'Programado ' . $payment->scheduled_for->diffForHumans();
        }
    }

    $reminder = $payment->reminder_at
        ? $payment->reminder_at->diffForHumans()
        : 'Sin recordatorio';
@endphp

<div class="list-group-item px-0 py-3" data-payment-id="payment-{{ $payment->id }}">
    <div class="d-flex align-items-start">
        <div class="mr-3">
            <span class="badge badge-pill {{ $isCompleted ? 'badge-success' : $scheduleBadge }}">{{ $scheduleLabel }}</span>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">{{ $payment->payee->alias ?? $payment->payee->name }}</h5>
                <span class="h5 mb-0 {{ $isCompleted ? 'text-success' : 'text-primary' }}">Bs. {{ number_format($payment->amount, 2, ',', '.') }}</span>
            </div>
            @if($payment->description)
                <p class="mb-2 text-muted small">{{ Str::limit($payment->description, 140) }}</p>
            @endif
            <div class="d-flex flex-wrap text-muted small">
                <span class="mr-3">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ $payment->scheduled_for ? $payment->scheduled_for->format('d/m/Y') : 'Sin fecha' }}
                </span>
                <span class="mr-3">
                    <i class="far fa-bell mr-1"></i>
                    {{ $reminder }}
                </span>
                <span>
                    <i class="far fa-clock mr-1"></i>
                    {{ $isCompleted ? 'Pagado ' . optional($payment->paid_at)->diffForHumans() : 'Registrado ' . $payment->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        <div class="ml-3 text-right">
            <form action="{{ route('payments.toggle', $payment) }}" method="POST" class="mb-2">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $isCompleted ? 'btn-outline-secondary' : 'btn-success' }}">
                    <i class="fas {{ $isCompleted ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                    {{ $isCompleted ? 'Marcar pendiente' : 'Marcar pagado' }}
                </button>
            </form>
            @if(! $isCompleted)
                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
            @else
                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit mr-1"></i> Ver/Editar
                </a>
            @endif
        </div>
    </div>
</div>
