<?php

namespace App\Http\Requests\Erp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LocalStoreRequest extends FormRequest
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
        return [
            'descripcion' => ['required', 'string', 'max:255'],
            'codigo' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('locales', 'codigo')->whereNull('deleted_at')
            ],
            'direccion' => ['nullable', 'string', 'max:500'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'estado' => ['sometimes', 'boolean'],
            'sede_id' => ['required', 'integer', 'exists:sedes,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'descripcion' => 'descripción',
            'codigo' => 'código',
            'direccion' => 'dirección',
            'correo' => 'correo electrónico',
            'telefono' => 'teléfono',
            'whatsapp' => 'WhatsApp',
            'estado' => 'estado',
            'sede_id' => 'sede',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'codigo.regex' => 'El código solo puede contener letras, números, guiones y guiones bajos.',
            'codigo.unique' => 'Este código ya está siendo utilizado por otro local.',
            'sede_id.exists' => 'La sede seleccionada no existe.',
        ];
    }
}
