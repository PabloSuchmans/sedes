<?php
  include_once 'utilidades.php';
  function sendMessage($apiKey = '', $basicAuth = '', $usuarios, $mensaje, $titulo = '', $color = '00ff00cc'){
    $content = array(
      "en" => $mensaje
    );
    
    $headings = array(
      'en' => $titulo
    );

    $fields = array(
      'app_id' => $apiKey,
      'include_player_ids' => $usuarios,
      'android_accent_color' => $color,
      'priority' => 11,
      // 'buttons' => array(
      //   array(
      //     "id" => "id2", 
      //     "text" => "second button", 
      //     "icon" => "ic_menu_share"
      //   ), 
      //   array(
      //     "id" => "id1", 
      //     "text" => "first button", 
      //     "icon" => "ic_menu_send"
      //   )
      // ),
      // 'data' => array(
      //   "foo" => "bar"
      // ),
      'contents' => $content,
      'headings' => $headings
    );
    
    $fields = json_encode($fields);

    print_r($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                'Authorization: Basic ' . $basicAuth));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
  }

  $datos_recibidos = obtenerDatosPOST();
  
  $response = sendMessage(
    $datos_recibidos['apiKey'], 
    $datos_recibidos['basicAuth'], 
    $datos_recibidos['usuarios'], 
    $datos_recibidos['titulo'], 
    $datos_recibidos['mensaje'], 
    $datos_recibidos['color']
  );
  
  echo $response;
?>   
 
    
