<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\Core\Database\Database;

class ResultadoPartido extends AbstractModel{
    public $table = "ResultadoPartido";
    public $fields = [
        "id_resultado" => null,
        "partido_id" => null,
        "equipo_ganador_id" => null,
        "equipo_perdedor_id" => null,
        "goles_equipo_ganador" => null,
        "goles_equipo_perdedor" => null,
        "elo_inicial_ganador" => null,
        "elo_final_ganador" => null,
        "elo_inicial_perdedor" => null,
        "elo_final_perdedor" => null,
        
    ];

    public function setIdResultado(int $idResultado){
        $this->fields["id_resultado"] = $idResultado;
    }

    public function setPartidoId(int $partidoId){
        $this->fields["partido_id"] = $partidoId;
    }

    public function setEquipoGanadorId(int $equipoGanadorId){
        $this->fields["equipo_ganador_id"] = $equipoGanadorId;
    }

    public function setEquipoPerdedorId(int $equipoPerdedorId){
        $this->fields["equipo_perdedor_id"] = $equipoPerdedorId;
    }

    public function setGolesEquipoGanador(int $golesEquipoGanador){
        $this->fields["goles_equipo_ganador"] = $golesEquipoGanador;
    }

    public function setGolesEquipoPerdedor(int $golesEquipoPerdedor){
        $this->fields["goles_equipo_perdedor"] = $golesEquipoPerdedor;
    }

    public function setEloInicialGanador(int $eloInicialGanador){
        $this->fields["elo_inicial_ganador"] = $eloInicialGanador;
    }

    public function setEloFinalGanador(int $eloFinalGanador){
        $this->fields["elo_final_ganador"] = $eloFinalGanador;
    }

    public function setEloInicialPerdedor(int $eloInicialPerdedor){
        $this->fields["elo_inicial_perdedor"] = $eloInicialPerdedor;
    }

    public function setEloFinalPerdedor(int $eloFinalPerdedor){
        $this->fields["elo_final_perdedor"] = $eloFinalPerdedor;
    }
    

    public function select(array $params) {
        $queryBuilder = $this->getQueryBuilder();
        $result = $queryBuilder->select($this->table, $params);
        return $result;
    }

    public function saveNewTeam(array $params): ?string{
        $queryBuilder = $this->getQueryBuilder();
        return $queryBuilder->insert($this->table, $params);
    }

    public function selectLike(array $params): array{
        $queryBuilder = $this->getQueryBuilder();
        $result = $queryBuilder->selectLike($this->table, $params);
        return $result;
    }
}

?>
