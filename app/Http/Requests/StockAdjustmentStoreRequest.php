<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole(['admin', 'warehouse_manager', 'warehouse_clerk']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:physical_count,waste,return,correction'],
            'reason' => ['required', 'string', 'min:20'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'string', 'exists:products,id'],
            'lines.*.lot_id' => ['required', 'string', 'exists:lots,id'],
            'lines.*.physical_qty' => ['required', 'numeric', 'min:0'],
            'lines.*.probable_cause' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de ajuste es requerido.',
            'type.in' => 'El tipo de ajuste no es valido.',
            'reason.required' => 'El motivo es requerido.',
            'reason.min' => 'El motivo debe tener al menos 20 caracteres.',
            'lines.required' => 'Debe agregar al menos una linea.',
            'lines.*.product_id.required' => 'El producto es requerido.',
            'lines.*.lot_id.required' => 'El lote es requerido.',
            'lines.*.physical_qty.required' => 'La cantidad fisica es requerida.',
        ];
    }
}
