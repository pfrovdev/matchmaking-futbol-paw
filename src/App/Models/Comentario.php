<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\App\Models\Equipo;

class Comentario extends AbstractModel{
    public $table = "Comentario";
    public $fields = [
        "id_comentario" => null,
        "id_equipo_comentado" => null,
        "id_equipo_comentador" => null,
        "comentario" => null,
        "deportividad" => null,
        "fecha_creacion" => null,
    ];

    public function setIdComentario(int $idComentario){
        $this->fields["id_comentario"] = $idComentario;
    }

    public function setEquipoComentadoId(int $equipoComentadoId){
        $this->fields["id_equipo_comentado"] = $equipoComentadoId;
    }

    public function setEquipoComentadorId(int $equipoComentadorId){
        $this->fields["id_equipo_comentador"] = $equipoComentadorId;
    }

    public function setComentario(string $comentario){
        $this->fields["comentario"] = $comentario;
    }

    public function setDeportividad(int $deportividad){
        $this->fields["deportividad"] = $deportividad;
    }

    public function setFechaCreacion(string $fechaCreacion){
        $this->fields["fecha_creacion"] = $fechaCreacion;
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

    public function getEquipoComentador(): Equipo{

        $qb = $this->getQueryBuilder(); 
        $equipoComentador = new Equipo();
        $data = $qb->select(
            $equipoComentador->table,
            ['id_equipo' => $this->fields['id_equipo_comentador']]
        );
    
        if (!empty($data)) {
            $equipoComentador->set($data[0]);
        }
    
        return $equipoComentador;
    }    
}

?>