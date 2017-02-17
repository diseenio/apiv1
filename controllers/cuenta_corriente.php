<?php
$tipo_f = 'fecha_vencimiento';
$querySaldoIni = "select sum(CASE WHEN tipo_operacion = 'D' THEN importe ELSE importe * (-1) END) as saldoIni from cuenta_corriente 
where estado ='V' ";
//cuenta = $cuenta and $tipo_f < '$fecha_ini'  and sistema = $sistema 

$query = "SELECT 
fecha_vencimiento, concat( desc_comprobante, ' ' , letra, ' ', sucursal , '-', numero ) as comprob, observaciones ,
 CASE WHEN tipo_operacion = 'D' THEN importe ELSE 0 END AS debe  ,  CASE WHEN tipo_operacion = 'C' THEN importe ELSE 0 END AS haber,
codigo, letra, sucursal, numero, orden, sistema 
FROM cuenta_corriente WHERE 
estado ='V' ";
//and cuenta = 50 and sistema = 1
// and $tipo_f >= '$fecha_ini' and $tipo_f <= '$fecha_fin'

$querySaldo = "select sum(CASE WHEN tipo_operacion = 'D' THEN importe ELSE importe * (-1) END) as saldoFin from cuenta_corriente where estado = 'V' ";
//cuenta = $cuenta and estado = 'V' and sistema = $sistema

$querySaldoVen = "select sum(CASE WHEN tipo_operacion = 'D' THEN importe ELSE importe * (-1) END) as saldoVen from cuenta_corriente where estado = 'V' ";
// cuenta = $cuenta and estado = 'V' and sistema = $sistema and fecha_vencimiento < now() 

$app->get("/cuenta_corriente/:cuenta/:fechaIni/:fechaFin", function ($cuenta,$fechaIni,$fechaFin) use ($app, $pdo, $db, $query, $querySaldoIni, $querySaldo, $querySaldoVen, $tipo_f ) {
	$sistema = 1; // clientes
	$result = array();
    // saldo inicial
	$whereSaldo = " and cuenta = $cuenta and $tipo_f < '$fechaIni'  and sistema = $sistema ";
	$res = $pdo->prepare($querySaldoIni.$whereSaldo);
	if ($res->execute()){
		$result['saldoIni'] = $res->fetchAll();
	}
	// movimientos
	$where = " and cuenta = $cuenta and $tipo_f >= '$fechaIni' and $tipo_f <= '$fechaFin'  and sistema = $sistema ";
	$res = $pdo->prepare($query.$where);
	if ($res->execute()){
		$result['movimientos'] = $res->fetchAll();
	}
	// saldo final
	$where = " and cuenta = $cuenta and sistema = $sistema ";
	$res = $pdo->prepare($querySaldo.$where);
	if ($res->execute()){
		$result['saldoFin'] = $res->fetchAll();
	}
	// saldo vencido
	$where = " and cuenta = $cuenta and sistema = $sistema and $tipo_f < NOW() ";
	$res = $pdo->prepare($querySaldoVen.$where);
	if ($res->execute()){
		$result['saldoVen'] = $res->fetchAll();
	}
	
	$app->response()->header("Content-Type", "application/json");
	echo json_encode($result);
	
}); 