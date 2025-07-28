<?php

namespace Paw\App\Dtos;

class ResultadoPartidoDto
{

    public int $id_resultado;
    public int $id_partido;
    public int $id_equipo_local;
    public int $id_equipo_visitante;
    public int $goles_equipo_local;
    public int $goles_equipo_visitante;
    public int $elo_inicial_local;
    public int $elo_final_local;
    public int $elo_inicial_visitante;
    public int $elo_final_visitante;
    public int $total_amarillas_local;
    public int $total_amarillas_visitante;
    public int $total_rojas_local;
    public int $total_rojas_visitante;
    public int $total_asistencias_local;
    public int $total_asistencias_visitante;
    public string $fecha_jugado;
    public string $resultado;



    public function __construct(
        int $id_resultado,
        int $id_partido,
        int $id_equipo_local,
        int $id_equipo_visitante,
        int $goles_equipo_local,
        int $goles_equipo_visitante,
        int $elo_inicial_local,
        int $elo_final_local,
        int $elo_inicial_visitante,
        int $elo_final_visitante,
        int $total_amarillas_local,
        int $total_amarillas_visitante,
        int $total_rojas_local,
        int $total_rojas_visitante,
        int $total_asistencias_local,
        int $total_asistencias_visitante,
        string $fecha_jugado,
        string $resultado
    ) {
        $this->id_resultado = $id_resultado;
        $this->id_partido = $id_partido;
        $this->id_equipo_local = $id_equipo_local;
        $this->id_equipo_visitante = $id_equipo_visitante;
        $this->goles_equipo_local = $goles_equipo_local;
        $this->goles_equipo_visitante = $goles_equipo_visitante;
        $this->elo_inicial_local = $elo_inicial_local;
        $this->elo_final_local = $elo_final_local;
        $this->elo_inicial_visitante = $elo_inicial_visitante;
        $this->elo_final_visitante = $elo_final_visitante;
        $this->total_amarillas_local = $total_amarillas_local;
        $this->total_amarillas_visitante = $total_amarillas_visitante;
        $this->total_rojas_local = $total_rojas_local;
        $this->total_rojas_visitante = $total_rojas_visitante;
        $this->total_asistencias_local = $total_asistencias_local;
        $this->total_asistencias_visitante = $total_asistencias_visitante;
        $this->fecha_jugado = $fecha_jugado;
        $this->resultado = $resultado;
    }

    public function getIdResultado(): int
    {
        return $this->id_resultado;
    }

    public function setIdResultado(int $id_resultado): void
    {
        $this->id_resultado = $id_resultado;
    }

    public function getIdPartido(): int
    {
        return $this->id_partido;
    }

    public function setIdPartido(int $id_partido): void
    {
        $this->id_partido = $id_partido;
    }

    public function getIdEquipoLocal(): int
    {
        return $this->id_equipo_local;
    }

    public function setIdEquipoLocal(int $id_equipo_local): void
    {
        $this->id_equipo_local = $id_equipo_local;
    }

    public function getIdEquipoVisitante(): int
    {
        return $this->id_equipo_visitante;
    }

    public function setIdEquipoVisitante(int $id_equipo_visitante): void
    {
        $this->id_equipo_visitante = $id_equipo_visitante;
    }

    public function getGolesEquipoLocal(): int
    {
        return $this->goles_equipo_local;
    }

    public function setGolesEquipoLocal(int $goles_equipo_local): void
    {
        $this->goles_equipo_local = $goles_equipo_local;
    }

    public function getGolesEquipoVisitante(): int
    {
        return $this->goles_equipo_visitante;
    }

    public function setGolesEquipoVisitante(int $goles_equipo_visitante): void
    {
        $this->goles_equipo_visitante = $goles_equipo_visitante;
    }

    public function getEloInicialLocal(): int
    {
        return $this->elo_inicial_local;
    }

    public function setEloInicialLocal(int $elo_inicial_local): void
    {
        $this->elo_inicial_local = $elo_inicial_local;
    }

    public function getEloFinalLocal(): int
    {
        return $this->elo_final_local;
    }

    public function setEloFinalLocal(int $elo_final_local): void
    {
        $this->elo_final_local = $elo_final_local;
    }

    public function getEloInicialVisitante(): int
    {
        return $this->elo_inicial_visitante;
    }

    public function setEloInicialVisitante(int $elo_inicial_visitante): void
    {
        $this->elo_inicial_visitante = $elo_inicial_visitante;
    }

    public function getEloFinalVisitante(): int
    {
        return $this->elo_final_visitante;
    }

    public function setEloFinalVisitante(int $elo_final_visitante): void
    {
        $this->elo_final_visitante = $elo_final_visitante;
    }

    public function getTotalAmarillasLocal(): int
    {
        return $this->total_amarillas_local;
    }

    public function setTotalAmarillasLocal(int $total_amarillas_local): void
    {
        $this->total_amarillas_local = $total_amarillas_local;
    }

    public function getTotalAmarillasVisitante(): int
    {
        return $this->total_amarillas_visitante;
    }

    public function setTotalAmarillasVisitante(int $total_amarillas_visitante): void
    {
        $this->total_amarillas_visitante = $total_amarillas_visitante;
    }

    public function getTotalRojasLocal(): int
    {
        return $this->total_rojas_local;
    }

    public function setTotalRojasLocal(int $total_rojas_local): void
    {
        $this->total_rojas_local = $total_rojas_local;
    }

    public function getTotalRojasVisitante(): int
    {
        return $this->total_rojas_visitante;
    }

    public function setTotalRojasVisitante(int $total_rojas_visitante): void
    {
        $this->total_rojas_visitante = $total_rojas_visitante;
    }

    public function getTotalAsistenciasLocal(): int
    {
        return $this->total_asistencias_local;
    }

    public function setTotalAsistenciasLocal(int $total_asistencias_local): void
    {
        $this->total_asistencias_local = $total_asistencias_local;
    }

    public function getTotalAsistenciasVisitante(): int
    {
        return $this->total_asistencias_visitante;
    }

    public function setTotalAsistenciasVisitante(int $total_asistencias_visitante): void
    {
        $this->total_asistencias_visitante = $total_asistencias_visitante;
    }

    public function getFechaJugado(): string
    {
        return $this->fecha_jugado;
    }

    public function setFechaJugado(string $fecha_jugado): void
    {
        $this->fecha_jugado = $fecha_jugado;
    }

    public function getResultado(): string
    {
        return $this->resultado;
    }

    public function setResultado(string $resultado): void
    {
        $this->resultado = $resultado;
    }
}
