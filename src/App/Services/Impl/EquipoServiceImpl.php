<?php

namespace Paw\App\Services\Impl;

use Paw\App\DataMapper\ComentarioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\DataMapper\TipoEquipoDataMapper;
use Paw\App\Dtos\BadgeEquipoFormularoDto;
use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Services\EquipoService;

use Paw\App\Models\Equipo;
use Paw\App\Models\TipoEquipo;


class EquipoServiceImpl implements EquipoService
{
    private TipoEquipoDataMapper $tipoEquipoDataMapper;
    private EquipoDataMapper $equipoDataMapper;
    private ComentarioDataMapper $comentarioDataMapper;
    private NivelEloDataMapper $nivelEloDataMapper;
    private ResultadoPartidoDataMapper $resultadoPartidoDataMapper;

    public function __construct(TipoEquipoDataMapper $tipoEquipoDataMapper, 
                                EquipoDataMapper $equipoDataMapper, 
                                ComentarioDataMapper $comentarioDataMapper, 
                                NivelEloDataMapper   $nivelEloDataMapper,
                                ResultadoPartidoDataMapper $resultadoPartidoDataMapper)
    {
        $this->tipoEquipoDataMapper = $tipoEquipoDataMapper;
        $this->equipoDataMapper = $equipoDataMapper;
        $this->comentarioDataMapper = $comentarioDataMapper;
        $this->nivelEloDataMapper = $nivelEloDataMapper;
        $this->nivelEloDataMapper = $nivelEloDataMapper;
        $this->resultadoPartidoDataMapper = $resultadoPartidoDataMapper;
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

    public function getByTeamName(string $temName): ?Equipo
    {
        return $this->equipoDataMapper->findByTeamName($temName);
    }

    public function getTypeTeamById(int $typeTeamId)
    {
        $typeTeamById = $this->tipoEquipoDataMapper->findTypeTeamById($typeTeamId);
        $typeTeam = new TipoEquipo();
        $typeTeam->set([
            'id_tipo_equipo'      => $typeTeamById->id_tipo_equipo,
            'tipo'                => $typeTeamById->tipo,
            'descripcion_corta'   => $typeTeamById->descripcion_corta,
        ]);
        return $typeTeam;
    }

    public function getAllEquipos(array $selectParams, string $orderBy = 'id_nivel_elo', string $direction = 'DESC'): array
    {
        $equipos = $this->equipoDataMapper->findAllPaginated($selectParams, $orderBy, $direction);
        return $equipos;
    }

    // devuelve EquipoBannerDto para representar la info de los equipos en las tarjetas del front
    public function getAllEquiposBanner(
        ?array $selectParams = [],
        ?string $orderBy = null,
        ?string $direction = null
    ): array    {
        $equipos = $this->equipoDataMapper->findAllPaginated($selectParams, $orderBy, $direction);

        $equiposBanner = [];

        foreach ($equipos as $equipo) {
            $equipoBanner = $this->getEquipoBanner($equipo);
            $equiposBanner[] = $equipoBanner;
        }

        return $equiposBanner;
    }

    public function getEquipoBanner(Equipo $equipo): EquipoBannerDto
    {
        $descElo = $this->getDescripcionNivelEloById($equipo->getIdEquipo());
        $deportividad = $this->getDeportividadEquipo($equipo->getIdEquipo());
        $tipoEquipo = $this->tipoEquipoDataMapper->findById(['id_tipo_equipo' => $equipo->getIdTipoEquipo()]);
        
        $resultadosEquipo = $this->setResultadoParitdo($equipo->getIdEquipo());
        $equipoBanner = new EquipoBannerDto($equipo, $descElo, 
                                        $deportividad, $tipoEquipo->getTipo(), 
                                        $resultadosEquipo);
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

    public function setResultadoParitdo(int $id_equipo): array{
        $resultados = [];
        $resultadoPartidosDisputados = $this->resultadoPartidoDataMapper->findByIdEquipo($id_equipo);
         if ((int)$id_equipo === (int)$resultadoPartidosDisputados['id_equipo']) {
            $resultados = [
                'ganados' => $resultadoPartidosDisputados['ganados'],
                'perdidos' => $resultadoPartidosDisputados['perdidos'],
                'empates' =>  $resultadoPartidosDisputados['empatados']
            ];
        } else {
            // Setear 0 en caso de que no sea el equipo que jugó
            $resultados =[
                'ganados' => 0,
                'perdidos' => 0,
                'empates' =>  0
            ];
        }
        return $resultados;
    }

    public function setRestultadosPartido(array $todosLosEquipos): array {
        // Recorremos y agregamos los datos de partidos ganados, perdidos y empatados
        foreach ($todosLosEquipos as &$equipo) {
            $resultadoPartidosDisputados = $this->resultadoPartidoDataMapper->findByIdEquipo($equipo->id_equipo);
            
            if ((int)$equipo->id_equipo === (int)$resultadoPartidosDisputados['id_equipo']) {
                $equipo->ganados = $resultadoPartidosDisputados['ganados'];
                $equipo->perdidos = $resultadoPartidosDisputados['perdidos'];
                $equipo->empatados = $resultadoPartidosDisputados['empatados'];
            } else {
                // Setear 0 en caso de que no sea el equipo que jugó
                $equipo->ganados = 0;
                $equipo->perdidos = 0;
                $equipo->empatados = 0;
            }
        }
        return $todosLosEquipos;
    }

    public function getAllEquiposbyId(int $id_equipo, array $todosLosEquipos) {
        foreach ($todosLosEquipos as &$equipo) {
            if ((int)$equipo->id_equipo === (int)$id_equipo) {
                return $equipo;
            }
        }
        return null;
    }

    public function quitarMiEquipoDeEquipos(array $todosLosEquipos, Equipo $miEquipo){
        // Quitamos nuestro equipo
        $todosLosEquipos = array_filter($todosLosEquipos, function($equipo) use ($miEquipo) {
            return (int)$equipo->id_equipo !== (int)$miEquipo->id_equipo;
        });
        return $todosLosEquipos;
    }

    public function getBadgeEquipo(int $id_equipo) : BadgeEquipoFormularoDto
    {
        $equipo = $this->equipoDataMapper->findById(['id_equipo' => $id_equipo]);
        $descElo = $this->getDescripcionNivelEloById($equipo->getIdEquipo());

        $badge = new BadgeEquipoFormularoDto(
            $equipo->getNombre(),
            $equipo->getAcronimo(),
            $descElo
        );

        return $badge;
    }

}
