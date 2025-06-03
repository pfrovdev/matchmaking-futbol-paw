<?php

namespace Paw\App\Models;

use Paw\Core\AbstractModel;
use Paw\Core\Database\Database;

class ResultadoPartido extends AbstractModel
{
    public $table = "ResultadoPartido";
    public $fields = [
        "id_resultado" => null,
        "id_partido" => null,
        "id_equipo_local" => null,
        "id_equipo_visitante" => null,
        "goles_equipo_local" => null,
        "goles_equipo_visitante" => null,
        "elo_inicial_local" => null,
        "elo_final_local" => null,
        "elo_inicial_visitante" => null,
        "elo_final_visitante" => null,
        "total_amarillas_local" => null,
        "total_amarillas_visitante" => null,
        "total_rojas_local" => null,
        "total_rojas_visitante" => null,
        "resultado" => null,
        "fecha_jugado" => null,
    ];

    public function setIdResultado(int $idResultado){
        $this->fields["id_resultado"] = $idResultado;
    }
    public function setResultado(string $resultado){
        $this->fields["resultado"] = $resultado;
    }

    public function setIdPartido(int $idPartido){
        $this->fields["id_partido"] = $idPartido;
    }

    public function setIdEquipoLocal(int $equipoGanadorId){
        $this->fields["id_equipo_local"] = $equipoGanadorId;
    }

    public function setIdEquipoVisitante(int $equipoPerdedorId){
        $this->fields["id_equipo_visitante"] = $equipoPerdedorId;
    }

    public function setGolesEquipoLocal(int $goles){
        $this->fields["goles_equipo_local"] = $goles;
    }

    public function setGolesEquipoVisitante(int $goles){
        $this->fields["goles_equipo_visitante"] = $goles;
    }

    public function setEloInicialLocal(int $elo){
        $this->fields["elo_inicial_local"] = $elo;
    }

    public function setEloFinalLocal(int $elo){
        $this->fields["elo_final_local"] = $elo;
    }

    public function setEloInicialVisitante(int $elo){
        $this->fields["elo_inicial_visitante"] = $elo;
    }

    public function setEloFinalVisitante(int $elo){
        $this->fields["elo_final_visitante"] = $elo;
    }

    public function setTotalAmarillasLocal(int $amarillas){
        $this->fields["total_amarillas_local"] = $amarillas;
    }

    public function setTotalAmarillasVisitante(int $amarillas){
        $this->fields["total_amarillas_visitante"] = $amarillas;
    }

    public function setTotalRojasLocal(int $rojas){
        $this->fields["total_rojas_local"] = $rojas;
    }

    public function setTotalRojasVisitante(int $rojas){
        $this->fields["total_rojas_visitante"] = $rojas;
    }

    public function setFechaJugado(string $fecha){
        $this->fields["fecha_jugado"] = $fecha;
    }

    public function set(array $values){
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
    public function getIdResultado(): ?int
    {
        return $this->fields['id_resultado'];
    }

    public function getIdPartido(): ?int
    {
        return $this->fields['id_partido'];
    }

    public function getResultado(){
        return $this->fields["resultado"];
    }

    public function getIdEquipoLocal(): ?int
    {
        return $this->fields['id_equipo_local'];
    }

    public function getIdEquipoVisitante(): ?int
    {
        return $this->fields['id_equipo_visitante'];
    }

    public function getGolesEquipoLocal(): ?int
    {
        return $this->fields['goles_equipo_local'];
    }

    public function getGolesEquipoVisitante(): ?int
    {
        return $this->fields['goles_equipo_visitante'];
    }

    public function getEloInicialLocal(): ?int
    {
        return $this->fields['elo_inicial_local'];
    }

    public function getEloFinalLocal(): ?int
    {
        return $this->fields['elo_final_local'];
    }

    public function getEloInicialVisitante(): ?int
    {
        return $this->fields['elo_inicial_visitante'];
    }

    public function getEloFinalVisitante(): ?int
    {
        return $this->fields['elo_final_visitante'];
    }

    public function getTotalAmarillasLocal(): ?int
    {
        return $this->fields['total_amarillas_local'];
    }

    public function getTotalAmarillasVisitante(): ?int
    {
        return $this->fields['total_amarillas_visitante'];
    }

    public function getTotalRojasLocal(): ?int
    {
        return $this->fields['total_rojas_local'];
    }

    public function getTotalRojasVisitante(): ?int
    {
        return $this->fields['total_rojas_visitante'];
    }

    public function getFechaJugado(): ?string
    {
        return $this->fields['fecha_jugado'];
    }

}

?>
