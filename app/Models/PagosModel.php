<?php

namespace App\Models;

use CodeIgniter\Model;

class PagosModel extends Model
{
    protected $table            = 'Pagos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['lectura_id', 'monto', 'fecha_pago', 'metodo', 'usuario_registro', 'comprobante', 'observaciones', 'estado', 'fecha_registro'];
    protected $useTimestamps    = false;
}
