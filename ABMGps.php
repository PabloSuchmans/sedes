<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_abm.php';
  include_once 'utilidades.php';

  $Gps = new ABMC();
  $Gps->enlace_bd = $base_datos->enlace_bd;
  $Gps->nombre_tabla = 'usuarios';
  $Gps->clave_unica = 'nro_usuario';

  $datos_recibidos = obtenerDatosPOST()[0];
  // print_r($datos_recibidos);
  // exit;
  $datos_recibidos['ultima_actualizacion'] = date('Y-m-d H:i:s');
  $envio = array(
    'accion' => MODIFICAR,
    'clave_unica' => 'nro_usuario',
    'numero_registro' => $datos_recibidos['nro_usuario'],
    'datos' => $datos_recibidos
    );

  echo funcRespuesta($Gps->ejecutar($envio));

?>
