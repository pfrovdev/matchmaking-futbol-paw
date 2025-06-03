<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\Partido;
use Paw\Core\Database\QueryBuilder;

class PartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'Partido', $logger);
    }

    public function map(array $row): Partido
    {
        $p = new Partido();
        $p->set($row);
        $this->logger->info("partido " .$p->getIdPartido());
        return $p;
    }

    public function mapAll(array $rows): array
    {
        return array_map(fn($r) => $this->map($r), $rows);
    }

    public function findById(array $params): ?Partido
    {
        $row = parent::findById($params);
        return $row ? $this->map($row) : null;
    }
    public function getAll(array $params): array{
        $rows = $this->findAll($params);
        return $this->mapAll($rows);
    }

    public function findByIdAndFinalizado(int $idPartido, bool $finalizado): ?Partido
    {
        $row = $this->findBy(['id_partido' => $idPartido, 'finalizado' => $finalizado? 1 : 0]);
        return $row ? $this->map($row[0]) : null;
    }

    public function findAllByEquipoAndFinalizado(int $idEquipo, int $finalizado)
    {
        return $this->mapAll($this->findBy(['id_equipo' => $idEquipo, 'finalizado' => $finalizado]));
    }

    public function insertPartido(Partido $p): int
    {
        $data = [
            'fecha_creacion' => $p->fields['fecha_creacion'],
            'finalizado' => $p->fields['finalizado'],
            'fecha_finalizacion' => $p->fields['fecha_finalizacion'],
            'id_estado_partido' => $p->fields['id_estado_partido'],
        ];
        return (int)$this->insert($data);
    }

    public function updatePartido(Partido $p): bool
    {
        $data = [
            'finalizado' => $p->fields['finalizado'],
            'fecha_finalizacion' => $p->fields['fecha_finalizacion'],
            'id_estado_partido' => $p->fields['id_estado_partido'],
        ];
        return (bool)$this->update($data, ['id_partido' => $p->fields['id_partido']]);
    }
}
