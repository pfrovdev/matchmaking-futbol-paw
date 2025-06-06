<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\App\Models\Equipo;

class Comentario extends AbstractModel{
    public $table = "Comentario";
    public $fields = [
        "id_comentario" => null,
        "id_equipo_comentado" => null,
        "id_equipo_comentador" => null,
        "comentario" => null,
        "deportividad" => null,
        "fecha_creacion" => null,
    ];

    public function setIdComentario(int $idComentario){
        $this->fields["id_comentario"] = $idComentario;
    }

    public function setIdEquipoComentado(int $equipoComentadoId){
        $this->fields["id_equipo_comentado"] = $equipoComentadoId;
    }

    public function setIdEquipoComentador(int $equipoComentadorId){
        $this->fields["id_equipo_comentador"] = $equipoComentadorId;
    }

    public function setComentario(string $comentario){
        $this->fields["comentario"] = $comentario;
    }

    public function setDeportividad(float $deportividad){
        $this->fields["deportividad"] = $deportividad;
    }

    public function setFechaCreacion(string $fechaCreacion){
        $this->fields["fecha_creacion"] = $fechaCreacion;
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

    public function getComentarioId(): ?int
    {
        return $this->fields["id_comentario"];
    }

    public function getEquipoComentadoId(): ?int
    {
        return $this->fields["id_equipo_comentado"];
    }
    public function getEquipoComentadorId(): ?int
    {
        return $this->fields["id_equipo_comentador"];
    }
    public function getComentario(): ?string
    {
        return $this->fields["comentario"];
    }
    public function getDeportividad(): ?float
    {
        return $this->fields["deportividad"];
    }
    public function getFechaCreacion(): ?string
    {
        return $this->fields["fecha_creacion"];
    }
}

?>