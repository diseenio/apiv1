<?php
$app->get("/".$tabla."", function () use ($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($db->$tabla()->where("borrado<>'S' and nombre <> ''")->order("nombre")); //->where("estado='V'")->order("orden")
});

$app->get("/".$tabla."/:id", function ($id) use ($app, $db, $tabla, $pk) {
    $app->response()->header("Content-Type", "application/json");
    $res = $db->$tabla->where("$pk",$id);
   // $app->response()->header("Content-Type", "application/json;charset=utf-8");
    echo json_encode($res);
});

$app->get("/".$tabla."/filtro/:filtro+", function ($filtro) use ($app, $db, $tabla, $pk) {
  // ejemplo: /api.php/notas/filtro/id/>/1130
    // NO FUNCIONA EL in()
    $app->response()->header("Content-Type", "application/json;charset=utf-8");
    $where = implode(",", $filtro);
    $where = str_replace(","," ",$where." and nombre <> '' "); // . " and estado='V' "
    $res = $db->$tabla->where($where)->order("nombre"); //->order("orden")
    echo json_encode($res);
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
