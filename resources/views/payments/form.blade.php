@csrf

<div class="form-group">
    <label for="payee_id">Destinatario *</label>
    <select name="payee_id" id="payee_id" class="form-control" required>
        <option value="">Seleccione una opción</option>
        @foreach($payees as $id => $name)
            <option value="{{ $id }}" {{ (string) old('payee_id', $payment->payee_id) === (string) $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('payee_id')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="amount">Monto *</label>
    <input type="number" name="amount" id="amount" class="form-control" min="0" step="0.01" value="{{ old('amount', $payment->amount) }}" required>
    @error('amount')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="scheduled_for">Fecha programada</label>
        <input type="date" name="scheduled_for" id="scheduled_for" class="form-control" value="{{ old('scheduled_for', optional($payment->scheduled_for)->format('Y-m-d')) }}">
        @error('scheduled_for')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="reminder_at">Recordatorio</label>
        <input type="datetime-local" name="reminder_at" id="reminder_at" class="form-control" value="{{ old('reminder_at', optional($payment->reminder_at)->format('Y-m-d\TH:i')) }}">
        @error('reminder_at')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="description">Descripción</label>
    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $payment->description) }}</textarea>
    @error('description')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group form-check">
    <input type="checkbox" name="is_paid" id="is_paid" class="form-check-input" {{ old('is_paid', $payment->is_paid) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_paid">Marcar como pagado</label>
</div>

<div class="form-group d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">
        {{ $submitText ?? 'Guardar' }}
    </button>
</div>
