<?php

namespace Paw\App\Services;

use Paw\App\Dtos\EquipoBannerDto;
use Paw\App\Models\Equipo;

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
    function getEquipoBanner(Equipo $equipo): EquipoBannerDto;

    function getAllEquiposBanner(): array;
    function getAllNivelElo(): array;
}