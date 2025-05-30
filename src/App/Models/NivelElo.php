<?php
namespace Paw\App\Models;

use Monolog\Logger;
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

    public function setIdNivelElo(int $id)
    {
        $this->fields['id_nivel_elo'] = $id;
    }
    public function setDescripcion(string $descripcion){
        $this->fields["descripcion"] = $descripcion;
    }
    public function setDescripcionCorta(string $descripcionCorta){
        $this->fields["descripcion_corta"] = $descripcionCorta;
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

    public function getIdNivelElo(): ?int
    {
        return $this->fields['id_nivel_elo'];
    }

    public function getDescripcion(): ?string
    {
        return $this->fields['descripcion'];
    }

    public function getDescripcionCorta(): ?string
    {
        return $this->fields['descripcion_corta'];
    }
}
