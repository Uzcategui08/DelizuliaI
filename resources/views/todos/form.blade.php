@csrf

<div class="form-group">
    <label for="title">Título *</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $todo->title) }}" required maxlength="255">
    @error('title')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="description">Descripción</label>
    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $todo->description) }}</textarea>
    @error('description')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="due_at">Fecha límite</label>
        <input type="datetime-local" name="due_at" id="due_at" class="form-control"
            value="{{ old('due_at', optional($todo->due_at)->format('Y-m-d\TH:i')) }}">
        @error('due_at')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="reminder_at">Recordar el</label>
        <input type="datetime-local" name="reminder_at" id="reminder_at" class="form-control"
            value="{{ old('reminder_at', optional($todo->reminder_at)->format('Y-m-d\TH:i')) }}">
        @error('reminder_at')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="form-group form-check">
    <input type="checkbox" name="is_completed" id="is_completed" class="form-check-input"
        {{ old('is_completed', $todo->is_completed) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_completed">Marcar como completada</label>
</div>

<div class="form-group d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">
        {{ $submitText ?? 'Guardar' }}
    </button>
</div>
