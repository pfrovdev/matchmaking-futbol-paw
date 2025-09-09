<?php
namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\Estadisticas;
use Paw\Core\Database\QueryBuilder;

class EstadisticaDataMapper extends DataMapper
{
    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'Estadisticas', $logger);
    }

    public function map(array $row): Estadisticas
    {
        $estadisticas = new Estadisticas();
        $estadisticas->set($row);
        return $estadisticas;
    }

    public function mapAll(array $rows): array
    {
        return $rows;
    }

    public function findIdByIdEquipo(int $id_equipo): ?Estadisticas
    {
        $resultados = $this->findBy(['id_equipo' => $id_equipo]);

        if (empty($resultados)) {
            return null;
        }

        return $this->map($resultados[0]);
    }

    public function save(Estadisticas $estadisticas)
    {
        $this->insert(
            $estadisticas->fields
        );
    }

    public function updateEstadisticas(Estadisticas $estadisticas): bool
    {
        $data = [
            'goles' => $estadisticas->fields['goles'],
            'asistencias' => $estadisticas->fields['asistencias'],
            'tarjetas_rojas' => $estadisticas->fields['tarjetas_rojas'],
            'tarjetas_amarillas' => $estadisticas->fields['tarjetas_amarillas'],
            'jugados' => $estadisticas->fields['jugados'],
            'ganados' => $estadisticas->fields['ganados'],
            'perdidos' => $estadisticas->fields['perdidos'],
            'empatados' => $estadisticas->fields['empatados'],
        ];
        return (bool) $this->update($data, ['id_estadistica' => $estadisticas->fields['id_estadistica']]);
    }
}