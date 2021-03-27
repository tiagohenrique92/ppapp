<?php

date_default_timezone_set(APP_TIMEZONE);

use Illuminate\Database\Capsule\Manager as Capsule;
use PPApp\Infra\DB;

$capsule = new Capsule;

$capsule->addConnection(array(
    "driver" => "mysql",
    "host" => DB_HOST,
    "database" => DB_NAME,
    "username" => DB_USER,
    "password" => DB_PASSWORD,
    'charset' => DB_CHARSET,
    'collation' => DB_COLLATION,
));

$capsule->setAsGlobal();
$capsule->bootEloquent();

DB::setConnection($capsule->getConnection());
