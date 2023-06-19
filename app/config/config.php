<?php

define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

define("CONFIG", [
    'db.driver' => 'mysql',
    'db.host' => 'mysql',
    'db.name' => $_ENV['MYSQL_DATABASE'],
    'db.user' => $_ENV['MYSQL_USER'],
    'db.password' => $_ENV['MYSQL_PASSWORD']
]);
