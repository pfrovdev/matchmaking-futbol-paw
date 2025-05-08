<?php

namespace Paw\Core\Traits;
use Monolog\Logger;
trait Loggeable{

    public $logger;

    public function setLogger(Logger $logger){
        $this->logger = $logger;
    }

}

?>