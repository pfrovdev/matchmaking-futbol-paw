<?php
namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\TipoEquipo;
use Paw\Core\Database\QueryBuilder;

use function PHPSTORM_META\map;

class TipoEquipoDataMapper extends DataMapper{
    public function __construct(QueryBuilder $qb, Logger $logger){
        parent::__construct($qb, 'TipoEquipo', $logger);
    }

    public function map(array $row): TipoEquipo{
        $tipoEquipo = new TipoEquipo();
        $tipoEquipo->set($row);
        return $tipoEquipo;
    }

    public function mapAll(array $rows): array{
        $tipoEquipos = [];
        foreach ($rows as $row) {
            $tipoEquipos[] = $this->map($row);
        }
        return $tipoEquipos;
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

    public function findById(array $params): TipoEquipo
    {
        $tipoEquipo = parent::findById($params);
        if (! $tipoEquipo) {
            throw new \RuntimeException("Tipo '$params[0]' no encontrado");
        }
        return $this->map($tipoEquipo);
    }

    public function findTypeTeamById(int $typeTeamId){
        return $this->findById(['id_tipo_equipo' => $typeTeamId]);
    }
}