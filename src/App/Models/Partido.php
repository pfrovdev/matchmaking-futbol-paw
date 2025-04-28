<?php

namespace Paw\App\Models;

use Paw\Core\Database\Database;

class Partido
{
    public int $id_partido;
    public int $desafio_id;
    public int $equipo1_id;
    public int $equipo2_id;
    public string $estado;
    public string $fecha_hora;
    public int $iteraciones;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id_partido = $data['id_partido'] ?? 0;
            $this->desafio_id = $data['desafio_id'] ?? 0;
            $this->equipo1_id = $data['equipo1_id'] ?? 0;
            $this->equipo2_id = $data['equipo2_id'] ?? 0;
            $this->estado = $data['estado'] ?? 'por_disputar';
            $this->fecha_hora = $data['fecha_hora'] ?? '';
            $this->iteraciones = $data['iteraciones'] ?? 0;
        }
    }

    public static function find($id)
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM partido WHERE id_partido = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function save()
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO partido (desafio_id, equipo1_id, equipo2_id, estado, fecha_hora, iteraciones) 
        VALUES (:desafio_id, :equipo1_id, :equipo2_id, :estado, :fecha_hora, :iteraciones)");

        $stmt->execute([
            ':desafio_id' => $this->desafio_id,
            ':equipo1_id' => $this->equipo1_id,
            ':equipo2_id' => $this->equipo2_id,
            ':estado' => $this->estado,
            ':fecha_hora' => $this->fecha_hora,
            ':iteraciones' => $this->iteraciones
        ]);
    }
    
    public function update()
    {
        $stmt = Database::getConnection()->prepare("UPDATE partido SET desafio_id = :desafio_id, equipo1_id = :equipo1_id, equipo2_id = :equipo2_id, 
        estado = :estado, fecha_hora = :fecha_hora, iteraciones = :iteraciones WHERE id_partido = :id");

        $stmt->execute([
            ':id' => $this->id_partido,
            ':desafio_id' => $this->desafio_id,
            ':equipo1_id' => $this->equipo1_id,
            ':equipo2_id' => $this->equipo2_id,
            ':estado' => $this->estado,
            ':fecha_hora' => $this->fecha_hora,
            ':iteraciones' => $this->iteraciones
        ]);
    }

    public function delete()
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM partido WHERE id_partido = :id");
        $stmt->execute([':id' => $this->id_partido]);
    }
}

?>
