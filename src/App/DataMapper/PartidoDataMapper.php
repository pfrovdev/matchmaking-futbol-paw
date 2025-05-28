<?php

namespace Paw\App\DataMapper;

use Paw\App\Models\Partido;
use Paw\Core\Database\QueryBuilder;

class PartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb)
    {
        parent::__construct($qb, 'Partido');
    }

    public function map(array $row): Partido
    {
        $p = new Partido();
        $p->set($row);
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
