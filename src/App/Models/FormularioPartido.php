<?php

namespace Paw\App\Models;

use Monolog\Logger;
use Paw\Core\AbstractModel;

class FormularioPartido extends AbstractModel
{
    public $table = "FormularioPartido";
    public $fields = [
        "id_formulario" => null,
        "id_equipo" => null,
        "id_partido" => null,
        "fecha" => null,
        "total_faltas" => null,
        "total_goles" => null,
        "total_amarillas" => null,
        "total_rojas" => null,
        "total_asistencias" => null,
        "total_iteraciones" => null,
        "tipo_formulario" => null
    ];

    public function setIdFormulario(int $id)
    {
        $this->fields['id_formulario'] = $id;
    }

    public function setIdEquipo(int $id)
    {
        $this->fields['id_equipo'] = $id;
    }

    public function setIdPartido(int $id)
    {
        $this->fields['id_partido'] = $id;
    }

    public function setFecha(string $fecha)
    {
        $this->fields['fecha'] = $fecha;
    }

    public function setTotalFaltas(int $totalFaltas)
    {
        $this->fields['total_faltas'] = $totalFaltas;
    }

    public function setTotalGoles(int $totalGoles)
    {
        $this->fields['total_goles'] = $totalGoles;
    }

    public function setTotalAmarillas(int $totalAmarillas)
    {
        $this->fields['total_amarillas'] = $totalAmarillas;
    }

    public function setTotalRojas(int $totalRojas)
    {
        $this->fields['total_rojas'] = $totalRojas;
    }

    public function setTotalAsistencias(int $totalAsistencias)
    {
        $this->fields['total_asistencias'] = $totalAsistencias;
    }

    public function setTotalIteraciones(int $totalIteraciones)
    {
        $this->fields['total_iteraciones'] = $totalIteraciones;
    }

    public function setTipoFormulario(string $tipoFormulario)
    {
        $this->fields['tipo_formulario'] = $tipoFormulario;
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

    public function getIdFormulario(): ?int
    {
        return $this->fields['id_formulario'];
    }

    public function getIdEquipo(): ?int
    {
        return $this->fields['id_equipo'];
    }

    public function getIdPartido(): ?int
    {
        return $this->fields['id_partido'];
    }

    public function getFecha(): ?string
    {
        return $this->fields['fecha'];
    }

    public function getTotalFaltas(): ?int
    {
        return $this->fields['total_faltas'];
    }

    public function getTotalGoles(): ?int
    {
        return $this->fields['total_goles'];
    }

    public function getTotalAmarillas(): ?int
    {
        return $this->fields['total_amarillas'];
    }

    public function getTotalRojas(): ?int
    {
        return $this->fields['total_rojas'];
    }

    public function getTotalAsistencias(): ?int
    {
        return $this->fields['total_asistencias'];
    }

    public function getTotalIteraciones(): ?int
    {
        return $this->fields['total_iteraciones'];
    }

    public function getTipoFormulario(): ?string
    {
        return $this->fields['tipo_formulario'];
    }
}
