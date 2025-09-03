<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class Partido extends AbstractModel
{
    public $table = "Partido";
    public $fields = [
        "id_partido" => null,
        "fecha_creacion" => null,
        "fecha_finalizacion" => null,
        "id_estado_partido" => null,
        "finalizado" => null,
        "finalizado_equipo_desafiante" => null,
        "finalizado_equipo_desafiado"=> null,
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

    public function setFinalizadoEquipoDesafiante(int $finalizadoEquipoDesafiante)
    {
        $this->fields["finalizado_equipo_desafiante"] = $finalizadoEquipoDesafiante;
    }

    public function setFinalizadoEquipoDesafiado(int $finalizadoEquipoDesafiado)
    {
        $this->fields["finalizado_equipo_desafiado"] = $finalizadoEquipoDesafiado;
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

    public function getFechaCreacion(): ?string
    {
        return $this->fields["fecha_creacion"];
    }

    public function getFinalizado(): ?string
    {
        return $this->fields["finalizado"];
    }


    public function getFinalizadoEquipoDesafiante(): ?int
    {
        return $this->fields["finalizado_equipo_desafiante"];
    }

    public function getFinalizadoEquipoDesafiado(): ?int
    {
        return $this->fields["finalizado_equipo_desafiado"];
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

    public function finalizar(string $fechaFinal, int $estadoFinalizado, int $esFinalizado = 0, string $esDesfiadooDesafiante=""): void
    {   
        
        $this->setFinalizado($esFinalizado);
        $this->setFechaFinalizacion($fechaFinal);
        $this->setIdEstadoPartido($estadoFinalizado);
        
        if($esDesfiadooDesafiante == "Desafiado"){
            $this->setFinalizadoEquipoDesafiado(1);
            $this->setFinalizadoEquipoDesafiante($this->getFinalizadoEquipoDesafiante());
        }elseif($esDesfiadooDesafiante == "Desafiante"){
            $this->setFinalizadoEquipoDesafiante(1);
            $this->setFinalizadoEquipoDesafiado($this->getFinalizadoEquipoDesafiado());
        }
    }
}