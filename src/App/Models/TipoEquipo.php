<?php
namespace Paw\App\Models;

use Paw\Core\AbstractModel;

class TipoEquipo extends AbstractModel
{
    public $table = "TipoEquipo";
    public $fields = [
        "tipo" => null,
    ];

    public function __construct($queryBuilder = null)
    {
        parent::__construct($queryBuilder);
    }

    public function setTipo(string $tipo)
    {
        $this->fields["tipo"] = $tipo;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->fields[$name];
        }
        return null;
    }
}
