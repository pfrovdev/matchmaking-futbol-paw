<?php

namespace Paw\Core;

use Paw\App\Commons\Notificador;
use Paw\App\Commons\NotificadorEmail;
use Paw\App\Services\DesafioService;
use Paw\App\Services\Impl\DesafioServiceImpl;
use Paw\App\Services\Impl\PartidoServiceImpl;
use Paw\App\Services\NotificationService;
use Paw\App\Services\PartidoService;
use Paw\Core\Container;
use Paw\Core\Database\QueryBuilder;

class ContainerConfig
{
    public static function configure(Container $c): void
    {
        $c->set(QueryBuilder::class, fn($c) => QueryBuilder::getInstance());

        $c->set(DesafioService::class, fn($c) => new DesafioServiceImpl(
            $c->get(QueryBuilder::class),
            $c->get(PartidoService::class)
        ));

        $c->set(PartidoService::class, fn($c) => new PartidoServiceImpl(
            $c->get(QueryBuilder::class)
        ));

        $c->set(Notificador::class, fn($c) => new NotificadorEmail());

        $c->set(NotificationService::class, fn($c) => new NotificationService(
            $c->get(Notificador::class)
        ));
    }
}
