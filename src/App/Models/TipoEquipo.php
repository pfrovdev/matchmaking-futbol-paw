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

    public function __get($name){
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }

    public function getTipo(){
        return $this->fields['tipo'];
    }
}
