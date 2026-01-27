<form method="POST" action="{{ route('almacenes.store') }}">
    @csrf
    <div class="row align-items-end g-3">
        <div class="col-sm-8 col-md-6 col-lg-4">
            <label for="nombre" class="form-label fw-semibold">{{ __('Nombre') }}</label>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control @error('nombre') is-invalid @enderror" 
                value="{{ old('nombre', $almacene?->nombre) }}" 
                placeholder="{{ __('Ingrese el nombre') }}"
                required
            >
            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary px-4">
                {{ __('Guardar') }}
            </button>
        </div>
    </div>
</form>
