<?php
namespace Paw\App\Services\Impl;
use DateTime;

use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\DataMapper\EstadoDesafioDataMapper;
use Paw\App\DataMapper\TipoEquipoDataMapper;

use Paw\App\Services\EquipoService;
use Paw\App\Services\DesafioService;
use Paw\App\Services\PartidoService;

use Paw\App\Models\Equipo;
use Paw\App\Models\TipoEquipo;

use Paw\Core\Database\QueryBuilder;


class EquipoServiceImpl implements EquipoService {
    private DesafioDataMapper $dm;
    private EstadoDesafioDataMapper $edm;
    private PartidoService $partidoSrv;
    private TipoEquipoDataMapper $tipoEquipoDataMapper;
    private EquipoDataMapper $equipoDataMapper;

    public function __construct(QueryBuilder $qb) {
        $this->tipoEquipoDataMapper = new TipoEquipoDataMapper($qb);
        $this->equipoDataMapper = new EquipoDataMapper($qb);
    }

    public function getAllTiposEquipos(): array {
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

    public function getTypeTeamById(int $typeTeamId){
        $typeTeamById = $this->tipoEquipoDataMapper->findTypeTeamById($typeTeamId);
        $typeTeam = new TipoEquipo();
        $typeTeam->set([
            'id_tipo_equipo'      => $typeTeamById['id_tipo_equipo'],
            'tipo'                => $typeTeamById['tipo'],
            'descripcion_corta'   => $typeTeamById['descripcion_corta'],
        ]);
        return $typeTeam;
    }

    function saveNewTeam(Equipo $equipo){
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
}
