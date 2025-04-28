<?php
namespace Paw\App\Models;
use Paw\Core\AbstractModel;
class EstadoPartido extends AbstractModel {
    public $table = "EstadoPartido";
    public $fields = [
        "estado" => null,
    ];

    public function __construct($queryBuilder = null)
    {
        parent::__construct($queryBuilder);
    }

    public function setEstado(string $estado){
        $this->fields["estado"] = $estado;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }
}
