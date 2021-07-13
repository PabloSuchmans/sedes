<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $ParametrosInfo = new Info();
  $ParametrosInfo->enlace_bd = $base_datos->enlace_bd;

  $datos_recibidos = obtenerDatosPOST();

  $ParametrosInfo->sql = '
    select 
        *
      from parametros
        ';

  echo funcRespuesta($ParametrosInfo->ejecutar($datos_recibidos));

?>
