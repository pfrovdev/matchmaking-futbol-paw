<?php

namespace Paw\App\Models;

use Paw\Core\Database\Database;

class Desafio
{
    public int $id_desafio;
    public int $equipo1_id;
    public int $equipo2_id;
    public string $estado;
    public string $fecha_creacion;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id_desafio = $data['id_desafio'] ?? 0;
            $this->equipo1_id = $data['equipo1_id'] ?? 0;
            $this->equipo2_id = $data['equipo2_id'] ?? 0;
            $this->estado = $data['estado'] ?? 'pendiente';
            $this->fecha_creacion = $data['fecha_creacion'] ?? '';
        }
    }

    public static function find($id)
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM desafio WHERE id_desafio = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function save()
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO desafio (equipo1_id, equipo2_id, estado, fecha_creacion) 
        VALUES (:equipo1_id, :equipo2_id, :estado, :fecha_creacion)");

        $stmt->execute([
            ':equipo1_id' => $this->equipo1_id,
            ':equipo2_id' => $this->equipo2_id,
            ':estado' => $this->estado,
            ':fecha_creacion' => $this->fecha_creacion
        ]);
    }
    
    public function update()
    {
        $stmt = Database::getConnection()->prepare("UPDATE desafio SET equipo1_id = :equipo1_id, equipo2_id = :equipo2_id, 
        estado = :estado WHERE id_desafio = :id");

        $stmt->execute([
            ':id' => $this->id_desafio,
            ':equipo1_id' => $this->equipo1_id,
            ':equipo2_id' => $this->equipo2_id,
            ':estado' => $this->estado
        ]);
    }

    public function delete()
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM desafio WHERE id_desafio = :id");
        $stmt->execute([':id' => $this->id_desafio]);
    }
}

?>
