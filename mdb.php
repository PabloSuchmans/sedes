<?php
  
  set_time_limit(0); 
  $entreRiosNet = TRUE;
  // $entreRiosNet = FALSE;
  $pasarTodo = 'Top 5';
  $pasarTodo = '';

  include_once 'constantes.php';
  include_once 'utilidades.php';

  $datos_recibidos = obtenerDatosPOST();
  
  // not sure this got pickd up; I needed to edit C:\Program Files\PHP\php.ini
  ini_set('display_errors', 1);
  
  try {
    if (!$entreRiosNet) {
      $mdbFilename = $_SERVER["DOCUMENT_ROOT"] . '/reclamos/canito/datosinter.mdb';
      $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)}; Dbq=$mdbFilename;Uid=Admin");
    } else {
      $mdbFilename = 'c:\datosinter.mdb';
      $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)}; Dbq=$mdbFilename;Uid=Admin");
    }
    
    echo "File: $mdbFilename\n";    //test
  
    $mdbConsorcios = $db->prepare('SELECT ' . $pasarTodo . ' * FROM consorcios ORDER BY codigo');
    $mdbConsorcios->execute();
    if ($mdbConsorcios) {
      $i = 0;
      while ($datos = $mdbConsorcios->fetch(PDO::FETCH_ASSOC)) {
        $datos['tipo_caja'] = $datos['tipo caja'];
        $datos = utf8_encode_array($datos);
        $envio = array(
          'accion' => INGRESAR_O_REEMPLAZAR,
          'datos' => $datos
        );

        echo '<br>';
        echo 'Consorcio: ' . $datos['codigo'];

        $respuestaConsorcios = funcEnviarDatosReclamos('https://www.creadoresdesoft.com.ar/reclamos/ABMConsorcios.php?slug=0JiMiR3Xz0Wbhx3YlJnIgojIkJ3XlJnYt0mbiACIgACIgACIgACIgoALiQXZu6ycvlmUlJHduVkIgojIhRnblV4Yf23buJCIgACIgACIgACIgAiCsIyb1lmbhNkIgojIvlmchV4c20Vbv6mIgACIgACIgACIgACIKwiI2gjIgojIvlmchV4c202by6mIgACIgACIgACIgACIKwiI1MjMxICI7ISZ3FGbjJCIgACIgACIgACIgAiCsICdl6mLz0WayVmc16WZAlGbv63ZhN3YiAiOiwWah2WZiACIgACIgACIgACIgowe', $envio);
        
        if ($respuestaConsorcios['resultado'] == CANCELAR) {
          throw new Exception($respuestaConsorcios['mensaje'] . $datos['codigo']);
        }
        
      }
    }  

    if ($pasarTodo == '') {
      $mdbClientes = $db->prepare('SELECT * FROM abonos where modify between (Date() - 30) and Date() ORDER BY numecli');
    } else {
      $mdbClientes = $db->prepare('SELECT ' . $pasarTodo . ' * FROM abonos ORDER BY numecli');
    }
    $mdbClientes->execute();

    if ($mdbClientes) {
      $i = 0;
      while ($datos = $mdbClientes->fetch(PDO::FETCH_ASSOC)) {
        $datos = utf8_encode_array($datos);
        $envio = array(
          'accion' => INGRESAR_O_REEMPLAZAR,
          'datos' => $datos
        );

        echo '<br>';
        echo 'Cliente: ' .$datos['numecli'];

        $respuestaClientes = funcEnviarDatosReclamos('https://www.creadoresdesoft.com.ar/reclamos/ABMClientes.php?slug=0JiMiR3Xz0Wbhx3YlJnIgojIkJ3XlJnYt0mbiACIgACIgACIgACIgoALiQXZu6ycvlmUlJHduVkIgojIhRnblV4Yf23buJCIgACIgACIgACIgAiCsIyb1lmbhNkIgojIvlmchV4c20Vbv6mIgACIgACIgACIgACIKwiI2gjIgojIvlmchV4c202by6mIgACIgACIgACIgACIKwiI1MjMxICI7ISZ3FGbjJCIgACIgACIgACIgAiCsICdl6mLz0WayVmc16WZAlGbv63ZhN3YiAiOiwWah2WZiACIgACIgACIgACIgowe', $envio);
        if ($respuestaClientes['resultado'] == CANCELAR) {
          throw new Exception($respuestaClientes['mensaje'] . $datos['numecli']);
        }
        
      }
    }  

    if (!$entreRiosNet) {
      $mdbFilename = $_SERVER["DOCUMENT_ROOT"] . '/reclamos/canito/mensajes.mdb';
      $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)}; Dbq=$mdbFilename;Uid=Admin");
    } else {
      $mdbFilename = 'c:\mensajes.mdb';
      $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)}; Dbq=$mdbFilename;Uid=Admin");
    }

    $sql = '
      SELECT 
        cliente as nro_cliente,
        fechacre + horacre as fecha_hora_creacion,
        85 as nro_usuario_creacion,
        observaciones as descripcion,
        fechalei + horalei as fecha_hora_asignado,
        85 as nro_usuario_asignado,
        fechaclu + horaclu as fecha_hora_resolucion,
        0 as nro_usuario_resolucion,
        observaciones2 as resolucion,
        numero
      FROM mensajes 
      where 
        fechacre between #2021-02-20 00:00:00# and  #2021-02-28 00:00:00#
      ORDER BY 
        fechacre desc 
    ';
    // echo $sql;
    $mdbReclamos = $db->prepare($sql);
  
    $mdbReclamos->execute();

    if ($mdbReclamos) {
      $i = 0;
      while ($datos = $mdbReclamos->fetch(PDO::FETCH_ASSOC)) {
        $datos = utf8_encode_array($datos);
        if ($datos['fechaclu'] != NULL and $datos['fechaclu'] != '') {
          $datos['nro_usuario_resolucion'] = 85;
        } else {
          $datos['nro_usuario_resolucion'] = 0;
        }
        $envio = array(
          'accion' => INGRESAR_O_REEMPLAZAR,
          'datos' => $datos
        );

        echo '<br>';
        echo 'Reclamo: ' . $datos['numero'];

        $respuestaReclamos = funcEnviarDatosReclamos('https://www.creadoresdesoft.com.ar/reclamos/ABMReclamos.php?slug=0JiMiR3Xz0Wbhx3YlJnIgojIkJ3XlJnYt0mbiACIgACIgACIgACIgoALiQXZu6ycvlmUlJHduVkIgojIhRnblV4Yf23buJCIgACIgACIgACIgAiCsIyb1lmbhNkIgojIvlmchV4c20Vbv6mIgACIgACIgACIgACIKwiI2gjIgojIvlmchV4c202by6mIgACIgACIgACIgACIKwiI1MjMxICI7ISZ3FGbjJCIgACIgACIgACIgAiCsICdl6mLz0WayVmc16WZAlGbv63ZhN3YiAiOiwWah2WZiACIgACIgACIgACIgowe', $envio);
        if ($respuestaReclamos['resultado'] == CANCELAR) {
          throw new Exception($respuestaReclamos['mensaje'] . $datos['numero']);
        }
        
      }
    }  
  
  } catch(PDOException $e) {
    echo $e->getMessage();
  }  
  function utf8_encode_array($array) {
    foreach($array as &$valor) {
      $valor = utf8_encode($valor);
    }
    return $array;
  }
?>