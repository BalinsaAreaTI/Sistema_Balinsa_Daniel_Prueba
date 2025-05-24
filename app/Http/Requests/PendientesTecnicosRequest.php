<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendientesTecnicosRequest extends FormRequest
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
            'cliente_id' =>'required',
            'descripcion' =>'required',
            'responsable' =>'required',
            'hora_prevista' =>'required',
            'hora_llegada' =>'required',
            'hora_limite' =>'required',
            'hora_termino' =>'required',
            'usuario' =>''
        ];
    }
}
