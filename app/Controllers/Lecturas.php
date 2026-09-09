<?php

namespace App\Controllers;

use App\Models\LecturasModel;
use App\Models\ContadoresModel;
use App\Models\TarifasModel;
use App\Models\ClientesModel;
use App\Models\TiposServicioModel;

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

    public function nueva(int $contadorId)
    {
        $ultimaLectura   = $this->lecturaModel->obtenerUltimaLectura($contadorId);
        $lecturaAnterior = $ultimaLectura['lectura_actual'] ?? 0;

        // Traemos el contador y su tipo de servicio para mostrarlos en
        // el formulario, así el Lector confirma que está en la casa correcta.
        $contador     = $this->contadorModel->find($contadorId);
        $tipoServicio = $contador
            ? (new TiposServicioModel())->find($contador['tipo_servicio_id'])
            : null;

        return view('lecturas/formulario', [
            'contador_id'      => $contadorId,
            'lectura_anterior' => $lecturaAnterior,
            'contador'         => $contador,
            'tipoServicio'     => $tipoServicio,
        ]);
    }

    public function guardar()
    {
        $contadorId       = (int) $this->request->getPost('contador_id');
        $lecturaActualRaw = $this->request->getPost('lectura_actual');
        $fecha            = date('Y-m-d H:i:s');

        if ($lecturaActualRaw === null || trim((string) $lecturaActualRaw) === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Debes ingresar la lectura actual.');
        }
        $lecturaActual = (float) $lecturaActualRaw;

        $contador = $this->contadorModel->find($contadorId);
        if (! $contador) {
            return redirect()->back()->withInput()
                ->with('error', 'El contador indicado no existe.');
        }

        $ultimaLectura   = $this->lecturaModel->obtenerUltimaLectura($contadorId);
        $lecturaAnterior = $ultimaLectura['lectura_actual'] ?? 0;

        if ($lecturaActual < $lecturaAnterior) {
            return redirect()->back()->withInput()
                ->with('error', 'La lectura actual no puede ser menor a la anterior.');
        }

        $consumo = $lecturaActual - $lecturaAnterior;

        $tarifa = $this->resolverTarifa((int) $contador['tipo_servicio_id'], $fecha);
        if (! $tarifa) {
            return redirect()->back()->withInput()
                ->with('error', 'No hay una tarifa vigente para el tipo de servicio de este contador.');
        }

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
            'periodo'             => date('Y-m-01'),
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
     * Además de la lectura en sí, trae los datos del cliente y del
     * contador (el recibo debe mostrar a quién se le está cobrando,
     * no solo el monto).
     */
    public function recibo(int $id)
    {
        $lectura = $this->lecturaModel->find($id);

        if (! $lectura) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // La lectura solo guarda contador_id — hay que seguir la relación
        // Lectura -> Contador -> Cliente para tener nombre, dirección, etc.
        $contador = $this->contadorModel->find($lectura['contador_id']);
        $cliente  = $contador ? (new ClientesModel())->find($contador['cliente_id']) : null;
        $tipoServicio = $contador
            ? (new TiposServicioModel())->find($contador['tipo_servicio_id'])
            : null;

        return view('lecturas/recibo', [
            'lectura'      => $lectura,
            'contador'     => $contador,
            'cliente'      => $cliente,
            'tipoServicio' => $tipoServicio,
        ]);
    }

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

    private function resolverUsuarioLector(): int
    {
        return (int) session()->get('usuario_id');
    }
}