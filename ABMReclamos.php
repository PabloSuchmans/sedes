<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_abm.php';
  include_once 'utilidades.php';

  class ReclamosRegistro extends ABMC {

    public function validar() {
      parent::validar();
      // $this->mensajeValidacion('mensajes de error');
    }
  }

  $Reclamos = new ReclamosRegistro();
  $Reclamos->enlace_bd = $base_datos->enlace_bd;
  $Reclamos->nombre_tabla = 'reclamos';
  $Reclamos->clave_unica = 'nro_reclamo';

  $datos_recibidos = obtenerDatosPOST();
  $nro_estado = 0;

  if ($datos_recibidos['accion'] == INGRESAR) {
    $datos_recibidos['datos']['nro_usuario_creacion'] = $base_datos->nro_usuario;
  }

  if ($datos_recibidos['datos']['nro_usuario_asignado'] == 0) {
    $datos_recibidos['datos']['fecha_hora_asignado'] = 0;
  } else {
    $datos_recibidos['datos']['fecha_hora_asignado'] = date("Y-m-d h:i:sa");
    $nro_estado = 2;
  }

  if ($datos_recibidos['datos']['nro_usuario_resolucion'] == 0) {
    $datos_recibidos['datos']['fecha_hora_resolucion'] = 0;
  } else {
    $datos_recibidos['datos']['fecha_hora_resolucion'] = date("Y-m-d h:i:sa");
    $nro_estado = 3;
  }
  // $Reclamos->ver_SQL = 1;
  $respuestaReclamos = $Reclamos->ejecutar($datos_recibidos);
  // print_r($respuestaReclamos);
  if ($respuestaReclamos['resultado'] == ACEPTAR) {
    // print_r($nro_estado);

    $Usuario = new Info();
    $Usuario->enlace_bd = $base_datos->enlace_bd;
    if ($nro_estado == 2) {
      $Usuario->sql = 'select * from usuarios where nro_usuario = @nro_usuario';
      $Usuario->agregarParametros('nro_usuario', $datos_recibidos['datos']['nro_usuario_asignado']);
    } else {
      $Usuario->sql = 'select * from usuarios';
    }        
    $respuestaUsuario = $Usuario->ejecutar();
    if ($respuestaUsuario['resultado'] == ACEPTAR and $respuestaUsuario['hayDatos']) {

      foreach($respuestaUsuario['hayDatos'] as $notificacionUsuario) {
        if ($datos_recibidos['accion'] == INGRESAR) {
          $mensajeNotificacion = $datos_recibidos['datos']['descripcion'];
        } else {
          $consultaReclamo = new Info();
          $consultaReclamo->enlace_bd = $base_datos->enlace_bd;
          $consultaReclamo->sql = 'select * from reclamos where nro_reclamo = @nro_reclamo';
          $consultaReclamo->agregarParametros('nro_reclamo', $datos_recibidos['numero_registro']);
          $respuestaConsultaReclamo = $consultaReclamo->ejecutar();
          $mensajeNotificacion = $respuestaConsultaReclamo['datos'][0]['descripcion'];
        }
        $respuestaReclamos['notificacion'] =
          funcEnviarNotificacion(
            array($notificacionUsuario['notificacion_id']),
            $mensajeNotificacion,
            'Tenes un nuevo mensaje'
          );
      }
    }
  }
  
  echo funcRespuesta($respuestaReclamos);
?>
