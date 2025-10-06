{{-- resources/views/empresas/form.blade.php --}}
<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre" class="required">Nombre</label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       class="form-control @error('nombre') is-invalid @enderror" 
                       value="{{ old('nombre', isset($empresa) ? $empresa->nombre : '') }}" 
                       required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="ruc" class="required">RUC</label>
                <input type="text" 
                       id="ruc" 
                       name="ruc" 
                       class="form-control @error('ruc') is-invalid @enderror" 
                       value="{{ old('ruc', isset($empresa) ? $empresa->ruc : '') }}" 
                       maxlength="11" 
                       required>
                @error('ruc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="razon_social" class="required">Razón Social</label>
                <input type="text" 
                       id="razon_social" 
                       name="razon_social" 
                       class="form-control @error('razon_social') is-invalid @enderror" 
                       value="{{ old('razon_social', isset($empresa) ? $empresa->razon_social : '') }}" 
                       required>
                @error('razon_social')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="direccion" class="required">Dirección</label>
                <textarea id="direccion" 
                          name="direccion" 
                          class="form-control @error('direccion') is-invalid @enderror" 
                          rows="3" 
                          required>{{ old('direccion', isset($empresa) ? $empresa->direccion : '') }}</textarea>
                @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" 
                       id="telefono" 
                       name="telefono" 
                       class="form-control @error('telefono') is-invalid @enderror" 
                       value="{{ old('telefono', isset($empresa) ? $empresa->telefono : '') }}">
                @error('telefono')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', isset($empresa) ? $empresa->email : '') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="website">Website</label>
                <input type="url" 
                       id="website" 
                       name="website" 
                       class="form-control @error('website') is-invalid @enderror" 
                       value="{{ old('website', isset($empresa) ? $empresa->website : '') }}" 
                       placeholder="https://ejemplo.com">
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="estado" class="required">Estado</label>
                <select id="estado" 
                        name="estado" 
                        class="form-control @error('estado') is-invalid @enderror" 
                        required>
                    <option value="">Seleccionar estado</option>
                    <option value="activo" {{ old('estado', isset($empresa) ? $empresa->estado : '') === 'activo' ? 'selected' : '' }}>
                        Activo
                    </option>
                    <option value="inactivo" {{ old('estado', isset($empresa) ? $empresa->estado : '') === 'inactivo' ? 'selected' : '' }}>
                        Inactivo
                    </option>
                </select>
                @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($empresa) ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('workspace.empresas.index', ['grupoempresa' => $grupoActual->slug ?? request()->route('grupoempresa')]) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </div>
</div>

<style>
.required::after {
    content: ' *';
    color: red;
}
</style>