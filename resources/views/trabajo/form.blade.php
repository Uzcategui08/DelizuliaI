<div class="row padding-1 p-1">
    <div class="col-md-6">
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre en español') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $trabajo?->nombre) }}" id="nombre" placeholder="Nombre en español">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-2 mb20">
            <label for="nombre_en" class="form-label">{{ __('Nombre en inglés') }}</label>
            <input type="text" name="traducciones[en]" class="form-control @error('traducciones.en') is-invalid @enderror" value="{{ old('traducciones.en', optional($trabajo?->traducciones)['en'] ?? '') }}" id="nombre_en" placeholder="Name in English">
            {!! $errors->first('traducciones.en', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>