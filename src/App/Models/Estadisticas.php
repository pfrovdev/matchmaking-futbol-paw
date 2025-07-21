<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\Core\Database\Database;

class Estadisticas extends AbstractModel
{
    public $table = "Estadisticas";
    public $fields = [
        "id_estadistica" => null,
        "id_equipo" => null,
        "goles" => null,
        "asistencias" => null,
        "tarjetas_rojas" => null,
        "tarjetas_amarillas" => null,
        "jugados" => null,
        "ganados" => null,
        "empatados" => null,
        "perdidos" => null,
    ];

    public function set(array $values)
    {
        foreach (array_keys($this->fields) as $field) {
            if (!array_key_exists($field, $values)) {
                continue;
            }
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (method_exists($this, $method) && $values[$field] !== null) {
                $this->$method($values[$field]);
            }
        }
    }

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __toString(): string
    {
        $output = [];
        foreach ($this->fields as $k => $v) {
            if ($v !== null) {
                $output[] = "$k: $v";
            }
        }
        return implode(", ", $output);
    }

    public function getIdEstadistica()
    {
        return $this->fields['id_estadistica'];
    }

    public function setIdEstadistica($value)
    {
        $this->fields['id_estadistica'] = $value;
        return $this;
    }

    public function getIdEquipo()
    {
        return $this->fields['id_equipo'];
    }

    public function setIdEquipo($value)
    {
        $this->fields['id_equipo'] = $value;
        return $this;
    }

    public function getGoles()
    {
        return $this->fields['goles'];
    }

    public function setGoles($value)
    {
        $this->fields['goles'] = $value;
        return $this;
    }

    public function getAsistencias()
    {
        return $this->fields['asistencias'];
    }

    public function setAsistencias($value)
    {
        $this->fields['asistencias'] = $value;
        return $this;
    }

    public function getTarjetasRojas()
    {
        return $this->fields['tarjetas_rojas'];
    }

    public function setTarjetasRojas($value)
    {
        $this->fields['tarjetas_rojas'] = $value;
        return $this;
    }

    public function getTarjetasAmarillas()
    {
        return $this->fields['tarjetas_amarillas'];
    }

    public function setTarjetasAmarillas($value)
    {
        $this->fields['tarjetas_amarillas'] = $value;
        return $this;
    }

    public function getJugados()
    {
        return $this->fields['jugados'];
    }

    public function setJugados($value)
    {
        $this->fields['jugados'] = $value;
        return $this;
    }

    public function getGanados()
    {
        return $this->fields['ganados'];
    }

    public function setGanados($value)
    {
        $this->fields['ganados'] = $value;
        return $this;
    }

    public function getEmpatados()
    {
        return $this->fields['empatados'];
    }

    public function setEmpatados($value)
    {
        $this->fields['empatados'] = $value;
        return $this;
    }

    public function getPerdidos()
    {
        return $this->fields['perdidos'];
    }

    public function setPerdidos($value)
    {
        $this->fields['perdidos'] = $value;
        return $this;
    }
}
