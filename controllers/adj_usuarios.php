<?php

$app->get("/adj_usuarios/:usu/:pass", function ($usu,$pass) use ($app, $db, $tabla, $pk) {
	$ret = array();	
    $res = $db->adj_usuarios->where("usuario = ? AND password = ?", $usu, $pass);
	if (count($res)>0){
		$reg = $res->fetch();			
		$fec = date('Ydm h:i:s');
		$ret['fechahora'] = $fec;		
		$token = md5($reg["id_usuario"].$fec);
		$dbdata = array("validacion" => $token);
		$filas = $res->update($dbdata);
		$ret['token'] = $token;
		$ret['registro'] = $reg;
		$ret['valida'] = true;
		$ret['descripcion'] = $reg["descripcion"];
	}else{
		$ret['valida'] = false;
	}
	$app->response()->header("Content-Type", "application/json;charset=utf-8");
	echo json_encode($ret);
	
});

$app->get("/adj_usuarios/:token", function ($token) use ($app, $db, $tabla, $pk) {
	$ret = array();	
    $res = $db->adj_usuarios->select("id_usuario")->where("validacion = ?", $token);
	if (count($res)>0){
		$ret = $res->fetch();					
	}else{
		$ret['valida'] = false;
	}
	$app->response()->header("Content-Type", "application/json;charset=utf-8");
	echo json_encode($ret);
	
});




 