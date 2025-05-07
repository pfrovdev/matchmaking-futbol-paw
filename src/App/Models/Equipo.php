<?php
namespace Paw\App\Models;

use Exception;
use Paw\Core\AbstractModel;
use Paw\Core\Exceptions\InvalidValueFormatException;

class Equipo extends AbstractModel
{
    public $table = "Equipo";
    public $fields = [
        "id_equipo"       => null,
        "email"           => null,
        "nombre"          => null,
        "contrasena"      => null,
        "telefono"        => null,
        "ubicacion"       => null,
        "lema"            => null,
        "acronimo"        => null,
        "elo_actual"      => null,
        "fecha_creacion"  => null,
        "url_foto_perfil" => null,
        "id_tipo_equipo"  => null,
        "id_nivel_elo"    => null,
        "id_rol"          => null,
    ];

    public function setIdEquipo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de equipo no válido");
        }
        $this->fields["id_equipo"] = $id;
    }

    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidValueFormatException("Formato de email no válido");
        }
        $this->fields["email"] = $email;
    }

    public function setNombre(string $nombre)
    {
        if (empty($nombre) || strlen($nombre) > 30) {
            throw new InvalidValueFormatException("Nombre debe tener entre 1 y 30 caracteres");
        }
        $this->fields["nombre"] = $nombre;
    }

    public function setContrasena(string $contrasena)
    {
        if (empty($contrasena)) {
            throw new InvalidValueFormatException("La contraseña es obligatoria");
        }
        $this->fields["contrasena"] = $contrasena;
    }

    public function setTelefono(string $telefono)
    {
        if (empty($telefono)) {
            throw new InvalidValueFormatException("El teléfono es obligatorio");
        }
        $this->fields["telefono"] = $telefono;
    }

    public function setUbicacion($ubicacion)
    {
        if (empty($ubicacion)) {
            throw new InvalidValueFormatException("La ubicación es obligatoria");
        }
        $this->fields["ubicacion"] = $ubicacion;
    }

    public function setLema(string $lema)
    {
        if (strlen($lema) > 200) {
            throw new InvalidValueFormatException("El lema no puede superar 200 caracteres");
        }
        $this->fields["lema"] = $lema;
    }

    public function setAcronimo(string $acronimo)
    {
        if (empty($acronimo) || strlen($acronimo) > 5) {
            throw new InvalidValueFormatException("El acrónimo debe tener hasta 5 caracteres");
        }
        $this->fields["acronimo"] = $acronimo;
    }

    public function setEloActual(int $elo)
    {
        if ($elo < 0) {
            throw new InvalidValueFormatException("Elo actual no válido");
        }
        $this->fields["elo_actual"] = $elo;
    }

    public function setFechaCreacion(string $fecha)
    {
        // Se espera formato YYYY-MM-DD HH:MM:SS
        if (!strtotime($fecha)) {
            throw new InvalidValueFormatException("Formato de fecha no válido");
        }
        $this->fields["fecha_creacion"] = $fecha;
    }

    public function setUrlFotoPerfil(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidValueFormatException("URL de foto de perfil no válida");
        }
        $this->fields["url_foto_perfil"] = $url;
    }

    public function setIdTipoEquipo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de tipo de equipo no válido");
        }
        $this->fields["id_tipo_equipo"] = $id;
    }

    public function setIdNivelElo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de nivel elo no válido");
        }
        $this->fields["id_nivel_elo"] = $id;
    }

    public function setIdRol(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de rol no válido");
        }
        $this->fields["id_rol"] = $id;
    }

    public function set(array $values)
    {
        foreach (array_keys($this->fields) as $field) {
            if (!array_key_exists($field, $values)) {
                continue;
            }
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (method_exists($this, $method)) {
                $this->$method($values[$field]);
            }
        }
    }

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function select(array $params)
    {
        $qb = $this->getQueryBuilder();
        return $qb->select($this->table, $params);
    }

    public function saveNewTeam(array $params): ?string
    {
        $qb = $this->getQueryBuilder();
        return $qb->insert($this->table, $params);
    }

    public function selectLike(array $params): array
    {
        $qb = $this->getQueryBuilder();
        return $qb->selectLike($this->table, $params);
    }

    public function __toString(): string
    {
        $output = [];
        foreach ($this->fields as $k => $v) {
            if ($v !== null) {
                $output[] = "$k: $v";
            }
        }
        return implode(", ", $output);
    }
}