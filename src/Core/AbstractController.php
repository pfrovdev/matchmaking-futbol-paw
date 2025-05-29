<?php

namespace Paw\Core;

use Paw\Core\ModelFactory;
use Monolog\Logger;
use Paw\Core\Middelware\AuthMiddelware;

class AbstractController
{
    public string $viewsDir = "";

    public ?string $modelName = null;
    public ?object $model = null;
    protected Logger $logger;
    protected AuthMiddelware $auth;

    public function __construct(Logger $log, AuthMiddelware $auth)
    {
        $this->logger = $log;
        $this->viewsDir = __DIR__ . "/../App/views/";
        $this->auth = $auth;
    }
}
