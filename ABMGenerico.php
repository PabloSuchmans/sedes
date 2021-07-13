<?php
  include_once 'clase_conectar_bd.php';
  include_once 'clase_abm.php';
  include_once 'utilidades.php';

  class GenericoRegistro extends ABMC {

    public function validar() {
      parent::validar();
      // $this->mensajeValidacion('mensajes de error');
    }
  }
// EJEMPLO DE COMO HACER UNA TRANSACCION
  // $respuesta = array('resultado' => ACEPTAR, 'mensaje' => '');
  // try {
  //   $base_datos->comenzar_transaccion();
  //   foreach($datos_recibidos as $RegistroTemporada) {
  //     if (isset($RegistroTemporada['editado'])) {
  //       if ($RegistroTemporada['editado'] !== 0) {
  //         $enviar = array(
  //           'accion' => MODIFICAR,
  //           'numero_registro' => $RegistroTemporada['fecha'],
  //           'datos' => array(
  //             'nro_temporada' => $RegistroTemporada['nro_temporada']
  //           )
    
  //         );
  //         $recibido = $TiposHabitacionesFecha->ejecutar($enviar);
  //       }
  //     }
  //   }
  //   $base_datos->commit_transaccion();
  // } catch (exception $e) {
  //   $base_datos->rollback_transaccion();
  //   $respuesta = array('resultado' => CANCELAR, 'mensaje' => $e);
  // }

  $Generico = new GenericoRegistro();
  $Generico->enlace_bd = $base_datos->enlace_bd;
  $Generico->nombre_tabla = 'nombre_tabla';
  $Generico->clave_unica = 'nro_clave_unica';

  $datos_recibidos = obtenerDatosPOST();
  // se espera un json similar a este 
  // {"accion":"ingresar","numero_registro":"0","clave_unica":"nro_tipo_habitacion","datos":{"nombre":"Horona Cafrune","stock":"3","mayores":"2","menores":"1"}}

  echo funcRespuesta($Generico->ejecutar($datos_recibidos));

?>
