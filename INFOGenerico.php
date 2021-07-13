<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $GenericoInfo = new Info();
  $GenericoInfo->enlace_bd = $base_datos->enlace_bd;

  $datos_recibidos = obtenerDatosPOST();

  $GenericoInfo->sql = 'SELECT AUTO_INCREMENT FROM information_schema.TABLES
  WHERE 
    TABLE_SCHEMA = "' . $base_datos->nombre_bd . '" and 
    TABLE_NAME = "reservas"';
  
  echo funcRespuesta($GenericoInfo->ejecutar($datos_recibidos));

?>
