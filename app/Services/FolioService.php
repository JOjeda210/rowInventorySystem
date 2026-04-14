<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FolioService
{
    /**
     * Genera un folio unico con formato PREFIJO-AAAA-NNNN
     *
     * Cuenta los registros existentes del modelo que comiencen con el prefijo
     * del anio actual y suma 1 para generar el siguiente numero secuencial.
     *
     * @param  string  $prefix  Prefijo del folio (ej: REC, DIS, ADJ, PO)
     * @param  string  $modelClass  Clase del modelo (nombre completo con namespace)
     * @return string
     */
    public static function generate(string $prefix, string $modelClass): string
    {
        $year = Carbon::now()->year;
        $prefixWithYear = "{$prefix}-{$year}-";
        
        // Contar cuantos registros existen con este prefijo en el anio actual
        $count = $modelClass::where('folio', 'like', "{$prefixWithYear}%")->count();
        
        // El siguiente numero sera count + 1
        $nextNumber = $count + 1;
        
        // Formatear como PREFIJO-AAAA-NNNN (4 digitos rellenados con ceros)
        $folio = sprintf("{$prefixWithYear}%04d", $nextNumber);
        
        return $folio;
    }
}
