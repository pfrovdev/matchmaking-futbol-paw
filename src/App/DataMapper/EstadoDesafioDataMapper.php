<?php
namespace Paw\App\DataMapper;

use Paw\Core\Database\QueryBuilder;

class EstadoDesafioDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb)
    {
        parent::__construct($qb, 'EstadoDesafio');
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
        $row = $this->findBy(['descripcion_corta' => $code]);
        if (! $row) {
            throw new \RuntimeException("Estado '$code' no encontrado");
        }
        return (int)$row['id_estado_desafio'];
    }
}