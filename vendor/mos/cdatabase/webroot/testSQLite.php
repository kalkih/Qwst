<?php

require "../src/Database/TSQLQueryBuilderBasic.php";
require "../src/Database/CDatabaseBasic.php";

$db = new \Mos\Database\CDatabaseBasic();


//
// Precondition
//
if (!is_writable(__DIR__)) {
    die("This directory must be writable to create the SQLite database file.");
}


//
// Read config file
//
$options = require "config_sqlite.php";



//
// Carry out som tests
//
require __DIR__ . '/testDatabaseQueries.php';
