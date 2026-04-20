<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole(['admin', 'warehouse_manager']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('products', 'code')->ignore($this->route('product'))],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'unit_id' => ['required', 'string', 'exists:units_of_measure,id'],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($this->route('product'))],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'gte:min_stock'],
            'location_id' => ['nullable', 'string', 'exists:locations,id'],
            'preferred_supplier_id' => ['nullable', 'string', 'exists:suppliers,id'],
            'shelf_life_days' => ['nullable', 'integer', 'min:1'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El codigo es requerido.',
            'code.unique' => 'Este codigo ya existe.',
            'name.required' => 'El nombre es requerido.',
            'category_id.required' => 'La categoria es requerida.',
            'category_id.exists' => 'La categoria seleccionada no existe.',
            'unit_id.required' => 'La unidad de medida es requerida.',
            'unit_id.exists' => 'La unidad seleccionada no existe.',
            'min_stock.required' => 'El stock minimo es requerido.',
            'min_stock.numeric' => 'El stock minimo debe ser un numero.',
            'max_stock.gte' => 'El stock maximo debe ser mayor o igual al stock minimo.',
        ];
    }
}
