<?php
  date_default_timezone_set('America/Argentina/Buenos_Aires');
  include_once 'constantes.php';
  include_once 'utilidades.php';
  $getslug = json_decode(base64_decode(funcEmpaquetarCadena('D' . $_GET['slug'])), TRUE);
  // $getslug = array('nombre_bd' => 'reclamos_db');
  if (!isset($getslug['ip'])) {
    $getslug['ip'] = '192.168.0.11';
  }
  if (isset($getslug['latitude']) and isset($getslug['longitude'])) {
    $getslug['geolocation'] = $getslug['latitude'] . ','. $getslug['longitude'];
  } else {
    $getslug['geolocation'] = '-33.002343, -58.540249';
  }
  // print('<pre>'.print_r($getslug, true).'</pre>');   //test
  class Conexion {

    public $servidor;
    public $usuario;
    public $password;
    public $nombre_bd;
    public $nombre_bd_original = 'reclamos_db';
    public $enlace_bd;
    public $nro_usuario;
    public $ip;
    public $geolocation;

    public function conectar($sesion_iniciada = NULL) {
      if (funcConexionPermitida()) {
        $this->enlace_bd = mysqli_connect($this->servidor, $this->usuario, $this->password, $this->nombre_bd_original);
        mysqli_set_charset($this->enlace_bd, "utf8");
        $this->enlace_bd->nombre_bd = $this->nombre_bd_original;
        $this->funcActualizarBD($this->enlace_bd);

        $this->enlace_bd = mysqli_connect($this->servidor, $this->usuario, $this->password, $this->nombre_bd);
        mysqli_set_charset($this->enlace_bd, "utf8");
        $this->enlace_bd->nombre_bd = $this->nombre_bd;
        $this->funcActualizarBD($this->enlace_bd);

      } else {
        $this->enlace_bd = FALSE;
      }
      if (!$this->enlace_bd) {
        return FALSE;
      } else {
        return TRUE;
      }
    }

    public function comenzar_transaccion() {
      mysqli_begin_transaction($this->enlace_bd, MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT);  
      // investigar
      // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
  
    public function commit_transaccion() {
      mysqli_commit($this->enlace_bd);    
    }
  
    public function rollback_transaccion() {
      mysqli_rollback($this->enlace_bd);    
    }
  
    public function desconectar() {
      mysqli_close($this->enlace_bd);
    }

    public function funcGuardarRegistroHistorial($nombreComando, $sql) {
      try {
        $Historial = new ABMC();
        $Historial->enlace_bd = $this->enlace_bd;
        $Historial->nombre_tabla = 'historial_acciones';

        $enviar = array(
          'accion' => INGRESAR,
          'datos' => array(
            'nro_usuario' => $this->nro_usuario, 
            'ip' => $this->ip, 
            'comando' => $nombreComando, 
            'coordenadas' => $this->geolocation, 
            'consulta' => mysqli_real_escape_string($this->enlace_bd, $sql)
          )
        );
        $recibido = $Historial->ejecutar($enviar);
      } catch (Exception $e) {
        // echo $e->getMessage();
      }
    }

    public function funcActualizarBD($actualizarEnlace_bd) {
      $Actualizaciones = new ABMC();
      $Actualizaciones->enlace_bd = $actualizarEnlace_bd;
      $Actualizaciones->nombre_tabla = 'actualizaciones';
      $Actualizaciones->clave_unica = 'nro_actualizacion';

      $ActualizacionesInfo = new Info();
      $ActualizacionesInfo->enlace_bd = $actualizarEnlace_bd;
      $ActualizacionesInfo->sql = '
          select 
              *
            from actualizaciones
            where 
              actualizaciones.nombre_archivo = @nombreArchivo
        ';
      foreach (glob("actualizaciones/*.sql") as $nombreArchivo) {
        // $nombreArchivo = str_replace($nombreArchivo, 'actualizaciones/')
        $ActualizacionesInfo->agregarParametros('nombreArchivo', $nombreArchivo);
        // $ActualizacionesInfo->ver_SQL = 1;    //test
        $ActualizacionesInfo->ejecutar();
        // print_r($ActualizacionesInfo->datos);    //test
        if (!$ActualizacionesInfo->datos) {
          // echo '<br>Nombre Archivo = ' . $nombreArchivo . '<br>';    //test
          $script = file_get_contents($nombreArchivo);
          // echo '<br>SCRIPT ' . $script . '<br>';   //test
          $i = 0;
          $errores = '';          
          try {
            if ($actualizarEnlace_bd->multi_query($script)) {
              do {
                // echo 'reviso errores<br>';
                if ($result = $actualizarEnlace_bd->store_result()) {
                    while ($row = $result->fetch_row()) {
                      // echo 'el error es ' . $row[0] . '<br>';
                      // printf("%s\n", $row[0]);
                    }
                    $result->free();
                }
                if ($actualizarEnlace_bd->more_results()) {
                  // printf("-----------------\n");
                }                
              } while ($actualizarEnlace_bd->next_result());
              $errores = '';
            } else {
              $errores = "Falló la multiconsulta: (" . $actualizarEnlace_bd->errno . ") " . $actualizarEnlace_bd->error;
            } 
            if ($errores != '') {
              throw new Exception($errores);
            } else {
              $resultado = array('resultado' => ACEPTAR);
            }          
          } catch (Exception $e) {
            $resultado = array('resultado' => CANCELAR, 'mensaje' => $e);
            if ($nombreArchivo == '') {
              mail('suchmanspablo@gmail.com', $actualizarEnlace_bd->nombre_bd . ': ' . $nombreArchivo, $e);            
            }
          }
          $enviar = array(
            'accion' => INGRESAR,
            'datos' => array(
              'nombre_archivo' => $nombreArchivo,
              'resultado' => mysqli_real_escape_string($actualizarEnlace_bd, $errores)
            )
          );
          // print_r($resultado);  //test
          // $Actualizaciones->ver_SQL = 1;  //test
          $Actualizaciones->ejecutar($enviar);
          // print_r('----------------------------------------------------------');    //test
        }
      }       
    
          
    }
  }


  $base_datos = new Conexion();
  $base_datos->servidor = 'localhost';
  if (funcEjecucionLocal()) {
    $base_datos->usuario = 'root';
    $base_datos->password = '';
  } else {
    $base_datos->usuario = 'c1web';
    $base_datos->password = 'vo!eT4iDo4SW';
  }
  $base_datos->nombre_bd = $getslug['nombre_bd'];

  include_once 'clase_abm.php';
  include_once 'clase_info.php';

  $base_datos->conectar();

  $base_datos->nro_usuario = $getslug['nro_usuario'];
  $base_datos->ip = $getslug['ip'];
  $base_datos->geolocation = $getslug['geolocation'];

  $UsuariosABM = new ABMC();
  $UsuariosABM->enlace_bd = $base_datos->enlace_bd;
  $UsuariosABM->nombre_tabla = 'usuarios';
  $UsuariosABM->clave_unica = 'nro_usuario';

  $UsuariosInfo = new Info();
  $UsuariosInfo->enlace_bd = $base_datos->enlace_bd;

  $UsuariosInfo->sql = 'select nro_usuario from usuarios' .
                      ' where email=\'' . $getslug['email'] . '\'';

  $respuestaUsuarios = $UsuariosInfo->ejecutar('');
  if ($respuestaUsuarios['resultado'] == ACEPTAR) {
    if (!$respuestaUsuarios['hayDatos']) {
      $envio = array(
        "accion" => INGRESAR,
        "datos" => array(
          "nro_usuario" => $getslug['nro_usuario'],
          "nombre" => $getslug['nom_usuario'],
          "email" => $getslug['email']
        )
      );
    }

    $UsuariosABM->ejecutar($envio);
  }


?>
