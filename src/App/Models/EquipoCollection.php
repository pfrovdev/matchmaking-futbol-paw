<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;


class EquipoCollection extends AbstractModel{

    public $table = "Equipo";

    public function getAll(): array{
        return $this->getQueryBuilder()->select($this->table);
    }
    
    public function getById(int $id): array{
        return $this->getQueryBuilder()->select($this->table, ["id_equipo" => $id]);
    }

    public function getByEmail(string $email): array{
        return $this->getQueryBuilder()->select($this->table, ["email" => $email]);
    }

    public function createEquipo(array $params){
        $qb = $this->getQueryBuilder();
        return $qb->insert($this->table, $params);
    }

}



?>