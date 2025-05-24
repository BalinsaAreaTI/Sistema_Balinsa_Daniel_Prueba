<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActaConformidadRequest extends FormRequest
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
            'idActa' =>'required',
            'tipo_acta_conformidad' =>'required',
            'tipo_conformidad' =>'required',
            'ruc_acta' =>'required',
            'razon_social_acta' =>'required',
            'direccion_acta' =>'required',
            'fecha_acta' =>'',
            'fecha_final_acta' =>'',
            'lugar_atencion' =>'required',
            'trabajo_conformidad' =>'required',
            'registro_user' =>'',
            'tecnico' =>'required',
        ];
    }

    public function messages()
    {
        return [
            'idActa.required' => 'El n° de acta es obligatorio.',
            'tipo_acta_conformidad.required' => 'La acta de conformidad es obligatoria',
            'tipo_conformidad.required' => 'El tipo de acta es obligatorio',
            'ruc_acta.required' => 'El ruc es obligatorio.',
            'razon_social_acta.required' => 'La razon social es obligatoria.',
            'direccion_acta.required' => 'La direccion es obligatoria.',
            'lugar_atencion.required' => 'El lugar de atencion es obligatorio.',
            'trabajo_conformidad.required' => 'El tipo de servicio es obligatorio.',
            'tecnico.required' => 'El nombre del tecnico es obligatorio.',
        ];
    }
}
