<?php

namespace App\Http\Requests\Erp;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        
        $empresaId = $this->route('empresa') ? $this->route('empresa')->id : null;
        Log::info('Validando datos de empresa');

        return [
            'numerodocumento' => [
                'required',
                'string',
                'max:20',
                'unique:empresas,numerodocumento,' . $empresaId,
            ],
            'razon_social' => [
                'required',
                'string',
                'max:100',
            ],
            'nombre_comercial' => [
                'nullable',
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
            'correo' => [
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
            'estado' => [
                'required',
                'boolean',
            ],
            'pais_id' => [
                'nullable',
                'exists:paises,id',
            ],
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
            'numerodocumento.required' => 'El número de documento es obligatorio.',
            'numerodocumento.string' => 'El número de documento debe ser una cadena de texto.',
            'numerodocumento.max' => 'El número de documento no debe exceder :max caracteres.',
            'numerodocumento.unique' => 'El número de documento ya está en uso.',
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
        ];
    }

}
