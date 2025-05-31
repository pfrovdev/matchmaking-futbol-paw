<?php

namespace Paw\App\Services\Impl;

use Monolog\Logger;
use Paw\App\DataMapper\ComentarioDataMapper;
use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\DataMapper\EstadoDesafioDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\TipoEquipoDataMapper;
use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;

use Paw\App\Models\Equipo;
use Paw\App\Models\TipoEquipo;

use Paw\Core\Database\QueryBuilder;

class EquipoServiceImpl implements EquipoService
{
    private TipoEquipoDataMapper $tipoEquipoDataMapper;
    private EquipoDataMapper $equipoDataMapper;
    private ComentarioDataMapper $comentarioDataMapper;
    private NivelEloDataMapper $nivelEloDataMapper;

    public function __construct(TipoEquipoDataMapper $tipoEquipoDataMapper, EquipoDataMapper $equipoDataMapper, ComentarioDataMapper $comentarioDataMapper, NivelEloDataMapper   $nivelEloDataMapper)
    {
        $this->tipoEquipoDataMapper = $tipoEquipoDataMapper;
        $this->equipoDataMapper = $equipoDataMapper;
        $this->comentarioDataMapper = $comentarioDataMapper;
        $this->nivelEloDataMapper = $nivelEloDataMapper;
    }

    public function getAllTiposEquipos(): array
    {
        $typeTeamAll = $this->tipoEquipoDataMapper->getAll();

        $typeTeams = [];

        foreach ($typeTeamAll as $data) {
            $typeTeam = new TipoEquipo();
            $typeTeam->set([
                'id_tipo_equipo'     => $data['id_tipo_equipo'] ?? null,
                'tipo'               => $data['tipo'] ?? null,
                'descripcion_corta'  => $data['descripcion_corta'] ?? null,
            ]);

            $typeTeams[] = $typeTeam;
        }

        return $typeTeams;
    }

    public function getByEmail(string $email): ?Equipo
    {
        return $this->equipoDataMapper->findByEmail($email);
    }

    public function getTypeTeamById(int $typeTeamId)
    {
        $typeTeamById = $this->tipoEquipoDataMapper->findTypeTeamById($typeTeamId);
        $typeTeam = new TipoEquipo();
        $typeTeam->set([
            'id_tipo_equipo'      => $typeTeamById['id_tipo_equipo'],
            'tipo'                => $typeTeamById['tipo'],
            'descripcion_corta'   => $typeTeamById['descripcion_corta'],
        ]);
        return $typeTeam;
    }

    public function getAllEquipos(array $selectParams, string $orderBy = 'id_nivel_elo', string $direction = 'DESC'): array
    {
        $equipos = $this->equipoDataMapper->findAllPaginated($selectParams, $orderBy, $direction);
        return $equipos;
    }

    // devuelve EquipoBannerDto para representar la info de los equipos en las tarjetas del front
    public function getAllEquiposBanner(): array
    {
        $equipos = $this->equipoDataMapper->findAll();

        $equiposBanner = [];

        foreach ($equipos as $equipo) {
            $equipoBanner = $this->getEquipoBanner($equipo);
            $equiposBanner[] = $equipoBanner;
        }

        return $equiposBanner;
    }

    public function getEquipoBanner(Equipo $equipo): EquipoBannerDto
    {
        $descElo = $this->getDescripcionNivelEloById($equipo->getIdNivelElo());
        $deportividad = $this->getDeportividadEquipo($equipo->getIdEquipo());
        $tipoEquipo = $this->tipoEquipoDataMapper->findById(['id_tipo_equipo' => $equipo->getIdTipoEquipo()]);
        $equipoBanner = new EquipoBannerDto($equipo, $descElo, $deportividad, $tipoEquipo->getTipo());
        return $equipoBanner;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->equipoDataMapper->existsByEmail($email);
    }

    function saveNewTeam(Equipo $equipo)
    {
        $team = new Equipo();

        $team->set([
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
        ]);
        $newId = $this->equipoDataMapper->insertNewTeam($team);

        $team->set(['id_equipo' => $newId]);

        return $team;
    }

    function getDeportividadEquipo(int $idEquipo): float
    {
        $comentarios = $this->comentarioDataMapper->findByEquipo($idEquipo);
        $deportividad = array_reduce($comentarios, function ($acc, $comentario) {
            return $acc + $comentario->getDeportividad();
        }, 0);
        return count($comentarios) == 0? 0 : $deportividad / count($comentarios);
    }

    function getEquipoById(int $idEquipo): Equipo
    {
        $equipo = $this->equipoDataMapper->findById(['id_equipo' => $idEquipo]);
        if (!$equipo) {
            throw new \Exception("No existe el equipo con id $idEquipo");
        }
        return $equipo;
    }

    function getDescripcionNivelEloById(int $idEquipo): string
    {
        $equipo = $this->equipoDataMapper->findById(['id_equipo' => $idEquipo]);
        if (!$equipo) {
            throw new \RuntimeException("Equipo $idEquipo no encontrado");
        }
        $nivelElo = $this->nivelEloDataMapper->findById(['id_nivel_elo' => $equipo->getIdNivelElo()]);
        return $nivelElo->getDescripcion();
    }

    function getAllNivelElo(): array{
        $nivelElo = $this->nivelEloDataMapper->findAll();
        return $nivelElo;
    }

}
