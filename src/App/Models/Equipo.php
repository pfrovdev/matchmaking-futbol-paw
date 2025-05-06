<?php
namespace Paw\App\Models;

use Exception;
use Paw\Core\AbstractModel;
use Paw\Core\Exceptions\InvalidValueFormatException;

class Equipo extends AbstractModel{
    public $table = "Equipo";
    public $fields = [
        "id_equipo" => null,
        "email" => null,
        "nombre" => null,
        "password" => null,
        "acronimo" => null,
        "lema" => null,
        "telefono" => null,
        "ubicacion" => null,
        "id_nivel_elo" => null,
        "elo_actual" => null,
        "id_tipo_equipo" => null,
        "fecha_creacion" => null,
        "id_estadisitca" => null,
        "url_foto_perfil" => null,
    ];

    public function setIdEquipo(int $id){
        $this->fields["id_equipo"] = $id;
    }

    public function setEmail(string $email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidValueFormatException("Formato de email no válido");
        }
        $this->fields["email"] = $email;
    }

    public function setPassword(string $password){
        $this->fields["password"] = $password;
    }

    public function setNombreEquipo(string $nombre){
        $this->fields["nombre"] = $nombre;
    }

    public function setAcronimo(?string $acronimo){
        $this->fields["acronimo"] = $acronimo;
    }

    public function setLema(?string $lema){
        $this->fields["lema"] = $lema;
    }

    public function setTelefono(?string $telefono){
        $this->fields["telefono"] = $telefono;
    }

    public function setUbicacion($ubicacion)
    {
        $this->fields["ubicacion"] = $ubicacion;
    }

    public function setIdTipoEquipo(string $id_tipo_equipo){
        $this->fields["id_tipo_equipo"] = $id_tipo_equipo;
    }

    public function set(array $values){
        foreach(array_keys($this->fields) as $field){
            if(!isset($values[$field])){
                continue;
            }
            $method = "set" . ucfirst($field);
            $this->$method($values[$field]); 
        }
    }

    public function __get($name){
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }

    public function select(array $params) {
        $queryBuilder = $this->getQueryBuilder();
        $result = $queryBuilder->select($this->table, $params);
        return $result;
    }

    public function saveNewTeam(array $params): ?string{
        $queryBuilder = $this->getQueryBuilder();
        return $queryBuilder->insert($this->table, $params);
    }

    public function selectLike(array $params): array{
        $queryBuilder = $this->getQueryBuilder();
        $result = $queryBuilder->selectLike($this->table, $params);
        return $result;
    }
}
