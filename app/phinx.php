<?php
require 'config.php';
return
    [
    'paths' => [
        'migrations' => DB_MIGRATIONS_PATH,
        'seeds' => DB_MIGRATIONS_SEED_PATH,
    ],
    'migration_base_class' => '\PPApp\Migration\Migration',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT,
            'charset' => DB_CHARSET,
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT,
            'charset' => DB_CHARSET,
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT,
            'charset' => DB_CHARSET,
        ],
    ],
    'version_order' => 'creation',
];
