<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_info.php';
  include_once 'utilidades.php';

  $ClientesInfo = new Info();
  $ClientesInfo->enlace_bd = $base_datos->enlace_bd;

  $datos_recibidos = obtenerDatosPOST();

  $ClientesInfo->sql = 'SELECT * from clientes';

  if (isset($datos_recibidos['buscarNombre'])) {
    if (strlen($datos_recibidos['buscarNombre']) < 3) {
      $datos_recibidos['buscarNombre'] = '._.';
    }
    if (!isset($datos_recibidos['mostrarBajas'])) {
      $datos_recibidos['mostrarBajas'] = 0;
    }
    unset($datos_recibidos['clave_unica']);
    unset($datos_recibidos['numero_registro']);
    $ClientesInfo->sql = '
      select 
          clientes.apellido as nombre_cli, 
          clientes.calle as direccion_cli, 
          clientes.tel as telefono_cli, 
          clientes.IP as ip_cli, 
          clientes.MAC as mac_cli,
          consorcios.codigo as codigo_con,
          consorcios.descripcion as descripcion_con,
          consorcios.usuario as usuario_con,
          consorcios.telefono as telefono_con,
          consorcios.direccion as direccion_con,
          consorcios.tipoacceso as tipoacceso_con,
          consorcios.usuario as usuario_con,
          consorcios.password as password_con,
          consorcios.modem as modem_con,
          clientes.numecli as nro_cliente
        from clientes 
        inner join consorcios on consorcios.codigo = clientes.codigo_consor
        where 
          clientes.apellido like @buscarNombre and 
          (@mostrarBajas or baja = 0)
    ';
    //$ClientesInfo->ver_SQL = 1;
    $ClientesInfo->agregarParametros('buscarNombre', '%' . $datos_recibidos['buscarNombre'] . '%');
    $ClientesInfo->agregarParametros('mostrarBajas', $datos_recibidos['mostrarBajas']);
  }

  
  echo funcRespuesta($ClientesInfo->ejecutar($datos_recibidos));

?>
