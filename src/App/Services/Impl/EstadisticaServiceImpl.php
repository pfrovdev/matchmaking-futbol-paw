<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\EstadisticaDataMapper;
use Paw\App\DataMapper\HistorialPartidoDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\Models\Estadisticas;
use Paw\App\Services\EstadisticaService;

class EstadisticaServiceImpl implements EstadisticaService
{

    private EstadisticaDataMapper $estadisticaDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;
    private HistorialPartidoDataMapper $historialPartidoDataMapper;

    public function __construct(EstadisticaDataMapper $estadisticaDataMapper, ResultadoPartidoDataMapper $resultadoPartidoDataMapper, HistorialPartidoDataMapper $historialPartidoDataMapper)
    {
        $this->estadisticaDataMapper = $estadisticaDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
        $this->historialPartidoDataMapper = $historialPartidoDataMapper;
    }

    public function findEstadisticasByIdEquipo(int $id_equipo): Array
    {

        $estadistica = $this->estadisticaDataMapper->findIdByIdEquipo($id_equipo);
        $resultadosPartidosEstadisticas = $this->resultadoPartidoDataMapper->getResultadosPartidosEstadisticas($id_equipo);

        $estadisticas = [
            'jugados' => $estadistica->getJugados() ?? 0,
            'goles' => $estadistica->getGoles() ?? 0,
            'goles_en_contra' => $resultadosPartidosEstadisticas['goles_en_contra'] ?? 0,
            'asistencias' => $estadistica->getAsistencias() ?? 0,
            'tarjetas_amarillas' => $estadistica->getTarjetasAmarillas() ?? 0,
            'tarjetas_rojas' => $estadistica->getTarjetasRojas() ?? 0,
            'ganados' => $estadistica->getGanados() ?? 0,
            'empatados' => $estadistica->getEmpatados() ?? 0,
            'perdidos' => $estadistica->getPerdidos() ?? 0,
            'promedio_goles' => $estadistica->getJugados() > 0 ? round($estadistica->getGoles() / $estadistica->getJugados(), 2) : 0,
            'promedio_asistencias' => $estadistica->getJugados() > 0 ? round($estadistica->getAsistencias() / $estadistica->getJugados(), 2) : 0,
            'promedio_amarillas' => $estadistica->getJugados() > 0 ? round($estadistica->getTarjetasAmarillas() / $estadistica->getJugados(), 2) : 0,
            'promedio_goles_en_contra' => $estadistica->getJugados() > 0 ? round(($resultadosPartidosEstadisticas['goles_en_contra'] ?? 0) / $estadistica->getJugados(), 2) : 0,
            'diferencia_gol' => ($estadistica->getGoles() ?? 0) - ($resultadosPartidosEstadisticas['goles_en_contra'] ?? 0),
            'elo_mas_alto' => $resultadosPartidosEstadisticas['elo_mas_alto'] ?? 0,
            'ultimos_5_partidos' => $resultadosPartidosEstadisticas['ultimos_5_partidos'] ?? [],
            'elo_historial' => $this->historialElo($id_equipo),
            'racha_actual' => $this->historialPartidoDataMapper->getRachaActualById($id_equipo),
            'racha_mas_larga' => $this->historialPartidoDataMapper->getRachaMasLargaById($id_equipo),
        ];

        return $estadisticas;
    }

    private function historialElo(int $id_equipo): array
    {
        return $this->historialPartidoDataMapper->getHistorialEloByIdEquipo($id_equipo);
    }
}
