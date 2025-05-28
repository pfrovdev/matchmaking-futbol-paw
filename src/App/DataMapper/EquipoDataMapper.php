<?php
namespace Paw\App\DataMapper;

use Paw\App\Models\Equipo;
use Paw\Core\Database\QueryBuilder;

class EquipoDataMapper extends DataMapper{
    public function __construct(QueryBuilder $qb){
        parent::__construct($qb, 'Equipo');
    }

    public function map(array $row): object{
        return (object)$row;
    }

    public function mapAll(array $rows): array{
        return $rows;
    }

    public function getAll(): array{
        return $this->findAll();
    }

    public function findByEmail(string $email): int{
        $row = $this->findBy(['email' => $email]);
        if (! $row) {
            throw new \RuntimeException("Tipo '$email' no encontrado");
        }
        return (int)$row['id_equipo'];
    }

    public function findTeamById(int $TeamId){
        return $this->findById(['id_equipo' => $TeamId]);
    }

    public function insertNewTeam(Equipo $equipo){
        $data = [
            "email"           => $equipo->getEmail(),
            "contrasena"      => $equipo->getContrasena(),
            "telefono"        => $equipo->getTelefono(),
            "nombre"          => $equipo->getNombre(),
            "acronimo"        => $equipo->getAcronimo(),
            "id_tipo_equipo"  => $equipo->getIdTipoEquipo(),
            "ubicacion"       => $equipo->getUbicacion(),
            "lema"            => $equipo->getLema(),
            "id_nivel_elo"    => $equipo->getIdNivelElo(),
            "id_rol"          => $equipo->getIdRol(),
            "elo_actual"      => $equipo->getEloActual(),
            "fecha_creacion"  => $equipo->getFechaCreacion(),
            "url_foto_perfil" => $equipo->getUrlFotoPerfil(),
        ];
        return (int) $this->insert($data);
    }
}