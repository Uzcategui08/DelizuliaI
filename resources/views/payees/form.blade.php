@csrf

<div class="form-group">
    <label for="name">Nombre *</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $payee->name) }}" required maxlength="255">
    @error('name')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="alias">Alias</label>
    <input type="text" name="alias" id="alias" class="form-control" value="{{ old('alias', $payee->alias) }}" maxlength="255">
    @error('alias')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="contact_info">Información de contacto</label>
    <input type="text" name="contact_info" id="contact_info" class="form-control" value="{{ old('contact_info', $payee->contact_info) }}" maxlength="255">
    @error('contact_info')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="notes">Notas</label>
    <textarea name="notes" id="notes" rows="4" class="form-control">{{ old('notes', $payee->notes) }}</textarea>
    @error('notes')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group d-flex justify-content-end">
    <button type="submit" class="btn btn-primary">
        {{ $submitText ?? 'Guardar' }}
    </button>
</div>
