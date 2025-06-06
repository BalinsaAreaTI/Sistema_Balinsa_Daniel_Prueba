<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'apellidopat' => 'required',
            'apellidomat' => 'required',
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'sexo' => ['required', 'string', 'max:255'],
            'celular' => ['required', 'string', 'max:255'],
        ];
    }
}
