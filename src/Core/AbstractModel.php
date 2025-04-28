<?php

namespace Paw\Core;

use Paw\Core\Database\QueryBuilder;
use Paw\Core\Traits\Loggeable;

class AbstractModel{
    
    use Loggeable;

    private $queryBuilder;

    public function __construct(?QueryBuilder $queryBuilder = null) {
        $this->queryBuilder = $queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder){
        $this->queryBuilder = $queryBuilder;
    }

    protected function getQueryBuilder(): QueryBuilder {
        return $this->queryBuilder;
    }

}

?>