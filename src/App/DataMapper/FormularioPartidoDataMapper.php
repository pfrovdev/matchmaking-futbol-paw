<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\FormularioPartido;
use Paw\Core\Database\QueryBuilder;

use function PHPSTORM_META\map;

class FormularioPartidoDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'FormularioPartido', $logger);
    }

    public function map(array $row): FormularioPartido
    {
        $formularioPartido = new FormularioPartido();
        $formularioPartido->set($row);
        return $formularioPartido;
    }

    public function mapAll(array $rows): array
    {
        $formulariosPartidos = [];
        foreach ($rows as $row) {
            $formulariosPartidos[] = $this->map($row);
        }
        return $formulariosPartidos;
    }

    public function findByIdFormularioPartido(int $id): FormularioPartido
    {
        return $this->map(parent::findById(['id' => $id]));
    }

}