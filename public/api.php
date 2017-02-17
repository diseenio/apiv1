<?php
header("Access-Control-Allow-Origin: *");
date_default_timezone_set('America/Argentina/Buenos_Aires');
// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Credentials: false');
// header('Access-Control-Max-Age: 86400');
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
//header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");


//
require '../vendor/autoload.php';
require '../vendor/notorm-master/NotORM.php';

$app = new \Slim\Slim();
require '../config/db.php';

$app->get('/', function () {
    echo "<h1>Bienvenido!</h1>";
    echo "<h3>Lista de controladores</h3>";
    echo "<ul>"; // agregar las tablas que se van incluyendo
    foreach (scandir('../controllers') as $filename) {
      $path = '../controllers/' . $filename;
      if (is_file($path)) {
          echo "<li><a href='api.php/".basename($filename,".php")."'>".basename($filename,'.php')."<a></li>";
      }
    }
    echo "</ul>";

});
foreach (scandir('../controllers') as $filename) {
  $path = '../controllers/' . $filename;
  if (is_file($path)) {
      require $path;
  }
}
// include ('../controllers/notas.php');
$app->get("/validar/:token", function ($token) use ($app, $db) {	
    $ret = validar($token);
	echo $ret;
});

$app->run();

function validar($token){
	require '../config/db.php';
	$ret = false;
	$reg = $db->adj_usuarios->where("validacion = '$token'")->count("*");
	if ($reg>0){
		$ret = true;
	}
	return json_encode($ret);
}
