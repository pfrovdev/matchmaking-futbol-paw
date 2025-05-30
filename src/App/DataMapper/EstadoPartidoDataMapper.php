<?php
namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\Core\Database\QueryBuilder;

class EstadoPartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'EstadoPartido', $logger);
    }

    public function map(array $row): object
    {
        return (object)$row;
    }

    public function mapAll(array $rows): array
    {
        return $rows;
    }

    public function findIdByCode(string $code): int
    {
        $row = $this->findBy(['descripcion_corta' => $code])[0];
        if (! $row) {
            throw new \RuntimeException("EstadoPartido '$code' no encontrado");
        }
        return (int)$row['id_estado_partido'];
    }
}