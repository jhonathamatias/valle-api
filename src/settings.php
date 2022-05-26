<?php

return [
    'renderer' => [
        'template_path' => dirname(__DIR__, 1) . "/templates",
        // 'cache' => __DIR__ . '/../cache/',
        'cache' => false,
        'debug' => true
    ],
    'dbal' => [
        'dbname' => getenv('RDS_DB_NAME'),
        'user' => getenv('RDS_USERNAME'),
        'password' => getenv('RDS_PASSWORD'),
        'host' => getenv('RDS_HOSTNAME'),
        'port' => getenv('RDS_PORT'),
        'driver' => 'pdo_mysql',
        'charset' => 'utf8mb4'
    ]
];