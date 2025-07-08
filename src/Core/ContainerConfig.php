<?php

namespace Paw\Core;

use Paw\App\Controllers\ComentarioController;
use Monolog\Logger;
use Paw\App\Commons\Notificador;
use Paw\App\Commons\NotificadorEmail;
use Paw\App\Controllers\AuthController;
use Paw\App\Controllers\DesafioController;
use Paw\App\Controllers\EquipoController;
use Paw\App\Controllers\ErrorController;
use Paw\App\Controllers\PageController;
use Paw\App\Controllers\PartidoController;
use Paw\App\DataMapper\ComentarioDataMapper;
use Paw\App\DataMapper\DesafioDataMapper;
use Paw\App\DataMapper\EquipoDataMapper;
use Paw\App\DataMapper\EstadoDesafioDataMapper;
use Paw\App\DataMapper\EstadoPartidoDataMapper;
use Paw\App\DataMapper\FormularioPartidoDataMapper;
use Paw\App\DataMapper\HistorialPartidoDataMapper;
use Paw\App\DataMapper\NivelEloDataMapper;
use Paw\App\DataMapper\PartidoDataMapper;
use Paw\App\DataMapper\ResultadoPartidoDataMapper;
use Paw\App\DataMapper\TipoEquipoDataMapper;
use Paw\App\Services\ComentarioEquipoService;
use Paw\App\Services\DesafioService;
use Paw\App\Services\EquipoService;
use Paw\App\Services\PartidoService;
use Paw\App\Services\NotificationService;
use Paw\App\Services\Impl\ComentarioEquipoServiceImpl;
use Paw\App\Services\Impl\DesafioServiceImpl;
use Paw\App\Services\Impl\EquipoServiceImpl;
use Paw\App\Services\Impl\PartidoServiceImpl;
use Paw\Core\Database\QueryBuilder;
use Paw\Core\JWT\JsonFileStorage;
use Paw\Core\JWT\RedisStorage;
use Paw\Core\JWT\Services\TokenService;
use Paw\Core\JWT\TokenStorageInterface;
use Paw\Core\Middelware\AuthMiddelware;

class ContainerConfig
{
    public static function configure(Container $c, Logger $logger): void
    {

        // Query Builder
        $c->set(QueryBuilder::class, fn($c) => QueryBuilder::getInstance());

        // DataMappers
        $c->set(ComentarioDataMapper::class, fn($c) => new ComentarioDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(EquipoDataMapper::class, fn($c) => new EquipoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(TipoEquipoDataMapper::class, fn($c) => new TipoEquipoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(NivelEloDataMapper::class, fn($c) => new NivelEloDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(DesafioDataMapper::class, fn($c) => new DesafioDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(EstadoDesafioDataMapper::class, fn($c) => new EstadoDesafioDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(PartidoDataMapper::class, fn($c) => new PartidoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(EstadoPartidoDataMapper::class, fn($c) => new EstadoPartidoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(ResultadoPartidoDataMapper::class, fn($c) => new ResultadoPartidoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(FormularioPartidoDataMapper::class, fn($c) => new FormularioPartidoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        $c->set(HistorialPartidoDataMapper::class, fn($c) => new HistorialPartidoDataMapper(
            $c->get(QueryBuilder::class),
            $logger
        ));
        // Token Storage (JWT)
        $c->set(TokenStorageInterface::class, function ($c) {
            $backend = strtolower(getenv('JWT_STORAGE') ?: 'file');

            switch ($backend) {
                case 'redis':
                    return new RedisStorage(
                        getenv('REDIS_HOST') ?: '127.0.0.1',
                        (int)(getenv('REDIS_PORT') ?: 6379),
                        getenv('JWT_REDIS_PREFIX') ?: 'jwt:blacklist:'
                    );
                default:
                    return new JsonFileStorage(
                        __DIR__ . '/../../Core/JWT/blacklist.json'
                    );
            }
        });

        $c->set(AuthMiddelware::class, fn($c) => new AuthMiddelware(
            $c->get(TokenService::class),
            (int) getenv('JWT_ACCESS_TTL'),
            (int) getenv('JWT_REFRESH_TTL'),
            (int) getenv('JWT_REFRESH_WINDOW')
        ));

        // Services
        $c->set(ComentarioEquipoService::class, fn($c) => new ComentarioEquipoServiceImpl(
            $c->get(ComentarioDataMapper::class),
            $c->get(EquipoService::class)
        ));

        $c->set(PartidoService::class, fn($c) => new PartidoServiceImpl(
            $c->get(PartidoDataMapper::class),
            $c->get(EstadoPartidoDataMapper::class),
            $c->get(DesafioDataMapper::class),
            $c->get(EquipoService::class),
            $c->get(HistorialPartidoDataMapper::class),
            $c->get(ResultadoPartidoDataMapper::class),
            $c->get(FormularioPartidoDataMapper::class),
            $c->get(NotificationService::class)
        ));

        $c->set(EquipoService::class, fn($c) => new EquipoServiceImpl(
            $c->get(TipoEquipoDataMapper::class),
            $c->get(EquipoDataMapper::class),
            $c->get(ComentarioDataMapper::class),
            $c->get(NivelEloDataMapper::class),
            $c->get(ResultadoPartidoDataMapper::class)
        ));

        $c->set(DesafioService::class, fn($c) => new DesafioServiceImpl(
            $c->get(DesafioDataMapper::class),
            $c->get(EstadoDesafioDataMapper::class),
            $c->get(PartidoService::class),
            $c->get(EquipoService::class)
        ));

        $c->set(TokenService::class, fn($c) => new TokenService(
            $c->get(TokenStorageInterface::class)
        ));

        $c->set(Notificador::class, fn($c) => new NotificadorEmail());
        $c->set(NotificationService::class, fn($c) => new NotificationService(
            $c->get(Notificador::class)
        ));

        // Controllers

        $c->set(PageController::class, fn($c) => new PageController(
            $logger,
            $c->get(AuthMiddelware::class)
        ));

        $c->set(EquipoController::class, fn($c) => new EquipoController(
            $logger,
            $c->get(EquipoService::class),
            $c->get(PartidoService::class),
            $c->get(DesafioService::class),
            $c->get(NotificationService::class),
            $c->get(ComentarioEquipoService::class),
            $c->get(AuthMiddelware::class)
        ));

        $c->set(DesafioController::class, fn($c) => new DesafioController(
            $logger,
            $c->get(DesafioService::class),
            $c->get(NotificationService::class),
            $c->get(EquipoService::class),
            $c->get(AuthMiddelware::class)
        ));

        $c->set(ComentarioController::class, fn($c) => new ComentarioController(
            $logger,
            $c->get(ComentarioEquipoService::class),
            $c->get(EquipoService::class),
            $c->get(AuthMiddelware::class)
        ));

        $c->set(PartidoController::class, fn($c) => new PartidoController(
            $logger,
            $c->get(PartidoService::class),
            $c->get(EquipoService::class),
            $c->get(AuthMiddelware::class)
        ));

        $c->set(AuthController::class, fn($c) => new AuthController(
            $logger,
            $c->get(TokenService::class),
            $c->get(EquipoService::class),
            $c->get(AuthMiddelware::class)
        ));

        $c->set(ErrorController::class, fn($c) => new ErrorController(
            $logger,
            $c->get(AuthMiddelware::class)
        ));
    }
}