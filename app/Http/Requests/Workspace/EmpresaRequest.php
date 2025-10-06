<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class EmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Mapea 'numerodocumento' del formulario a 'ruc' de la base de datos
     */
    // protected function prepareForValidation(): void
    // {
    //     if ($this->has('numerodocumento')) {
    //         $this->merge([
    //             'ruc' => $this->input('numerodocumento'),
    //         ]);
    //     }
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        
        $empresaId = $this->route('empresa') ? $this->route('empresa')->id : null;
        
        Log::info('Validando datos de empresa');

        return [
            
            'ruc' => [
                'required',
                'string',
                'max:11',
                'unique:empresas,ruc,' . $empresaId,
            ],
            'nombre_comercial' => [
                'nullable',
                'string',
                'max:100',
            ],
            'razon_social' => [
                'required',
                'string',
                'max:100',
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:200',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'email' => [
                'nullable',
                'string',
                'max:100',
                'email',
            ],
            'avatar' => [
                'nullable',
                'string',
                'max:200',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
            'pais_id' => [
                'nullable',
                'exists:paises,id',
            ],
            // Logo SVG
            'logo' => [
                'nullable',
                'file',
                'mimes:svg',
                'max:2048', // 2MB en kilobytes
            ],
            // Favicon
            'favicon' => [
                'nullable',
                'file',
                'mimes:ico,png,svg',
                'max:1024', // 1MB en kilobytes
            ],
            // Checkboxes de eliminación (solo en update)
            'remove_logo' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ];
        
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ruc.required' => 'El número de documento es obligatorio.',
            'ruc.string' => 'El número de documento debe ser una cadena de texto.',
            'ruc.max' => 'El número de documento no debe exceder :max caracteres.',
            'ruc.unique' => 'El número de documento ya está en uso.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.string' => 'La razón social debe ser una cadena de texto.',
            'razon_social.max' => 'La razón social no debe exceder :max caracteres.',
            'nombre_comercial.string' => 'El nombre comercial debe ser una cadena de texto.',
            'nombre_comercial.max' => 'El nombre comercial no debe exceder :max caracteres.',
            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no debe exceder :max caracteres.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no debe exceder :max caracteres.',
            'correo.string' => 'El correo debe ser una cadena de texto.',
            'correo.max' => 'El correo no debe exceder :max caracteres.',
            'correo.email' => 'El correo debe ser una dirección de correo válida.',
            'avatar.string' => 'El avatar debe ser una cadena de texto.',
            'avatar.max' => 'El avatar no debe exceder :max caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
            'pais_id.exists' => 'El país seleccionado no es válido.',
            // Mensajes para logo y favicon
            'logo.file' => 'El logo debe ser un archivo.',
            'logo.mimes' => 'El logo debe ser un archivo SVG.',
            'logo.max' => 'El logo no debe ser mayor a 2MB.',
            'favicon.file' => 'El favicon debe ser un archivo.',
            'favicon.mimes' => 'El favicon debe ser ICO, PNG o SVG.',
            'favicon.max' => 'El favicon no debe ser mayor a 1MB.',
        ];
    }

}
