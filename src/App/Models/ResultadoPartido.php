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
        "id_equipo_ganador" => null,
        "id_equipo_perdedor" => null,
        "goles_equipo_ganador" => null,
        "goles_equipo_perdedor" => null,
        "elo_inicial_ganador" => null,
        "elo_final_ganador" => null,
        "elo_inicial_perdedor" => null,
        "elo_final_perdedor" => null,
        "total_amarillas_ganador" => null,
        "total_amarillas_perdedor" => null,
        "total_rojas_ganador" => null,
        "total_rojas_perdedor" => null,
        "fecha_jugado" => null,
    ];

    public function setIdResultado(int $idResultado){
        $this->fields["id_resultado"] = $idResultado;
    }

    public function setIdPartido(int $idPartido){
        $this->fields["id_partido"] = $idPartido;
    }

    public function setIdEquipoGanador(int $equipoGanadorId){
        $this->fields["id_equipo_ganador"] = $equipoGanadorId;
    }

    public function setIdEquipoPerdedor(int $equipoPerdedorId){
        $this->fields["id_equipo_perdedor"] = $equipoPerdedorId;
    }

    public function setGolesEquipoGanador(int $goles){
        $this->fields["goles_equipo_ganador"] = $goles;
    }

    public function setGolesEquipoPerdedor(int $goles){
        $this->fields["goles_equipo_perdedor"] = $goles;
    }

    public function setEloInicialGanador(int $elo){
        $this->fields["elo_inicial_ganador"] = $elo;
    }

    public function setEloFinalGanador(int $elo){
        $this->fields["elo_final_ganador"] = $elo;
    }

    public function setEloInicialPerdedor(int $elo){
        $this->fields["elo_inicial_perdedor"] = $elo;
    }

    public function setEloFinalPerdedor(int $elo){
        $this->fields["elo_final_perdedor"] = $elo;
    }

    public function setTotalAmarillasGanador(int $amarillas){
        $this->fields["total_amarillas_ganador"] = $amarillas;
    }

    public function setTotalAmarillasPerdedor(int $amarillas){
        $this->fields["total_amarillas_perdedor"] = $amarillas;
    }

    public function setTotalRojasGanador(int $rojas){
        $this->fields["total_rojas_ganador"] = $rojas;
    }

    public function setTotalRojasPerdedor(int $rojas){
        $this->fields["total_rojas_perdedor"] = $rojas;
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

    public function getIdEquipoGanador(): ?int
    {
        return $this->fields['id_equipo_ganador'];
    }

    public function getIdEquipoPerdedor(): ?int
    {
        return $this->fields['id_equipo_perdedor'];
    }

    public function getGolesEquipoGanador(): ?int
    {
        return $this->fields['goles_equipo_ganador'];
    }

    public function getGolesEquipoPerdedor(): ?int
    {
        return $this->fields['goles_equipo_perdedor'];
    }

    public function getEloInicialGanador(): ?int
    {
        return $this->fields['elo_inicial_ganador'];
    }

    public function getEloFinalGanador(): ?int
    {
        return $this->fields['elo_final_ganador'];
    }

    public function getEloInicialPerdedor(): ?int
    {
        return $this->fields['elo_inicial_perdedor'];
    }

    public function getEloFinalPerdedor(): ?int
    {
        return $this->fields['elo_final_perdedor'];
    }

    public function getTotalAmarillasGanador(): ?int
    {
        return $this->fields['total_amarillas_ganador'];
    }

    public function getTotalAmarillasPerdedor(): ?int
    {
        return $this->fields['total_amarillas_perdedor'];
    }

    public function getTotalRojasGanador(): ?int
    {
        return $this->fields['total_rojas_ganador'];
    }

    public function getTotalRojasPerdedor(): ?int
    {
        return $this->fields['total_rojas_perdedor'];
    }

    public function getFechaJugado(): ?string
    {
        return $this->fields['fecha_jugado'];
    }

}

?>
