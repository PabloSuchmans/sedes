<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'clase_abm.php';
  include_once 'utilidades.php';

  $ParametrosInfo = new Info();
  $ParametrosInfo->enlace_bd = $base_datos->enlace_bd;
  $ParametrosInfo->sql = 'select * from parametros';
  // $ParametrosInfo->ver_SQL=1;
  $respuestaParametros = $ParametrosInfo->ejecutar();

  $Usuarios = new ABMC();
  $Usuarios->enlace_bd = $base_datos->enlace_bd;
  $Usuarios->nombre_tabla = 'usuarios';
  $Usuarios->clave_unica = 'nro_usuario';

  $datos_recibidos = obtenerDatosPOST();

  // $datos_recibidos['numero_registro'] = $nro_usuario;
  $datos_recibidos['nro_cuenta'] = $respuestaParametros['datos'][0]['nro_cuenta'];
  $datos_recibidos['datos']['cuenta'] = '';
  if ($datos_recibidos['accion'] == INGRESAR) {
    $datos_recibidos['datos']['activo'] = TRUE;
  }
    
  $respuestaUsuarios = funcEnviarDatosReclamos('https://www.creadoresdesoft.com.ar/reclamos_adm/ABMUsuarios.php?slug=Vdsfas5rdffghd45gaqewqr35regdqdsfdfsgq4rG8gYmUgbXlzcWxpLCBib29sZWFuIGdpdmVuIGlu', $datos_recibidos);
  // print_r($datos_recibidos);
  if ($respuestaUsuarios['resultado'] == ACEPTAR) {
    if (isset($datos_recibidos['datos']['nombre'])) {
      funcCrearImagenTexto('user-' . 
            $respuestaParametros['datos'][0]['nro_cuenta'] . '-' . $datos_recibidos['datos']['nro_usuario'], 
            45, 
            45, 
            funcObtenerIniciales($datos_recibidos['datos']['nombre']), 
            'FFFFFF', 
            'FF0000' 
      );
    }
    $datos_recibidos['datos']['nro_usuario'] = $respuestaUsuarios['datos']['nro_usuario'];
    // $Usuarios->ver_SQL = 1;
    $respuesta = $Usuarios->ejecutar($datos_recibidos);
    if ($respuesta['resultado'] == ACEPTAR) {
      echo funcRespuesta($respuestaUsuarios);
    }    
  } else {
    echo funcRespuesta($respuestaUsuarios);
  }

?>
