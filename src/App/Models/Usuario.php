<?php

namespace Paw\App\Models;

use Paw\Core\Database\Database;

class Usuario
{
    public int $id_usuario;
    public string $nombre;
    public string $apellido;
    public string $email;
    public string $telefono;
    public int $anio_nacimiento;
    public string $pie_dominante;
    public float $altura;
    public string $rol;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id_usuario = $data['id_usuario'] ?? 0;
            $this->nombre = $data['nombre'] ?? '';
            $this->apellido = $data['apellido'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->telefono = $data['telefono'] ?? '';
            $this->anio_nacimiento = $data['anio_nacimiento'] ?? 0;
            $this->pie_dominante = $data['pie_dominante'] ?? 'left';
            $this->altura = $data['altura'] ?? 0.0;
            $this->rol = $data['rol'] ?? 'jugador';
        }
    }

    public static function find($id)
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM usuario WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
        return $data ? new self($data) : null;
    }

    public function save()
    {
        $stmt = Database::getConnection()->prepare("INSERT INTO usuario (nombre, apellido, email, telefono, anio_nacimiento, pie_dominante, altura, rol) 
        VALUES (:nombre, :apellido, :email, :telefono, :anio_nacimiento, :pie_dominante, :altura, :rol)");

        $stmt->execute([
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':telefono' => $this->telefono,
            ':anio_nacimiento' => $this->anio_nacimiento,
            ':pie_dominante' => $this->pie_dominante,
            ':altura' => $this->altura,
            ':rol' => $this->rol
        ]);
    }
    
    public function update()
    {
        $stmt = Database::getConnection()->prepare("UPDATE usuario SET nombre = :nombre, apellido = :apellido, email = :email, 
        telefono = :telefono, anio_nacimiento = :anio_nacimiento, pie_dominante = :pie_dominante, altura = :altura, rol = :rol WHERE id_usuario = :id");

        $stmt->execute([
            ':id' => $this->id_usuario,
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':telefono' => $this->telefono,
            ':anio_nacimiento' => $this->anio_nacimiento,
            ':pie_dominante' => $this->pie_dominante,
            ':altura' => $this->altura,
            ':rol' => $this->rol
        ]);
    }

    public function delete()
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM usuario WHERE id_usuario = :id");
        $stmt->execute([':id' => $this->id_usuario]);
    }
}

?>
