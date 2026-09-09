<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturasModel extends Model
{
    protected $table            = 'Lecturas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['contador_id', 'lectura_anterior', 'lectura_actual', 'consumo', 'tarifa_id', 'volumen_base_m3', 'consumo_base_m3', 'consumo_exceso_m3', 'tarifa_base_valor', 'tarifa_exceso_valor', 'monto_base', 'monto_exceso', 'monto_total', 'fecha_lectura', 'usuario_lector_id', 'observaciones', 'periodo'];
    protected $useTimestamps    = false;
}
