<?php

namespace Paw\App\Models;

use Exception;
use Paw\Core\AbstractModel;
use Paw\App\Models\Equipo;

class Desafio extends AbstractModel{
    public $table = "Desafio";
    public $fields = [
        "id_desafio" => null,
        "id_equipo_desafiante" => null,
        "id_equipo_desafiado" => null,
        "fecha_creacion" => null,
        "fecha_aceptacion" => null,
        "id_estado_desafio" => null,
        "id_partido" => null,
    ];

    public function setIdDesafio(int $idDesafio){
        $this->fields["id_desafio"] = $idDesafio;
    }

    public function setIdEquipoDesafiante(int $equipoDesafianteId){
        $this->fields["id_equipo_desafiante"] = $equipoDesafianteId;
    }

    public function setIdEquipoDesafiado(int $equipoDesafiadoId){
        $this->fields["id_equipo_desafiado"] = $equipoDesafiadoId;
    }

    public function setFechaCreacion(string $fechaCreacion){
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

    public function set(array $values)
    {
        foreach (array_keys($this->fields) as $field) {
            if (!array_key_exists($field, $values)) {
                continue;
            }
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (method_exists($this, $method)) {
                $this->$method($values[$field]);
            }
        }
    }

    public function getEquipoDesafiante(): Equipo{
        $qb = $this->getQueryBuilder();
        $equipoDesafiante = new Equipo($qb);
        $data = $qb->select(
            $equipoDesafiante->table,
            ['id_equipo' => $this->fields['id_equipo_desafiante']]
        );
        $equipoDesafiante->set($data[0]);
        return $equipoDesafiante;
    }
}

?>
