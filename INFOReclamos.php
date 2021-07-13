<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $ReclamosInfo = new Info();
  $ReclamosInfo->enlace_bd = $base_datos->enlace_bd;

  $cantidadRegistros = 50;

  $datos_recibidos = obtenerDatosPOST();

  $ReclamosInfo->funcLeerSQL('SQLReclamos.sql');
  $consultaIndividual = (
    isset($datos_recibidos['numero_registro']) or 
    isset($datos_recibidos['clave_unica'])
  );

  $consultaConWhere = (isset($datos_recibidos['fechaDesde']) or 
      isset($datos_recibidos['fechaHasta']) or 
      isset($datos_recibidos['nro_cliente']) or 
      isset($datos_recibidos['nro_estado']) or 
      isset($datos_recibidos['nro_usuario'])
  );
  if (!isset($datos_recibidos['nro_pagina'])) {
    $datos_recibidos['nro_pagina'] = -1;
  }
  if ($consultaConWhere) {
    $ReclamosInfo->sql = $ReclamosInfo->sql . ' where ';
  }



  switch ($datos_recibidos['consulta']) {
    case 'operador':
      $and = FALSE;
      if (isset($datos_recibidos['nro_estado']) and count($datos_recibidos['nro_estado']) != 0) {
        $ReclamosInfo->sql = $ReclamosInfo->sql . "
            case 
              when 
                reclamos.nro_usuario_resolucion <> 0 then 3  
              when 
                (reclamos.nro_usuario_resolucion = 0) and  
                (reclamos.nro_usuario_asignado <> 0) then 2
              when 
                (reclamos.nro_usuario_resolucion = 0) and  
                (reclamos.nro_usuario_asignado = 0) then 1
              end in (" . implode(",", $datos_recibidos['nro_estado']) . ")
        ";   //1 = no asignado, 2 = Asignado y no resuelto, 3 = Resuelto
        // $ReclamosInfo->agregarParametros('nro_estado', $datos_recibidos['nro_estado']);
        $and = TRUE;
      }
          if (isset($datos_recibidos['fechaDesde']) and isset($datos_recibidos['fechaHasta'])) {
        if ($and) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . ' and ';
        }
        $ReclamosInfo->sql = $ReclamosInfo->sql . '
              date(fecha_hora_creacion) between @fechaDesde and @fechaHasta
        ';
        $ReclamosInfo->agregarParametros('fechaDesde', $datos_recibidos['fechaDesde']);
        $ReclamosInfo->agregarParametros('fechaHasta', $datos_recibidos['fechaHasta']);
        $and = TRUE;
      }

      if (isset($datos_recibidos['nro_cliente']) and $datos_recibidos['nro_cliente'] != 0) {
        if ($and) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . ' and ';
        }
        $ReclamosInfo->sql = $ReclamosInfo->sql . ' reclamos.nro_cliente = @nro_cliente ';
        $ReclamosInfo->agregarParametros('nro_cliente', $datos_recibidos['nro_cliente']);
        $and = TRUE;
      }

      if (isset($datos_recibidos['nro_usuario'])) {
        if ($and) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . ' and ';
        }
        if (count($datos_recibidos['nro_estado']) == 0) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . '
            (
              reclamos.nro_usuario_creacion = @nro_usuario or
              reclamos.nro_usuario_asignado = @nro_usuario or
              reclamos.nro_usuario_resolucion = @nro_usuario
            )
          ';
        } else {
          // print_r($datos_recibidos['nro_estado']);
          $or = FALSE;
          $filtrarEstadosUsuarios = '';
          if (in_array(1, $datos_recibidos['nro_estado'])) {  //reclamos que han sido creados por un usuario
            $filtrarEstadosUsuarios = $filtrarEstadosUsuarios . ' reclamos.nro_usuario_creacion = @nro_usuario ';
            $or = TRUE;
          }
          if (in_array(2, $datos_recibidos['nro_estado'])) {  //reclamos que han sido asignados a un usuario
            if ($or) {
              $filtrarEstadosUsuarios = $filtrarEstadosUsuarios  . ' or ';
            }
            $filtrarEstadosUsuarios = $filtrarEstadosUsuarios  . ' reclamos.nro_usuario_asignado = @nro_usuario ';
            $or = TRUE;
          }
          if (in_array(3, $datos_recibidos['nro_estado'])) {  //reclamos que han sido creados por un usuario
            if ($or) {
              $filtrarEstadosUsuarios = $filtrarEstadosUsuarios  . ' or ';
            }
            $filtrarEstadosUsuarios = $filtrarEstadosUsuarios  . ' reclamos.nro_usuario_resolucion = @nro_usuario ';
            $or = TRUE;
          }
          if ($or) {
            $ReclamosInfo->sql = $ReclamosInfo->sql . '(' . $filtrarEstadosUsuarios . ') ';
          }
        }
        $ReclamosInfo->agregarParametros('nro_usuario', $datos_recibidos['nro_usuario']);
        $and = TRUE;
      }
      break;
    case 'app':
      $and = FALSE;
      if (isset($datos_recibidos['nro_estado']) and $datos_recibidos['nro_estado'] != 0) {
        $ReclamosInfo->sql = $ReclamosInfo->sql . "
            case 
              when 
                reclamos.nro_usuario_resolucion <> 0 then 3  
              when 
                (reclamos.nro_usuario_resolucion = 0) and  
                (reclamos.nro_usuario_asignado <> 0) then 2
              when 
                (reclamos.nro_usuario_resolucion = 0) and  
                (reclamos.nro_usuario_asignado = 0) then 1
            end = @nro_estado
        ";   //1 = no asignado, 2 = Asignado y no resuelto, 3 = Resuelto
        $ReclamosInfo->agregarParametros('nro_estado', $datos_recibidos['nro_estado']);
        $and = TRUE;
      }
      if (isset($datos_recibidos['nro_estado']) and $datos_recibidos['nro_estado'] == 2) {
        if ($and) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . ' and ';
        }
        $ReclamosInfo->sql = $ReclamosInfo->sql . '
          reclamos.nro_usuario_asignado = @nro_usuario
        ';
        $ReclamosInfo->agregarParametros('nro_usuario', $base_datos->nro_usuario);
        $and = TRUE;
      }

      if (isset($datos_recibidos['nro_estado']) and $datos_recibidos['nro_estado'] == 3) {
        if ($and) {
          $ReclamosInfo->sql = $ReclamosInfo->sql . ' and ';
        }
        $ReclamosInfo->sql = $ReclamosInfo->sql . '
          reclamos.nro_usuario_resolucion = @nro_usuario and
          date(reclamos.fecha_hora_resolucion) = CURRENT_DATE
          ';
        $ReclamosInfo->agregarParametros('nro_usuario', $base_datos->nro_usuario);
        $and = TRUE;
      }
      break;
  }

  if (!$consultaIndividual) {
    if ($datos_recibidos['nro_estado'] == 3 or in_array(3, $datos_recibidos['nro_estado'])) {
      $ReclamosInfo->sql = $ReclamosInfo->sql . ' 
        order by 
          reclamos.fecha_hora_creacion desc
      ';
    } else {
      $ReclamosInfo->sql = $ReclamosInfo->sql . ' 
        order by 
          reclamos.fecha_hora_creacion asc
      ';
    }
    if ($datos_recibidos['nro_pagina'] >= 0) {
      $ReclamosInfo->sql = $ReclamosInfo->sql . ' limit ' . (($datos_recibidos['nro_pagina'] + 1) * $cantidadRegistros);
    }
  }

    // $ReclamosInfo->ver_SQL = 1;  
  $respuestaReclamos = $ReclamosInfo->ejecutar($datos_recibidos);

  if ($datos_recibidos['nro_pagina'] >= 0) {
    $respuestaReclamos['nro_pagina'] = $datos_recibidos['nro_pagina'];
    // if (count($respuestaReclamos['datos']) != ($cantidadRegistros + 1)) {
    //   $respuestaReclamos['masPaginas'] = FALSE;
    // } else {
    //   $respuestaReclamos['masPaginas'] = TRUE;
    //   unset($respuestaReclamos['datos'][$cantidadRegistros]);
    // }
  }
  // $respuestaReclamos['sql'] = $ReclamosInfo->sql;
  echo funcRespuesta($respuestaReclamos);

?>
