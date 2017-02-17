<?php
$tabla ="articulos"; // ESTO ES LO UNICO QUE HAY QUE REEMPLAZAR
$pk = "codarticulo"; // ESTO ES LO UNICO QUE HAY QUE REEMPLAZAR
$query="SELECT articulos.*, proveedores.nombre as proveedor, familias.nombre as rubro, marcas.nombre as marca 
FROM articulos
join artpro
on articulos.codarticulo = artpro.codarticulo
join proveedores
on proveedores.codproveedor = artpro.codproveedor 
left join familias 
on familias.codfamilia = articulos.codfamilia 
left join marcas
on marcas.codmarca = articulos.codproveedor1
WHERE 
articulos.descripcion <> '' and  
articulos.precio_2 > 0 and 
articulos.borrado <> 'S' AND 
articulos.estado_web <> 'A' AND 
artpro.borrado <> 'S' AND 
proveedores.rotacion = 'S' AND 
proveedores.borrado <> 'S'  ";

$query2 = "SELECT * 
			FROM articulos WHERE 1=1 ";
				 
/*
		   WHERE articulos.precio_2 > 0 and 
				 articulos.borrado <> 'S' AND 
				 articulos.estado_web <> 'A' AND
				 articulos.stock2 > 0 */				 

$orden=" ORDER BY descripcion";


							
$app->get("/".$tabla."", function () use ($app, $pdo, $db, $tabla, $pk, $query, $orden) {
	$limite = " LIMIT 1000"; // pongo este limite porque si no se cuelga.
    $app->response()->header("Content-Type", "application/json");
	$res = $pdo->prepare($query.$orden.$limite);
	if ($res->execute()){
		echo json_encode($res->fetchAll());
	}
	
});

$app->get("/".$tabla."/:id", function ($id) use ($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    $res = $db->$tabla->where("$pk",$id);
   // $app->response()->header("Content-Type", "application/json;charset=utf-8");
    echo json_encode($res);
});

$app->get("/".$tabla."/filtro/:filtro+", function ($filtro) use ($app, $pdo, $db, $tabla, $pk, $query, $orden) {
  // ejemplo: /api.php/notas/filtro/id/>/1130
    // NO FUNCIONA EL in()    
    $where = implode(",", $filtro);
    $where = str_replace(","," ",$where);	
    $limite = " LIMIT 1000"; // pongo este limite porque si no se cuelga.
    $app->response()->header("Content-Type", "application/json");
	$res = $pdo->prepare($query." and ".$where.$orden.$limite);
	if ($res->execute()){
		echo json_encode($res->fetchAll());
	}
	
	
});
$app->get("/".$tabla."/artpro/:proveedor/:filtro+", function ($proveedor,$filtro) use ($app, $pdo, $db, $tabla, $pk, $query, $query2, $orden) {
  // ejemplo: /api.php/notas/filtro/id/>/1130
    // NO FUNCIONA EL in()    
    $where = implode(",", $filtro);
    $where = str_replace(","," ",$where);	
    $limite = " LIMIT 1000"; // pongo este limite porque si no se cuelga.
    $app->response()->header("Content-Type", "application/json");
	if ($proveedor=='-100'){
		$query = $query2;
	} else {
		$query = $query." and artpro.codproveedor = $proveedor ";
	}
	//echo $query." and ".$where.$orden.$limite;
	$res = $pdo->prepare($query." and ".$where.$orden.$limite);
	if ($res->execute()){
		echo json_encode($res->fetchAll());
	}
	
	
});

$app->get("/".$tabla."/like/:desc", function ($desc) use ($app, $pdo, $db, $tabla, $pk, $query, $orden) {
	$limite = " LIMIT 1000"; // pongo este limite porque si no se cuelga.
    $app->response()->header("Content-Type", "application/json");
	$res = $pdo->prepare($query." and descripcion like '%$desc%'".$orden.$limite);
	if ($res->execute()){
		echo json_encode($res->fetchAll());
	}  
});

$app->post("/".$tabla."", function () use($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    $post = $app->request()->post();
    $max = $db->$tabla->max("$pk");
    $max++;
    $post["$pk"] = $max;
    $res = $db->$tabla->insert($post);
    echo json_encode($res);
});

$app->put("/".$tabla."/:id", function ($id) use ($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    $res = $db->$tabla("$pk", $id);
    $put = $app->request()->put();
    $res->update($put);
    echo json_encode($res);
});

$app->delete("/".$tabla."/:id", function ($id) use($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    $row = $db->$tabla()->where("$pk", $id);
    $res = $row->delete();
    echo json_encode($res);
});
