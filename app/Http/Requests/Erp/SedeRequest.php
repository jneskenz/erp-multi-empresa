<?php

namespace App\Http\Requests\Erp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SedeRequest extends FormRequest
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
        $sedeId = $this->route('sede') ? $this->route('sede')->id : null;
        Log::info('Validando datos de sede');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:sedes,nombre,' . $sedeId,
            ],
            'codigo' => [
                'required',
                'string',
                'max:10',
                'unique:sedes,codigo,' . $sedeId,
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'estado' => [
                'required',
                'boolean',
            ],
            'empresa_id' => [
                'required',
                'exists:empresas,id',
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
            'nombre.required' => 'El nombre de la sede es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe exceder :max caracteres.',
            'nombre.unique' => 'Ya existe una sede con este nombre para la empresa seleccionada.',
            'codigo.required' => 'El código de la sede es obligatorio.',
            'codigo.max' => 'El código no debe exceder :max caracteres.',
            'codigo.unique' => 'Ya existe una sede con este código.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no debe exceder :max caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
            'empresa_id.required' => 'Debe seleccionar una empresa.',
            'empresa_id.exists' => 'La empresa seleccionada no es válida.',

        ];
    }
}
