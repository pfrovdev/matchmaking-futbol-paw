<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\Core\Database\QueryBuilder;

class HistorialPartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'v_historial_partidos', $logger);
    }

    public function findByEquipoPaginated(
        int $idEquipo,
        int $limit,
        int $offset,
        string $orderBy,
        string $direction
    ): array {
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        return $this->findByOrPaginated(
            ['id_equipo_local', 'id_equipo_visitante'],
            $idEquipo,
            $limit,
            $offset,
            $orderBy,
            $direction
        );
    }

    public function countByEquipo(int $idEquipo): int
    {
        return $this->countByOr(
            ['id_equipo_local', 'id_equipo_visitante'],
            $idEquipo
        );
    }

    public function getHistorialEloByIdEquipo(int $id_equipo): array
    {
        $local = $this->selectAdvanced(
            $this->table,
            ['id_equipo_local' => $id_equipo],
            [],
            'fecha_jugado',
            'ASC'
        );

        $visitante = $this->selectAdvanced(
            $this->table,
            ['id_equipo_visitante' => $id_equipo],
            [],
            'fecha_jugado',
            'ASC'
        );

        $historial = [];

        foreach ($local as $partido) {
            $historial[] = [
                'id_partido' => $partido['id_partido'],
                'fecha' => $partido['fecha_jugado'],
                'elo' => $partido['elo_final_local']
            ];
        }

        foreach ($visitante as $partido) {
            $historial[] = [
                'id_partido' => $partido['id_partido'],
                'fecha' => $partido['fecha_jugado'],
                'elo' => $partido['elo_final_visitante']
            ];
        }

        usort($historial, fn($a, $b) => strcmp($a['fecha'], $b['fecha']));

        return $historial;
    }

    public function getRachaMasLargaById(int $id_equipo): int
    {
        $partidos = $this->selectAdvanced(
            'v_historial_partidos',
            [],
            [
                [
                    'sql' => '(id_equipo_local = ? OR id_equipo_visitante = ?)',
                    'params' => [$id_equipo, $id_equipo]
                ]
            ],
            'fecha_jugado',
            'ASC'
        );

        $rachaActual = 0;
        $rachaMax = 0;

        foreach ($partidos as $partido) {
            if ((int) $partido['id_equipo_ganador'] === $id_equipo) {
                $rachaActual++;
                $rachaMax = max($rachaMax, $rachaActual);
            } else {
                $rachaActual = 0;
            }
        }

        return $rachaMax;
    }

    public function getRachaActualById(int $id_equipo): int
    {
        $partidos = $this->selectAdvanced(
            'v_historial_partidos',
            [],
            [
                [
                    'sql' => '(id_equipo_local = ? OR id_equipo_visitante = ?)',
                    'params' => [$id_equipo, $id_equipo]
                ]
            ],
            'fecha_jugado',
            'DESC'
        );

        $rachaActual = 0;

        foreach ($partidos as $partido) {
            if ((int) $partido['id_equipo_ganador'] === $id_equipo) {
                $rachaActual++;
            } else {
                break;
            }
        }

        return $rachaActual;
    }

    public function mapAll(array $rows): array
    {
        return $rows;
    }

    public function map(array $row): object
    {
        throw new \BadMethodCallException('Use findByEquipoPaginated y luego mapea en el servicio');
    }
}
