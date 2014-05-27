<?php

return [
    'dsn'     => "mysql:host=host;dbname=dbname;",
    'username'        => "",
    'password'        => "",
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "phpmvc_",
    'verbose' => false,
    //'debug_connect' => 'true',
];
