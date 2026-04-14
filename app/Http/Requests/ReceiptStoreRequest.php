<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReceiptStoreRequest extends FormRequest
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
            'supplier_id' => ['required', 'string', 'exists:suppliers,id'],
            'arrived_at' => ['required', 'date'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'string', 'exists:products,id'],
            'lines.*.received_qty' => ['required', 'numeric', 'min:0.001'],
            'lines.*.lot_number' => ['required', 'string', 'max:50'],
            'lines.*.expiry_date' => ['nullable', 'date'],
            'lines.*.expected_qty' => ['nullable', 'numeric', 'min:0'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'lines.*.location_id' => ['nullable', 'string', 'exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'El proveedor es requerido.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
            'arrived_at.required' => 'La fecha de llegada es requerida.',
            'arrived_at.date' => 'La fecha de llegada debe ser una fecha valida.',
            'lines.required' => 'Debe agregar al menos una linea.',
            'lines.*.product_id.required' => 'El producto es requerido.',
            'lines.*.received_qty.required' => 'La cantidad recibida es requerida.',
            'lines.*.lot_number.required' => 'El numero de lote es requerido.',
        ];
    }
}
