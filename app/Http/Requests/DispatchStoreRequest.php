<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DispatchStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk', 'production']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'string', 'exists:products,id'],
            'lines.*.requested_qty' => ['required', 'numeric', 'min:0.001'],
        ];
    }

    public function messages(): array
    {
        return [
            'lines.required' => 'Debe agregar al menos una linea.',
            'lines.*.product_id.required' => 'El producto es requerido.',
            'lines.*.requested_qty.required' => 'La cantidad solicitada es requerida.',
            'lines.*.requested_qty.min' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
