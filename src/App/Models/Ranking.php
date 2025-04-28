<?php

namespace Paw\App\Models;

use Paw\Core\Database\Database;

class Ranking
{
    public int $id_equipo;
    public int $puntuacion_elo;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id_equipo = $data['id_equipo'] ?? 0;
            $this->puntuacion_elo = $data['puntuacion_elo'] ?? 1000;
        }
    }

    public static function find($id)
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM ranking WHERE id_equipo = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function save()
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO ranking (id_equipo, puntuacion_elo) VALUES (:id_equipo, :puntuacion_elo)");

        $stmt->execute([
            ':id_equipo' => $this->id_equipo,
            ':puntuacion_elo' => $this->puntuacion_elo
        ]);
    }
    
    public function update()
    {
        $stmt = Database::getConnection()->prepare("UPDATE ranking SET puntuacion_elo = :puntuacion_elo WHERE id_equipo = :id");

        $stmt->execute([
            ':id' => $this->id_equipo,
            ':puntuacion_elo' => $this->puntuacion_elo
        ]);
    }

    public function delete()
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM ranking WHERE id_equipo = :id");
        $stmt->execute([':id' => $this->id_equipo]);
    }
}

?>
