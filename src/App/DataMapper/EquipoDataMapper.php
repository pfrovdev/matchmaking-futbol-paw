<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\Equipo;
use Paw\Core\Database\QueryBuilder;
use Paw\Core\Traits\Loggeable;

class EquipoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'Equipo', $logger);
    }

    public function map(array $row): Equipo
    {
        $equipo = new Equipo();
        $equipo->set($row);
        return $equipo;
    }

    public function mapAll(array $rows): array
    {
        $equipos = [];
        foreach ($rows as $row) {
            $equipo = $this->map($row);
            $equipos[] = $equipo;
        }
        return $equipos;
    }

    public function findById(array $params): ?Equipo
    {
        $row = parent::findById($params);
        return $row ? $this->map($row) : null;
    }

    public function findAll(?array $selectParams = [], ?string $orderBy = null, ?string $direction = null): array
    {
        $rows = parent::findAll($selectParams, $orderBy, $direction);
        return $this->mapAll($rows);
    }

    public function findByEmail(string $email): ?Equipo
    {
        if($this->existsByEmail($email)){
            return $this->map($this->findBy(['email' => $email])[0]);
        }
        return null;
    }

    public function existsByEmail(string $email): bool
    {
        return ! empty($this->findBy(['email' => $email]));
    }

    public function findAllPaginated(array $selectParams, string $orderBy = 'id_nivel_elo', string $direction = 'DESC'): array
    {
        $conditions = [];
        $rawConditions = [];

        if (!empty($selectParams['nombre'])) {
            $conditions['nombre'] = ['operator' => 'LIKE', 'value' => '%' . $selectParams['nombre'] . '%'];
        }

        if (!empty($selectParams['id_nivel_elo'])) {
            $conditions['id_nivel_elo'] = $selectParams['id_nivel_elo'];
        }

        if (!empty($selectParams['miEquipo']->id_equipo)) {
            $conditions['id_equipo'] = ['operator' => '!=', 'value' => $selectParams['miEquipo']->id_equipo];
        }

        if (isset($selectParams['lat'], $selectParams['lng'], $selectParams['radio_km'])) {
            $lat = $selectParams['lat'];
            $lng = $selectParams['lng'];
            $radio_m = $selectParams['radio_km'] * 1000;

            $rawConditions[] = [
                'sql' => "ST_Distance_Sphere(ubicacion, ST_GeomFromText(?, 4326)) <= ?",
                'params' => ["POINT($lng $lat)", $radio_m]
            ];
        }

        $equipos = $this->selectAdvanced(
            $this->table,
            $conditions,
            $rawConditions,
            $orderBy,
            $direction
        );

        return $this->mapAll($equipos);
    }

    public function findTeamById(int $TeamId)
    {
        return $this->findById(['id_equipo' => $TeamId]);
    }

    public function insertNewTeam(Equipo $equipo)
    {
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
