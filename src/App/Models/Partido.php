<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class Partido extends AbstractModel
{
    public $table = "Partido";
    public $fields = [
        "id_partido" => null,
        "fecha_creacion" => null,
        "finalizado" => null,
        "fecha_finalizacion" => null,
        "id_estado_partido" => null,
    ];

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

    public function setIdPartido(int $idPartido)
    {
        $this->fields["id_partido"] = $idPartido;
    }

    public function setFechaCreacion(string $fechaCreacion)
    {
        $this->fields["fecha_creacion"] = $fechaCreacion;
    }

    public function setFinalizado(int $finalizado)
    {
        $this->fields["finalizado"] = $finalizado;
    }

    public function setFechaFinalizacion(?string $fechaFinalizacion)
    {
        $this->fields["fecha_finalizacion"] = $fechaFinalizacion;
    }

    public function setIdEstadoPartido(int $idEstadoPartido)
    {
        $this->fields["id_estado_partido"] = $idEstadoPartido;
    }

    public function getIdPartido(): ?int
    {
        return $this->fields["id_partido"];
    }

    public function getFechaInicio()
    {
        return $this->fields["fecha_inicio"];
    }

    public function getFechaFinalizacion(): ?string
    {
        return $this->fields["fecha_finalizacion"];
    }

    public function getIdEstadoPartido()
    {
        return $this->fields["id_estado_partido"];
    }

    public function iniciarPendiente(string $fecha, int $estadoPendiente): void
    {
        $this->setFechaCreacion($fecha);
        $this->setFinalizado(0);
        $this->setFechaFinalizacion(null);
        $this->setIdEstadoPartido($estadoPendiente);
    }

    public function finalizar(string $fechaFinal, int $estadoFinalizado): void
    {
        $this->setFinalizado(1);
        $this->setFechaFinalizacion($fechaFinal);
        $this->setIdEstadoPartido($estadoFinalizado);
    }
}