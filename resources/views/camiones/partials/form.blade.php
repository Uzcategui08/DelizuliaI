<div class="form-row">
    <div class="form-group col-md-6">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $camion->nombre) }}" required>
    </div>

    <div class="form-group col-md-6">
        <label for="placa">Placa (opcional)</label>
        <input type="text" name="placa" id="placa" class="form-control" value="{{ old('placa', $camion->placa) }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="ultimo_cambio_aceite_km">Km base (último cambio de aceite)</label>
        <input type="number" name="ultimo_cambio_aceite_km" id="ultimo_cambio_aceite_km" class="form-control" value="{{ old('ultimo_cambio_aceite_km', $camion->ultimo_cambio_aceite_km) }}" min="0">
        <small class="text-muted">Esto se usa para calcular los 5.000 km.</small>
    </div>

    <div class="form-group col-md-6">
        <label for="activo">Activo</label>
        <select name="activo" id="activo" class="form-control">
            <option value="1" @selected(old('activo', $camion->activo ?? true))>Sí</option>
            <option value="0" @selected(!old('activo', $camion->activo ?? true))>No</option>
        </select>
    </div>
</div>
