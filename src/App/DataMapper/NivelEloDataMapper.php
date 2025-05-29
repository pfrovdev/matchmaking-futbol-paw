<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\NivelElo;
use Paw\Core\Database\QueryBuilder;

class NivelEloDataMapper extends DataMapper{

    public function __construct(QueryBuilder $qb, Logger $logger) {
        parent::__construct($qb, 'NivelElo',$logger);
    }

    public function findDescripcionById(int $id): string
    {
        $nivelElo = $this->findById(['id_nivel_elo' => $id]);
        return $nivelElo->getDescripcion();
    }

    public function findById(array $params): NivelElo
    {
        $nivelElo = parent::findById($params);
        if  ($nivelElo) {
            throw new \Exception("No existe el nivel de elo con id $params[id_nivel_elo]");
        }
        return $this->map($nivelElo);
    }

    public function map(array $row): NivelElo
    {
        $nivelElo = new NivelElo();
        $nivelElo->set($row);
        return $nivelElo;
    }

    public function mapAll(array $rows): array
    {
        $nivelElos = [];
        foreach ($rows as $row) {
            $nivelElos[] = $this->map($row);
        }
        return $nivelElos;
    }

}