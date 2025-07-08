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

    public function mapAll(array $rows): array
    {
        return $rows;
    }

    public function map(array $row): object
    {
        throw new \BadMethodCallException('Use findByEquipoPaginated y luego mapea en el servicio');
    }
}
