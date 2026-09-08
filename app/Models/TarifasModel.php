<?php

namespace App\Models;

use CodeIgniter\Model;

class TarifasModel extends Model
{
    protected $table            = 'Tarifas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['tipo_servicio_id', 'volumen_incluido_m3', 'tarifa_base', 'tarifa_exceso', 'vigente_desde', 'vigente_hasta', 'activo', 'fecha_creacion'];
    protected $useTimestamps    = false;
}
