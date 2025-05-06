<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\Core\Database\Database;

class Desafio extends AbstractModel{
    public $table = "Desafio";
    public $fields = [
        "id_desafio" => null,
        "equipo_desafiante_id" => null,
        "equipo_desafiado_id" => null,
        "fecha_creacion" => null,
        "fecha_aceptacion" => null,
        "id_estado_desafio" => null,
        "id_partido" => null,
    ];

    public function setIdDesafio(int $idDesafio){
        $this->fields["id_desafio"] = $idDesafio;
    }

    public function setEquipoDesafianteId(int $equipoDesafianteId){
        $this->fields["equipo_desafiante_id"] = $equipoDesafianteId;
    }

    public function setEquipoDesafiadoId(int $equipoDesafiadoId){
        $this->fields["equipo_desafiado_id"] = $equipoDesafiadoId;
    }

    public function setFechaCreacion(int $fechaCreacion){
        $this->fields["fecha_creacion"] = $fechaCreacion;
    }

    public function setIdEstadoDesafio(int $idEstadoDesafio){
        $this->fields["id_estado_desafio"] = $idEstadoDesafio;
    }

    public function setIdPartido(int $idPartido){
        $this->fields["id_partido"] = $idPartido;
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
