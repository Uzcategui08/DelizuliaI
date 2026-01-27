@php
    use Illuminate\Support\Str;

    $completed = $completed ?? false;
@endphp

@if($payments->isEmpty())
    <p class="text-muted mb-0">{{ $emptyMessage ?? 'No hay pagos registrados.' }}</p>
@else
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Destinatario</th>
                    <th>Monto</th>
                    <th>Programado para</th>
                    <th>Recordatorio</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payee->alias ?? $payment->payee->name }}</td>
                        <td>Bs. {{ number_format($payment->amount, 2, ',', '.') }}</td>
                        <td>{{ optional($payment->scheduled_for)->format('d/m/Y') ?: '—' }}</td>
                        <td>{{ optional($payment->reminder_at)->format('d/m/Y H:i') ?: '—' }}</td>
                        <td>{{ Str::limit($payment->description, 80) }}</td>
                        <td class="d-flex gap-2">
                            <form action="{{ route('payments.toggle', $payment) }}" method="POST" class="mr-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $completed ? 'btn-outline-secondary' : 'btn-success' }}">
                                    {{ $completed ? 'Marcar pendiente' : 'Marcar pagado' }}
                                </button>
                            </form>
                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-primary mr-1">Editar</a>
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
