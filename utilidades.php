<?php
  if (isset($_SERVER['HTTP_ORIGIN'])) {  
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");  
    header('Access-Control-Allow-Credentials: true');  
    header('Access-Control-Max-Age: 86400');   
  }  

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
  
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
          header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
  
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
  } 

  function obtenerDatosPOST($ComoCadena = NULL) {
      if ($ComoCadena != NULL) {
        return file_get_contents('php://input');  //uso esto en vez de $_POST
      } else {
        return json_decode(file_get_contents('php://input'), TRUE);  //uso esto en vez de $_POST
      }
  }

  function _slugify($string, $replace = array(), $delimiter = '-') {
  // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
  // if (!extension_loaded('iconv')) {
  //     throw new Exception('iconv module not loaded');
  // }
  // Save the old locale and set the new locale to UTF-8
      $oldLocale = setlocale(LC_ALL, '0');
      setlocale(LC_ALL, 'en_US.UTF-8');
      $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
      if (!empty($replace)) {
          $clean = str_replace((array) $replace, ' ', $clean);
      }
      $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
      $clean = strtolower($clean);
      $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
      $clean = trim($clean, $delimiter);
      // Revert back to the old locale
      setlocale(LC_ALL, $oldLocale);
      return $clean;
  }

  function funcEjecucionLocal() {
    return strtoupper($_SERVER['HTTP_HOST']) === 'LOCALHOST';
  }

  function funcConexionPermitida() {
    // if (isset($_GET['slug'])) {
    //   $slug = 'V2FybmluZzwvYj46IG15c3FsaV9lcnJvcigpIGV4cGVjdHMgcGFyYW1ldGVyIDEgdG8gYmUgbXlzcWxpLCBib29sZWFuIGdpdmVuIGlu';
    //   //idea, mandar por GET el usuario y algun dato mas para poder constatar si la conexion esta permitida
    //   //tambien aca podria chequearse que el ultimo acceso sea en los ultimos X segundos / minutos / horas
    //   if ($slug === $_GET['slug']) {
        return TRUE;
    //   } else {
    //     return FALSE;
    //   }
    // } else {
    //   return FALSE;
    // }
  }

  function funcEmpaquetarCadena($cadena) {
    // $cadena = 'ewoiZW1haWwiOiAiam9hcXVpbmRhbmVyaUBnbWFpbC5jb20iLAoiY2xhdmUiOiAiMSIsCiJub21icmVfYmQiOiAiY2hhbm5lbF9tYW5hZ2VyXzQyIgp9'; //test
    if ($cadena[0] === 'E') {
      $resultado = '';
      for($i = 1; $i < strlen($cadena); $i++) {
        switch ($cadena[$i]) {
          case '0': $resultado = '1' . $resultado; break;
          case '1': $resultado = '2' . $resultado; break;
          case '2': $resultado = '3' . $resultado; break;
          case '3': $resultado = '4' . $resultado; break;
          case '4': $resultado = '5' . $resultado; break;
          case '5': $resultado = '6' . $resultado; break;
          case '6': $resultado = '7' . $resultado; break;
          case '7': $resultado = '8' . $resultado; break;
          case '8': $resultado = '9' . $resultado; break;
          case '9': $resultado = '0' . $resultado; break;
          default: $resultado = $cadena[$i] . $resultado; 
        }
      }      
    // echo 'antes:   ' . $cadena . '<br>';     //test
    // echo 'despues: ' . $resultado . '<br>';   //test
    }
    if ($cadena[0] === 'D') {
      $resultado = '';
      for($i = 1; $i < strlen($cadena); $i++) {
        switch ($cadena[$i]) {
          case '1': $resultado = '0' . $resultado; break;
          case '2': $resultado = '1' . $resultado; break;
          case '3': $resultado = '2' . $resultado; break;
          case '4': $resultado = '3' . $resultado; break;
          case '5': $resultado = '4' . $resultado; break;
          case '6': $resultado = '5' . $resultado; break;
          case '7': $resultado = '6' . $resultado; break;
          case '8': $resultado = '7' . $resultado; break;
          case '9': $resultado = '8' . $resultado; break;
          case '0': $resultado = '9' . $resultado; break;
          default: $resultado = $cadena[$i] . $resultado; 
        }
      }    
    // echo 'antes:   ' . $cadena . '<br>';    //test
    }      
    return $resultado;
  }

  function funcLeerArchivoTexto($archivo = '') {
    return implode('', file($archivo));
  }

  function funcRespuesta($respuesta) {
    return json_encode($respuesta, JSON_UNESCAPED_UNICODE);
  }

  function funcRestarFechas($fechaDesde, $fechaHasta) {
    $fechaDesde = strtotime($fechaDesde); 
    $fechaHasta = strtotime($fechaHasta); 
    return ($fechaHasta - $fechaDesde)/60/60/24;   
  }

  function funcOrdenarArray(&$array, $key) {
    $orden = array_column($array, $key);
    array_multisort($orden, SORT_ASC, $array);
  }
  
  function funcControlarParametros(&$parametro, $nombresParametros = array()) {
    $resultado = FALSE;
    if (!isset($parametro)) {
      $resultado = 'no hay parametros';
    } else {
      foreach($nombresParametros as $par) {
        if (!isset($parametro[$par])) {
          $resultado = 'el parametro \'' . $par . '\' no existe';
        }
      }
    }
    if ($resultado) {
      echo $resultado  ;
      throw new Exception($resultado);
    }
  }

  function funcCoalesce($variable, $valorDefecto) {
    if ($variable == null) {
      return $valorDefecto;
    } else {
      return $variable;
    }
  }

  function print_enter($variable) {
    print_r($variable);
    print_r('\r\n');
    echo '<br><hr>';
  }

  function funcEnviarDatosReclamos($url, $data, $ver = FALSE) {
    if ($ver) {
      print_r($data);
    }
    $postdata = json_encode($data, JSON_UNESCAPED_UNICODE);
    if ($ver) {
      echo '<br>';
      echo 'POST DATA';
      echo '<br>';
      echo '<br>';
      echo $postdata;
      echo '<br>';
      echo '<br>';
      echo '-----------';
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    if ($ver) {
      echo 'RESPUESTA';
      echo '<br>';
      echo '<br>';
      print_r($result);
      echo '<br>';
      echo '<br>';
    }
    return json_decode($result, TRUE);
  }   

  function funcCrearImagenTexto($nombreImagen,
                                $ancho,
                                $alto,
                                $txt,
                                $colorTexto = 'FFFFFF', 
                                $colorGlobo = '770000') {

    $im = imagecreatetruecolor($ancho, $alto);
    $colorGlobo = hexdec($colorGlobo);
    $colorTexto = hexdec($colorTexto);
    $negro = imagecolorallocate($im, 0, 0, 0);
    // $colorTexto = imagecolorallocate($im, 255, 255, 255);
    // print_r($colorTexto);

    // Hacer el fondo transparente
    imagecolortransparent($im, $negro);

    $fontname = realpath('fonts/RobotoMono/RobotoMono-VariableFont_wght.ttf');

    $tb = imagettfbbox(24, 0, $fontname, $txt);
    $x = ceil(($ancho - $tb[2]) / 2); // lower left X coordinate for text
    $tb = imagettfbbox(24, 0, $fontname, $txt);
    $y = ceil(($alto - $tb[7]) / 2); // lower left y coordinate for text

    // Dibujar un rectángulo rojo
    imagefilledellipse($im, $ancho / 2, $alto / 2, $ancho, $alto, $colorGlobo);

    // dibujar las letras
    imagettftext($im, 24, 0, $x, $y, $colorTexto, $fontname, $txt); 

    // Guardar la imagen
    imagepng($im, 'img/' . $nombreImagen . '.png');

    imagedestroy($im);
  }
  
  function funcObtenerIniciales($nombre) {
    $iniciales = preg_split('/[\s,]+/', strtoupper($nombre));
    return $iniciales[0][0] . (count($iniciales) == 1 ? '' : $iniciales[count($iniciales) - 1][0]);
  }

  function funcEnviarNotificacion($usuarios, $mensaje, $titulo = '', $color = '00ff00cc'){
    $content = array(
      "en" => $mensaje
    );
    
    $headings = array(
      'en' => $titulo
    );

    $fields = array(
      'app_id' => 'b65f8252-643f-414f-b62c-2041461c2ae9',
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

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                'Authorization: Basic ZDE3ZjNkOTUtZWQ1Ni00OTMzLTgxMzYtODc4MDcwNDJhNjAw'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
  }

?>
