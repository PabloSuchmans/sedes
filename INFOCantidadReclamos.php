<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $CantidadReclamosInfo = new Info();
  $CantidadReclamosInfo->enlace_bd = $base_datos->enlace_bd;

  $datos_recibidos = obtenerDatosPOST();

  $CantidadReclamosInfo->sql = '
    select  
      (select 
          count(*) 
        from reclamos 
        where 
          reclamos.nro_usuario_resolucion = 0 and  
          reclamos.nro_usuario_asignado = 0) as cantidad_pendientes,
      (select 
          count(*) 
        from reclamos 
        where 
          reclamos.nro_usuario_resolucion = 0 and  
          reclamos.nro_usuario_asignado = @nro_usuario) as cantidad_asignados,
      (select 
          count(*) 
        from reclamos 
        where 
          reclamos.nro_usuario_resolucion = @nro_usuario and 
          date(fecha_hora_resolucion) = CURRENT_DATE) as cantidad_resueltos    
  ';

  $CantidadReclamosInfo->agregarParametros('nro_usuario', $base_datos->nro_usuario);
  // $CantidadReclamosInfo->ver_SQL = 1;
  
  echo funcRespuesta($CantidadReclamosInfo->ejecutar($datos_recibidos));

?>
