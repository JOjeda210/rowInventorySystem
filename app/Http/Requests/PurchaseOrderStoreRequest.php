<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole(['admin', 'warehouse_manager', 'purchasing']) ?? false;
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
            'required_by' => ['nullable', 'date', 'after:today'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'string', 'exists:products,id'],
            'lines.*.ordered_qty' => ['required', 'numeric', 'min:0.001'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'El proveedor es requerido.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
            'required_by.date' => 'La fecha debe ser valida.',
            'required_by.after' => 'La fecha debe ser posterior a hoy.',
            'lines.required' => 'Debe agregar al menos una linea.',
            'lines.*.product_id.required' => 'El producto es requerido.',
            'lines.*.ordered_qty.required' => 'La cantidad es requerida.',
        ];
    }
}
