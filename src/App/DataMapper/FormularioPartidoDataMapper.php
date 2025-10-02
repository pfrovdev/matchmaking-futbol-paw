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
        // Defino valores por defecto para todos los numéricos
        $defaults = [
            'total_faltas'      => 0,
            'total_goles'       => 0,
            'total_amarillas'   => 0,
            'total_rojas'       => 0,
            'total_asistencias' => 0,
            'total_iteraciones' => 0,
        ];

        // Recorro cada default y si la fila no lo trae o viene a NULL, lo relleno
        foreach ($defaults as $campo => $valorPorDefecto) {
            if (! array_key_exists($campo, $row) || $row[$campo] === null) {
                $row[$campo] = $valorPorDefecto;
            }
        }

        $formularioPartido = new FormularioPartido();
        $formularioPartido->set($row);

        $this->logger->info('Mapeando FormularioPartido '. $formularioPartido->getIdFormulario());
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
        return $this->map(parent::findById(['id_formulario' => $id]));
    }

    public function findByIdPartidoOrderByFechaDesc(int $idPartido)
    {
        $formularios = $this->qb->select(
            $this->table,
            ['id_partido' => $idPartido],
            'fecha',
            'DESC'
        );

        return $this->mapAll($formularios);
    }

    public function findByIdPartidoAndIdEquipoOrderByFechaDesc(int $idPartido, int $idEquipo)
    {
        $formularios = $this->qb->select(
            $this->table,
            ['id_partido' => $idPartido, 'id_equipo' => $idEquipo],
            'fecha',
            'DESC'
        );
        return $this->mapAll($formularios);
    }

    public function save(FormularioPartido $formularioPartido)
    {
        $this->insert(
            $formularioPartido->fields
        );
    }
}
