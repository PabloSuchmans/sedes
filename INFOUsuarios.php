<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $UsuariosInfo = new Info();
  $UsuariosInfo->enlace_bd = $base_datos->enlace_bd;

  $datos_recibidos = obtenerDatosPOST();
  if (!isset($datos_recibidos['clave_unica']) and !isset($datos_recibidos['numero_registro'])) {
    $UsuariosInfo->sql = '

      select 
          nro_usuario, 
          nombre,
          activo,
          lon,
          lat,
          ultima_actualizacion,
          version_app,
          (select 
              count(*) 
            from reclamos 
            where 
              reclamos.nro_usuario_resolucion = 0 and  
              reclamos.nro_usuario_asignado = usuarios.nro_usuario) as cantidad_asignados,
          concat("' . $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME'] . '/reclamos/img/user-", (select nro_cuenta from parametros), "-", usuarios.nro_usuario, ".png") as url_usuario_mapa
        from usuarios 
        where 
          @verTodos <> 0 or
          activo <> 0
      union all
      select 
          0, 
          "< Ninguno >",
          0,
          "",
          "",
          "",
          "",
          0,
          ""
      order by 2
    ';
  } else {
    $UsuariosInfo->sql = 'select * from usuarios';
  }
  $UsuariosInfo->agregarParametros('verTodos', $datos_recibidos['verTodos']);
  // $UsuariosInfo->ver_SQL=1;
  echo funcRespuesta($UsuariosInfo->ejecutar($datos_recibidos));

?>
