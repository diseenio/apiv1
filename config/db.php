<?php
$dbhost="localhost";
$dbuser="punidas_punidas";
$dbpass="Provincias123";
$dbname="punidas_sistema";
$dsn="mysql:host=$dbhost;dbname=$dbname";
$pdo = new PDO($dsn, $dbuser, $dbpass);
//$pdo->exec("set names 'utf8'");
$db = new NotORM($pdo);
?>
