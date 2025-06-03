<?php

return [
    // Entorno de la aplicación
    'debug' => true,

    // Configuración de logs
    'log' => [
        'name' => 'mvc-app',
        'path' => __DIR__ . '/../../logs/app.log',
        'level' => Monolog\Logger::DEBUG
    ],

    // Configuración de rutas iniciales
    'routes' => [
        ['path' => '/', 'action' => 'PageController@index', 'method' => 'GET'],
        ['path' => '/about-us', 'action' => 'PageController@aboutUs', 'method' => 'GET'],
        ['path' => '/login', 'action' => 'PageController@login', 'method' => 'GET'],
        ['path' => '/create-account', 'action' => 'EquipoController@createAccount', 'method' => 'GET'],
        ['path' => '/create-team', 'action' => 'EquipoController@createTeam', 'method' => 'GET'],
        ['path' => '/search-team', 'action' => 'EquipoController@searchTeam', 'method' => 'GET'],
        ['path' => '/dashboard', 'action' => 'EquipoController@dashboard', 'method' => 'GET'],
        ['path' => '/ranking-teams', 'action' => 'EquipoController@rankingTeams', 'method' => 'GET'],
        ['path' => '/details-team', 'action' => 'EquipoController@detailsTeam', 'method' => 'GET'],
        ['path' => '/comentarios', 'action' => 'ComentarioController@index', 'method' => 'GET'],
        ['path' => '/coordinar-resultado', 'action' => 'EquipoController@coordinarResultado', 'method' => 'GET'],
        ['path' => '/desafios', 'action' => 'DesafioController@index', 'method' => 'GET'],
        
        ['path' => '/login', 'action' => 'AuthController@login', 'method' => 'POST'],
        ['path' => '/register', 'action' => 'EquipoController@register', 'method' => 'POST'],
        ['path' => '/register-team', 'action' => 'EquipoController@registerTeam', 'method' => 'POST'],
        ['path' =>  '/logout', 'action' => 'AuthController@logout', 'method' => 'GET'],
        
        ['path' => '/accept-desafio', 'action' => 'DesafioController@aceptarDesafio', 'method' => 'POST'],
        ['path' => '/reject-desafio', 'action' => 'DesafioController@rechazarDesafio', 'method' => 'POST']
    ],


    'database' => [
        'DB_ADAPTER' => 'mysql',
        'DB_HOSTNAME' => getenv('DB_HOST') ?: '127.0.0.1',
        'DB_DATABASE' => getenv('DB_NAME'),
        'DB_USERNAME' => getenv('DB_USER'),
        'DB_PASSWORD' => getenv('DB_PASSWORD'),
        'DB_PORT' => getenv('DB_PORT') ?: '3306',
        'DB_CHARSET' => getenv('DB_CHARSET') ?: 'utf8mb4'
    ]
];
