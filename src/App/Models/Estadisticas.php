<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\Core\Database\Database;

class Estadisticas extends AbstractModel {
    public $table = "Estadisticas";
    public $fields = [
        "id_estaditicas" => null,
        
    ];

    public function setIdEstadisitcas(int $idEstadisticas){
        $this->fields["id_estaditicas"] = $idEstadisticas;
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
