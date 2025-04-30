<?php

namespace Paw\Core;

use Paw\Core\Database\QueryBuilder;
use Monolog\Logger;

class ModelFactory
{
    private QueryBuilder $queryBuilder;

    public function __construct(Logger $logger){
        $this->queryBuilder = QueryBuilder::getInstance();
        $this->queryBuilder->setLogger($logger);
    }

    public function make(string $modelClass): object{
        return new $modelClass($this->queryBuilder);
    }
}
