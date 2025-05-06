<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class NivelElo extends AbstractModel
{
    public $table = "NivelElo";
    public $fields = [

        "id_nivel_elo" => null,
        "descripcion" => null,
        "descripcion_corta" => null,
    ];

    public function __construct($queryBuilder = null){
        parent::__construct($queryBuilder);
    }

    public function setDescripcion(string $descripcion){
        $this->fields["descripcion"] = $descripcion;
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
