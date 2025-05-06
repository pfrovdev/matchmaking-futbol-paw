<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class TipoEquipo extends AbstractModel{
    public $table = "TipoEquipo";
    public $fields = [
        "id_tipo_equipo" => null,
        "tipo" => null,
        "descripcion_corta" => null,
    ];
    public function setIdTipoEquipo(int $idTipoEquipo){
        $this->fields["id_tipo_equipo"] = $idTipoEquipo;
    }
    public function setTipo(string $tipo){
        $this->fields["tipo"] = $tipo;
    }

    public function setDescripcionCorta(string $descripcionCorta){
        $this->fields["descripcion_corta"] = $descripcionCorta;
    }

    public function all(){
        return $this->getQueryBuilder()->select($this->table);
    }

    public function find(array $params){
        return $this->getQueryBuilder()->select($this->table, $params);
    }

    public function __get($name){
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }
}
