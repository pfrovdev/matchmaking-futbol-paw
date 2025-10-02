<?php

namespace Paw\App\Services;

use Paw\App\Dtos\BadgeEquipoFormularoDto;
use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Dtos\FormularioPartidoDto;
use Paw\App\Models\Equipo;
use Paw\App\Models\FormularioPartido;

interface EquipoService {
    function getAllTiposEquipos();
    function getTypeTeamById(int $idTypeTeam);
    function saveNewTeam(Equipo $equipo);
    function getDeportividadEquipo(int $idEquipo): float;
    function getEquipoById(int $idEquipo): Equipo;
    function getDescripcionNivelEloById(int $idEquipo): string;
    function getAllEquipos(array $selectParams, string $orderBy = 'id_nivel_elo', string $direction = 'DESC'): array;
    function existsByEmail(string $email): bool;
    function getByEmail(string $email): ?Equipo;
    function getByTeamName(string $teamName): ?Equipo;
    function getEquipoBanner(Equipo $equipo): EquipoBannerDto;

    function getAllEquiposBanner(): array;
    function getAllNivelElo(): array;
    function getAllEquiposbyId(int $id_equipo, array $todosLosEquipos);
    function quitarMiEquipoDeEquipos(array $todosLosEquipos, Equipo $miEquipo);
    function setRestultadosPartido(array $todosLosEquipos);
    function getBadgeEquipo(int $id_equipo) : BadgeEquipoFormularoDto;
    function ActualizarEloActualEquipo(Equipo $equipo);
    function updateTeam(Equipo $equipo): bool;
}