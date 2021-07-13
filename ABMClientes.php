<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_abm.php';
  include_once 'utilidades.php';

  class ClientesRegistro extends ABMC {

    public function validar() {
      parent::validar();
      // $this->mensajeValidacion('mensajes de error');
    }
  }

  $Clientes = new ClientesRegistro();
  $Clientes->enlace_bd = $base_datos->enlace_bd;
  $Clientes->nombre_tabla = 'clientes';
  $Clientes->clave_unica = 'nro_cliente';

  $datos_recibidos = obtenerDatosPOST();
  // se espera un json similar a este 
  // {"accion":"ingresar","numero_registro":"0","clave_unica":"nro_tipo_habitacion","datos":{"nombre":"Horona Cafrune","stock":"3","mayores":"2","menores":"1"}}

  echo funcRespuesta($Clientes->ejecutar($datos_recibidos));

?>
