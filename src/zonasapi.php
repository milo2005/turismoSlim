<?php

$app->get("/zonas", function($request, $response, $args){

  $query = $this->db->prepare("SELECT * FROM zonas");
  $query->execute();

  $results = $query->fetchAll(PDO::FETCH_ASSOC);

  $response = $response->withStatus(200);
  $response = $response->withHeader("Content-Type","application/json");

  $body =  $response->getBody();
  $body->write(json_encode($results));

  return $response;

});

$app->get("/zonas/{id}", function($request, $response, $args){

  $id  = $args["id"];
  $query = $this->db->prepare("SELECT * FROM zonas WHERE idzonas = ".$id);
  $query->execute();

  $results = $query->fetchAll(PDO::FETCH_ASSOC);

  $response = $response->withHeader("Content-Type","application/json");
  if(count($results)>0){
      $response = $response->withStatus(200);
      $body =  $response->getBody();
      $body->write(json_encode($results[0]));
  }else{
    $response = $response->withStatus(404);
  }

  return $response;


});

$app->post("/zonas", function($request, $response,$args){

  $body = $request->getBody();
  $body = json_decode($body);

  $img = base64_decode($body->imagen);
  $name = date_format(new DateTime(), "Y_m_d_H_i_s");
  $file = fopen("images/".$name.".png","w");
  fwrite($file,$img);
  fclose($file);
  $url = "http://localhost/turismo/public/images/".$name.".png";

  $query = $this->db->prepare("INSERT INTO zonas (nombre, descripcion, imagen, direccion)"
   ."VALUES (:n,:d,:i,:di)");

  $status = $query->execute(array(":n"=>$body->nombre, ":d"=>$body->descripcion
  , ":i"=>$url,":di"=>$body->direccion));

  $rta = "";

  if($status){
    $response = $response->withStatus(200);
    $rta = json_encode(array("status"=>"OK", "img"=>$url));
  }else{
    $response = $response->withStatus(500);
    $rta = json_encode(array("status"=>"FAIL"));
  }

  $response = $response->withHeader("Content-Type", "application/json");
  $bodyResponse =  $response->getBody();
  $bodyResponse->write($rta);
  return $response;

});

$app->delete("/zonas/{id}", function($request,$response, $args){
  $id  = $args["id"];
  $query = $this->db->prepare("DELETE FROM zonas WHERE idzonas = ".$id);
  $status = $query->execute();

  $rta = "";

  if($status){
    $response = $response->withStatus(200);
    $rta = json_encode(array("status"=>"OK"));
  }else{
    $response = $response->withStatus(500);
    $rta = json_encode(array("status"=>"FAIL"));
  }

  $response = $response->withHeader("Content-Type", "application/json");
  $bodyResponse =  $response->getBody();
  $bodyResponse->write($rta);
  return $response;

});

$app->put("/zonas/{id}", function($request, $response, $args){
  $id = $args["id"];

  $body = $request->getBody();
  $body = json_decode($body);

  $query = $this->db->prepare("UPDATE zonas SET nombre = :n , descripcion = :d, imagen = :i ,  direccion = :di WHERE idzonas = :idz" );

  $status = $query->execute(array(":n"=>$body->nombre, ":d"=>$body->descripcion, ":i"=>$body->imagen,":di"=>$body->direccion, ":idz"=>$id));

  $rta = "";

  if($status){
    $response = $response->withStatus(200);
    $rta = json_encode(array("status"=>"OK"));
  }else{
    $response = $response->withStatus(500);
    $rta = json_encode(array("status"=>"FAIL"));
  }

  $response = $response->withHeader("Content-Type", "application/json");
  $bodyResponse =  $response->getBody();
  $bodyResponse->write($rta);
  return $response;
});
