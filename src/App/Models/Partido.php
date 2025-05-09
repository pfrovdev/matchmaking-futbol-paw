<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class Partido extends AbstractModel{
    public $table = "Partido";
    public $fields = [
        "id_partido" => null,
        "fecha_creacion" => null,
        "finalizado" => null,
        "fecha_finalizacion" => null,
        "id_estado_partido" => null,
    ];

    public function setIdPartido(int $idPartido){
        $this->fields["id_partido"] = $idPartido;
    }

    public function setFechaCreacion(string $fechaCreacion){
        $this->fields["fecha_creacion"] = $fechaCreacion;
    }

    public function setFinalizado(int $finalizado){
        $this->fields["finalizado"] = $finalizado;
    }

    public function setFechaFinalizacion(string $fechaFinalizacion){
        $this->fields["fecha_finalizacion"] = $fechaFinalizacion;
    }

    public function setIdEstadoPartido(int $idEstadoPartido){
        $this->fields["id_estado_partido"] = $idEstadoPartido;
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

    public function crearPendiente(){

        $qb = $this->getQueryBuilder();
        $estadoPartido = new EstadoPartido($qb);
        $estadosPartido = $qb->select($estadoPartido->table);
        $idPendiente = null;

        foreach ($estadosPartido as $estado) {
            if ($estado['descripcion_corta'] === 'pendiente') {
                $idPendiente = $estado['id_estado_partido'];
                break;
            }
        }

        $qb->insert(
            $this->table,
            [
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_finalizacion' => null,
                'id_estado_partido' => $idPendiente,
                'finalizado' => 0
            ]
        );
    }
}

?>
