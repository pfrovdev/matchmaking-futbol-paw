<?php

namespace Paw\App\Dtos;

use JsonSerializable;
use Paw\App\Models\Equipo;

class EquipoBannerDto implements JsonSerializable
{
    public string $id_equipo;
    public int $ganados;
    public int $empatados;
    public int $perdidos;
    public string $nombre_equipo;
    public string $acronimo;
    public string $url_foto_perfil;
    public string $lema;
    public int $elo_actual;
    public string $descripcion_elo;
    public float $deportividad;
    public string $tipoEquipo;
    public string $numero_telefono;
    public string $ubicacion;
    public array $resultadosEquipo;

    public function __construct(Equipo $equipo, string $descripcion_elo, float $deportividad, string $tipoEquipo, ?array $resultadosEquipo)
    {
        $this->id_equipo = $equipo->getIdEquipo();
        $this->nombre_equipo = $equipo->getNombre();
        $this->acronimo = $equipo->getAcronimo();
        $this->url_foto_perfil = $equipo->getUrlFotoPerfil() ?? '';
        $this->lema = $this->utf8ize($equipo->getLema());
        $this->elo_actual = $equipo->getEloActual();
        $this->descripcion_elo = $descripcion_elo;
        $this->deportividad = $deportividad;
        $this->tipoEquipo = $tipoEquipo;
        $this->numero_telefono = $equipo->getTelefono();
        $this->ubicacion = $this->utf8ize($equipo->getUbicacion()) ?? '';
        $this->resultadosEquipo = $resultadosEquipo ?? [];
    }

    private function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = $this->utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

    public function getIdEquipo(): string
    {
        return $this->id_equipo;
    }

    public function getNombreEquipo(): string
    {
        return $this->nombre_equipo;
    }

    public function getAcronimo(): string
    {
        return $this->acronimo;
    }

    public function getUrlFotoPerfil(): string
    {
        return $this->url_foto_perfil;
    }

    public function getLema(): string
    {
        return $this->lema;
    }

    public function getDescripcionElo(): string
    {
        return $this->descripcion_elo;
    }

    public function getDeportividad(): float
    {
        return $this->deportividad;
    }

    public function getEloActual(): int
    {
        return $this->elo_actual;
    }

    public function getTipoEquipo(): string
    {
        return $this->tipoEquipo;
    }

    private function decodePointWKB(string $value): array
    {
        if (str_starts_with($value, '0x')) {
            $value = hex2bin(substr($value, 2));
        }

        if (empty($value) || strlen($value) < 25) {
            return ['lat' => 0.0, 'lon' => 0.0];
        }

        $endianness = unpack('C', substr($value, 4, 1))[1];

        if ($endianness === 1) {
            $data = unpack('Vsrid/Cendian/Lgeometry_type/dlon/dlat', $value);
        } else {
            $data = unpack('Nsrid/Cendian/Ngeometry_type/dlon/dlat', $value);
        }

        if (($data['geometry_type'] ?? null) !== 1) {
            return ['lat' => 0.0, 'lon' => 0.0];
        }
        return [
            'lat' => $data['lat'],
            'lon' => $data['lon'],
        ];
    }


    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function getLatitud(): float
    {
        $coords = $this->decodePointWKB($this->ubicacion);
        error_log("LAT: " . $coords['lat']);
        return $coords['lat'];
    }

    public function getLongitud(): float
    {
        $coords = $this->decodePointWKB($this->ubicacion);
        error_log("LON: " . $coords['lon']);
        return $coords['lon'];
    }


    public function getNumeroTelefono(): string
    {
        return $this->numero_telefono;
    }

    public function getResultadosEquipo(): array
    {
        return $this->resultadosEquipo;
    }

    public function jsonSerialize(): array
    {
        return [
            'id_equipo' => $this->id_equipo,
            'nombre_equipo' => $this->nombre_equipo,
            'acronimo' => $this->acronimo,
            'url_foto_perfil' => $this->url_foto_perfil,
            'lema' => $this->lema,
            'elo_actual' => $this->elo_actual,
            'descripcion_elo' => $this->descripcion_elo,
            'deportividad' => $this->deportividad,
            'tipoEquipo' => $this->tipoEquipo,
            'numero_telefono' => $this->numero_telefono,
            'ubicacion' => $this->ubicacion,
            'resultadosEquipo' => $this->resultadosEquipo
        ];
    }
}
