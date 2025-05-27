<?php

namespace Paw\App\DataMapper;

use Paw\App\Models\Desafio;
use Paw\Core\Database\QueryBuilder;

class DesafioDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb)
    {
        parent::__construct($qb, 'Desafio');
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

    public function findById(int $id): ?Desafio
    {
        $row = parent::findById($id);
        return $row ? $this->map($row) : null;
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
