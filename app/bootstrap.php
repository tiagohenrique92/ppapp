<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection(array(
    "driver" => "mysql",
    "host" => "localhost",
    "database" => "ppapp",
    "username" => "root",
    "password" => "root"
));

$capsule->setAsGlobal();
$capsule->bootEloquent();