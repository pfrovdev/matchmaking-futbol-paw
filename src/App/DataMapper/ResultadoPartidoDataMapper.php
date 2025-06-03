<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\ResultadoPartido;
use Paw\Core\Database\QueryBuilder;

class ResultadoPartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $queryBuilder, Logger $logger)
    {
        parent::__construct($queryBuilder, 'ResultadoPartido', $logger);
    }

    public function findByIdPartido(int $idPartido): ?ResultadoPartido
    {
        $result = $this->findBy(['id_partido' => $idPartido]);
        if ($result) {
            return $this->map($result[0]);
        }
        return null;
    }

    public function map(array $row): ResultadoPartido
    {
        $resultadoPartido = new ResultadoPartido();
        $resultadoPartido->set($row);
        return $resultadoPartido;
    }

    public function mapAll(array $rows): array
    {
        $resultadosPartidos = [];
        foreach ($rows as $row) {
            $resultadosPartidos[] = $this->map($row);
        }
        return $resultadosPartidos;
    }

    public function findByIdEquipo(int $idEquipo): array{
        //Ganados
        $partidoGanadosComoLocal = $this->findBy(['id_equipo_local' => $idEquipo, 'resultado' => 'gano_local']);
        $partidoGanadosComoVisitante = $this->findBy(['id_equipo_visitante' => $idEquipo, 'resultado' => 'gano_visitante']);
        //Perdidos
        $partidPerdidoComoLocal = $this->findBy(['id_equipo_local' => $idEquipo, 'resultado' => 'gano_visitante']);
        $partidoPerdidoComoVisitante = $this->findBy(['id_equipo_visitante' => $idEquipo, 'resultado' => 'gano_local']);
        //Empatados
        $partidosEmpatados = $this->findBy(['id_equipo_local' => $idEquipo, 'resultado' => 'empate']);
        $partidosEmpatados = $this->findBy(['id_equipo_visitante' => $idEquipo, 'resultado' => 'empate']);
        $result = [
            'ganados' => count($partidoGanadosComoLocal) + count($partidoGanadosComoVisitante),
            'perdidos'=> count($partidPerdidoComoLocal) + count($partidoPerdidoComoVisitante),
            'empatados' => count($partidosEmpatados) + count($partidosEmpatados),
            'id_equipo' => $idEquipo
        ];
        if ($result) {
            return $result;
        }
        return [];
    }
}