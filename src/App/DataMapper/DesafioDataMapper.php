<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\Desafio;
use Paw\Core\Database\QueryBuilder;

class DesafioDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'Desafio', $logger);
    }

    public function map(array $row): Desafio
    {
        $d = new Desafio();
        $d->set($row);
        return $d;
    }

    public function mapAll(array $rows): array
    {
        return array_map(fn($r) => $this->map($r), $rows);
    }

    public function findById(array $params): ?Desafio
    {
        $row = parent::findById($params);
        return $row ? $this->map($row) : null;
    }

    // solo trae los desafios donde el equipo tiene desafios con el estado $estadoId
    public function findByEquipoAndEstadoPaginated(int $equipoId, int $estadoId, int $limit, int $offset, string $orderBy, string $direction): array
    {
        $rows = $this->qb->select(
            $this->table,
            ["id_equipo_desafiado" => $equipoId, "id_estado_desafio" => $estadoId],
            $orderBy,
            $direction,
            $limit,
            $offset
        );
        return $this->mapAll($rows);
    }

    public function countByEquipoAndEstado(int $equipoId, int $estadoId): int
    {
        return $this->qb->count(
            $this->table,
            ["id_equipo_desafiado" => $equipoId, "id_estado_desafio" => $estadoId]
        );
    }

    public function findAllByEquipoAndEstado(int $equipoId, int $estadoId)
    {
        $rowsDesafiado = $this->findBy(["id_equipo_desafiado" => $equipoId, "id_estado_desafio" => $estadoId]);
        $rowsDesafiante = $this->findBy(["id_equipo_desafiante" => $equipoId, "id_estado_desafio" => $estadoId]);
        return $this->mapAll(array_merge($rowsDesafiado, $rowsDesafiante));
    }

    public function findByIdPartido(int $idPartido){
        return $this->map($this->findBy(["id_partido" => $idPartido])[0]);
    }

    public function insertDesafio(Desafio $d): int
    {
        $data = [
            'id_equipo_desafiante' => $d->fields['id_equipo_desafiante'],
            'id_equipo_desafiado' => $d->fields['id_equipo_desafiado'],
            'fecha_creacion' => $d->fields['fecha_creacion'],
            'id_estado_desafio' => $d->fields['id_estado_desafio'],
        ];
        return (int) $this->insert($data);
    }

    public function updateDesafio(Desafio $d): bool
    {
        $data = [
            'id_estado_desafio' => $d->fields['id_estado_desafio'],
            'fecha_aceptacion' => $d->fields['fecha_aceptacion'],
            'id_partido' => $d->fields['id_partido'],
        ];
        return (bool) $this->update($data, ['id_desafio' => $d->fields['id_desafio']]);
    }
}
