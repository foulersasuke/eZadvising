<?php


define("DBUSER", "root");
define("DBPASSWORD", "");
define("DBSERVER", "localhost");
define("DBNAME", "ezadvising");

$connectionString = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;

define("DBCONNECTSTRING", $connectionString);

//Supress Errors
//ini_set("display_errors", 0);

//dont suprress
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>