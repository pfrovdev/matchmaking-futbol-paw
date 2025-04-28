<?php

namespace Paw\App\Models;

use Paw\Core\Database\Database;

class Resultado
{
    public int $id_resultado;
    public int $partido_id;
    public int $equipo_id;
    public int $goles_hechos;
    public int $goles_recibidos;
    public int $asistencias;
    public int $tarjetas_amarillas;
    public int $tarjetas_rojas;
    public bool $confirmacion;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id_resultado = $data['id_resultado'] ?? 0;
            $this->partido_id = $data['partido_id'] ?? 0;
            $this->equipo_id = $data['equipo_id'] ?? 0;
            $this->goles_hechos = $data['goles_hechos'] ?? 0;
            $this->goles_recibidos = $data['goles_recibidos'] ?? 0;
            $this->asistencias = $data['asistencias'] ?? 0;
            $this->tarjetas_amarillas = $data['tarjetas_amarillas'] ?? 0;
            $this->tarjetas_rojas = $data['tarjetas_rojas'] ?? 0;
            $this->confirmacion = $data['confirmacion'] ?? false;
        }
    }

    public static function find($id)
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM resultado WHERE id_resultado = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function save()
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO resultado (partido_id, equipo_id, goles_hechos, goles_recibidos, asistencias, 
        tarjetas_amarillas, tarjetas_rojas, confirmacion) VALUES (:partido_id, :equipo_id, :goles_hechos, :goles_recibidos, :asistencias, 
        :tarjetas_amarillas, :tarjetas_rojas, :confirmacion)");

        $stmt->execute([
            ':partido_id' => $this->partido_id,
            ':equipo_id' => $this->equipo_id,
            ':goles_hechos' => $this->goles_hechos,
            ':goles_recibidos' => $this->goles_recibidos,
            ':asistencias' => $this->asistencias,
            ':tarjetas_amarillas' => $this->tarjetas_amarillas,
            ':tarjetas_rojas' => $this->tarjetas_rojas,
            ':confirmacion' => $this->confirmacion
        ]);
    }
    
    public function update()
    {
        $stmt = Database::getConnection()->prepare("UPDATE resultado SET partido_id = :partido_id, equipo_id = :equipo_id, 
        goles_hechos = :goles_hechos, goles_recibidos = :goles_recibidos, asistencias = :asistencias, tarjetas_amarillas = :tarjetas_amarillas, 
        tarjetas_rojas = :tarjetas_rojas, confirmacion = :confirmacion WHERE id_resultado = :id");

        $stmt->execute([
            ':id' => $this->id_resultado,
            ':partido_id' => $this->partido_id,
            ':equipo_id' => $this->equipo_id,
            ':goles_hechos' => $this->goles_hechos,
            ':goles_recibidos' => $this->goles_recibidos,
            ':asistencias' => $this->asistencias,
            ':tarjetas_amarillas' => $this->tarjetas_amarillas,
            ':tarjetas_rojas' => $this->tarjetas_rojas,
            ':confirmacion' => $this->confirmacion
        ]);
    }

    public function delete()
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM resultado WHERE id_resultado = :id");
        $stmt->execute([':id' => $this->id_resultado]);
    }
}

?>
