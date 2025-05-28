<?php
namespace Paw\App\DataMapper;

use Paw\Core\Database\QueryBuilder;

class TipoEquipoDataMapper extends DataMapper{
    public function __construct(QueryBuilder $qb){
        parent::__construct($qb, 'TipoEquipo');
    }

    public function map(array $row): object{
        return (object)$row;
    }

    public function mapAll(array $rows): array{
        return $rows;
    }

    public function getAll(): array{
        return $this->findAll();
    }

    public function findIdByCode(string $code): int{
        $row = $this->findBy(['descripcion_corta' => $code]);
        if (! $row) {
            throw new \RuntimeException("Tipo '$code' no encontrado");
        }
        return (int)$row['id_tipo_equipo'];
    }

    public function findTypeTeamById(int $typeTeamId){
        return $this->findById(['id_tipo_equipo' => $typeTeamId]);
    }
}