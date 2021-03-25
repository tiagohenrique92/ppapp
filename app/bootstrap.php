<?php

date_default_timezone_set("America/Sao_Paulo");

use Illuminate\Database\Capsule\Manager as Capsule;
use PPApp\Infra\DB;

$capsule = new Capsule;

$capsule->addConnection(array(
    "driver" => "mysql",
    "host" => "localhost",
    "database" => "ppapp",
    "username" => "root",
    "password" => "root",
));

$capsule->setAsGlobal();
$capsule->bootEloquent();

DB::setConnection($capsule->getConnection());
