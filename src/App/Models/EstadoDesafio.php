<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class EstadoDesafio extends AbstractModel
{
    public $table = "EstadoDesafio";
    public $fields = [
        "id_estado_desafio" => null,
        "descripcion" => null,
        "descripcion_corta" => null,
    ];
    
    public function setIdEstadoDesafio(int $idEstadoDesafio){
        $this->fields["id_estado_desafio"] = $idEstadoDesafio;
    }
    public function setDescripcion(string $descripcion){
        $this->fields["descripcion"] = $descripcion;
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

    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
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
