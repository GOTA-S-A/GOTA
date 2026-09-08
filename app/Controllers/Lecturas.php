<?php

namespace App\Controllers;

use App\Models\LecturasModel;
use App\Models\ContadoresModel;
use App\Models\TarifasModel;

class Lecturas extends BaseController
{
    protected LecturasModel $lecturaModel;
    protected ContadoresModel $contadorModel;
    protected TarifasModel $tarifaModel;

    public function __construct()
    {
        $this->lecturaModel  = new LecturasModel();
        $this->contadorModel = new ContadoresModel();
        $this->tarifaModel   = new TarifasModel();
    }

    /**
     * Muestra el formulario para registrar una nueva lectura de un contador.
     * Ruta esperada: GET /lecturas/nueva/{contador_id}
     */
    public function nueva(int $contadorId)
    {
        $ultimaLectura   = $this->lecturaModel->obtenerUltimaLectura($contadorId);
        $lecturaAnterior = $ultimaLectura['lectura_actual'] ?? 0;

        return view('lecturas/formulario', [
            'contador_id'      => $contadorId,
            'lectura_anterior' => $lecturaAnterior,
        ]);
    }

    /**
     * Procesa el formulario: recalcula la lectura anterior desde la BD,
     * busca la tarifa vigente para el tipo de servicio del contador,
     * calcula el desglose base/exceso, y guarda el registro.
     *
     * Ruta esperada: POST /lecturas/guardar
     */
    public function guardar()
    {
        $contadorId       = (int) $this->request->getPost('contador_id');
        $lecturaActualRaw = $this->request->getPost('lectura_actual');
        $fecha            = date('Y-m-d H:i:s'); // fecha_lectura es DATETIME en la BD real

        // --- Validación: lectura_actual debe venir y no estar vacía ---
        if ($lecturaActualRaw === null || trim((string) $lecturaActualRaw) === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Debes ingresar la lectura actual.');
        }
        $lecturaActual = (float) $lecturaActualRaw;

        // --- El contador debe existir: de ahí sacamos su tipo_servicio_id ---
        $contador = $this->contadorModel->find($contadorId);
        if (! $contador) {
            return redirect()->back()->withInput()
                ->with('error', 'El contador indicado no existe.');
        }

        // --- Recalculamos la lectura anterior del lado del servidor ---
        $ultimaLectura   = $this->lecturaModel->obtenerUltimaLectura($contadorId);
        $lecturaAnterior = $ultimaLectura['lectura_actual'] ?? 0;

        if ($lecturaActual < $lecturaAnterior) {
            return redirect()->back()->withInput()
                ->with('error', 'La lectura actual no puede ser menor a la anterior.');
        }

        $consumo = $lecturaActual - $lecturaAnterior;

        // --- Tarifa vigente para el tipo de servicio de este contador ---
        $tarifa = $this->resolverTarifa((int) $contador['tipo_servicio_id'], $fecha);
        if (! $tarifa) {
            return redirect()->back()->withInput()
                ->with('error', 'No hay una tarifa vigente para el tipo de servicio de este contador.');
        }

        // --- Cálculo escalonado: parte dentro del volumen incluido + parte de exceso ---
        $volumenIncluido = (float) $tarifa['volumen_incluido_m3'];
        $consumoBase     = min($consumo, $volumenIncluido);
        $consumoExceso   = max($consumo - $volumenIncluido, 0);
        $tarifaBaseValor   = (float) $tarifa['tarifa_base'];
        $tarifaExcesoValor = (float) $tarifa['tarifa_exceso'];
        $montoBase   = $consumoBase * $tarifaBaseValor;
        $montoExceso = $consumoExceso * $tarifaExcesoValor;

        $usuarioLectorId = $this->resolverUsuarioLector();

        $data = [
            'contador_id'         => $contadorId,
            'lectura_anterior'    => $lecturaAnterior,
            'lectura_actual'      => $lecturaActual,
            // 'consumo' y 'monto_total' NO se incluyen aquí.
            // Son columnas STORED GENERATED en MySQL
            'tarifa_id'           => $tarifa['id'],
            'volumen_base_m3'     => $volumenIncluido,
            'consumo_base_m3'     => $consumoBase,
            'consumo_exceso_m3'   => $consumoExceso,
            'tarifa_base_valor'   => $tarifaBaseValor,
            'tarifa_exceso_valor' => $tarifaExcesoValor,
            'monto_base'          => $montoBase,
            'monto_exceso'        => $montoExceso,
            'fecha_lectura'       => $fecha,
            'usuario_lector_id'   => $usuarioLectorId,
            'periodo'             => date('Y-m-01'), // columna DATE: primer día del mes
        ];

        $lecturaId = $this->lecturaModel->insert($data);

        if (! $lecturaId) {
            return redirect()->back()->withInput()
                ->with('error', 'No se pudo guardar la lectura. Revisa los datos.');
        }

        return redirect()->to('/lecturas/recibo/' . $lecturaId);
    }

    /**
     * Muestra el recibo imprimible de una lectura ya registrada.
     * Ruta esperada: GET /lecturas/recibo/{id}
     */
    public function recibo(int $id)
    {
        // Volvemos a leer de la BD ,
        // porque solo así obtenemos 'consumo' y 'monto_total' ya
        // calculados por MySQL.
        $lectura = $this->lecturaModel->find($id);

        if (! $lectura) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('lecturas/recibo', ['lectura' => $lectura]);
    }

    /**
        * Resuelve la tarifa vigente para un tipo de servicio en una fecha dada.
     */
    private function resolverTarifa(int $tipoServicioId, string $fecha): ?array
    {
        return $this->tarifaModel
            ->where('tipo_servicio_id', $tipoServicioId)
            ->where('activo', 1)
            ->where('vigente_desde <=', $fecha)
            ->groupStart()
                ->where('vigente_hasta >=', $fecha)
                ->orWhere('vigente_hasta IS NULL', null, false)
            ->groupEnd()
            ->orderBy('vigente_desde', 'DESC')
            ->first();
    }

    /**
     * Resuelve el usuario (Lector) que registra la lectura, desde la
     * sesión real de Auth (ya confirmado: la clave es 'usuario_id').
     */
    private function resolverUsuarioLector(): int
    {
        return (int) session()->get('usuario_id');
    }
}