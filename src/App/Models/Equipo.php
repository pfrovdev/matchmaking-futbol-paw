<?php
namespace Paw\App\Models;

use Exception;
use Paw\Core\AbstractModel;
use Paw\Core\Exceptions\InvalidValueFormatException;

class Equipo extends AbstractModel
{
    public $table = "Equipo";
    public $fields = [
        "id_equipo" => null,
        "email" => null,
        "password" => null,
        "nombre_equipo" => null,
        "acronimo" => null,
        "descripcion_lema" => null,
        "telefono" => null,
        "geolocalizacion" => null,
        "tipo_equipo" => null,
    ];

    public function setIdEquipo(int $id)
    {
        $this->fields["id_equipo"] = $id;
    }

    public function setEmail(string $email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidValueFormatException("Formato de email no válido");
        }
        $this->fields["email"] = $email;
    }

    public function setPassword(string $password)
    {
        $this->fields["password"] = $password;
    }

    public function setNombreEquipo(string $nombre)
    {
        $this->fields["nombre_equipo"] = $nombre;
    }

    public function setAcronimo(?string $acronimo)
    {
        $this->fields["acronimo"] = $acronimo;
    }

    public function setDescripcionLema(?string $descripcion)
    {
        $this->fields["descripcion_lema"] = $descripcion;
    }

    public function setTelefono(?string $telefono)
    {
        $this->fields["telefono"] = $telefono;
    }

    public function setGeolocalizacion($geolocalizacion)
    {
        $this->fields["geolocalizacion"] = $geolocalizacion;
    }

    public function setTipoEquipo(string $tipo)
    {
        $this->fields["tipo_equipo"] = $tipo;
    }

    public function set(array $values){
        foreach(array_keys($this->fields) as $field){
            if(!isset($values[$field])){
                continue;
            }
            $method = "set" . ucfirst($field);
            $this->method($values[$field]); 
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }
}
