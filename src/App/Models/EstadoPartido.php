<?php
namespace Paw\App\Models;
use Paw\Core\AbstractModel;
class EstadoPartido extends AbstractModel {
    public $table = "EstadoPartido";
    public $fields = [
        "id_estado_partido" => null,
        "descripcion" => null,
        "descripcion_corta" => null,
    ];

    public function setIdEstadoPartido(int $idEstadoPartido){
        $this->fields["id_estado_partido"] = $idEstadoPartido;
    }
    public function setDescripcion(string $descripcion){
        $this->fields["descripcion"] = $descripcion;
    }

    public function setDescripcionCorta(string $descripcionCorta){
        $this->fields["descripcion_corta"] = $descripcionCorta;
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
