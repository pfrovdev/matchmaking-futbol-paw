<?php

namespace Paw\App\DataMapper;

use Monolog\Logger;
use Paw\App\Models\Comentario;
use Paw\Core\Database\QueryBuilder;

class ComentarioDataMapper extends DataMapper
{

    public function __construct(QueryBuilder $qb, Logger $logger)
    {
        parent::__construct($qb, 'Comentario', $logger);
    }

    public function findById(array $params): ?Comentario
    {
        $result = parent::findById($params);
        if ($result) {
            return $this->map($result);
        }
        return null;
    }

    public function findByEquipo(int $idEquipo): array
    {
        $result = $this->findBy(['id_equipo_comentado' => $idEquipo]);
        return $this->mapAll($result);
    }

    public function saveNewComentario(Comentario $comentario){
        $this->qb->insert($this->table, $comentario->fields);
    }

    public function map (array $data): Comentario
    {
        $comentario = new Comentario();
        $comentario->set($data);
        return $comentario;
    }

    public function mapAll(array $rows): array
    {
        $comentarios = [];
        foreach ($rows as $row) {
            $comentarios[] = $this->map($row);
        }
        $this->logger->info("comentarios: ".$comentarios[0]->getEquipoComentadorId());
        return $comentarios;
    }
}