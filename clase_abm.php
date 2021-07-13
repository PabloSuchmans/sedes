<?php

include_once 'constantes.php';

class ABMC {

  public $clave_unica;                   // identifica la clave unica mediante la cual se ubica el registro de la tabla
  public $condicion_conjunto;            // 2020 04 06 se agrega la condicion para modificar un conjunto de registros MODIFICAR_CONJUNTO 
  public $nombre_tabla;                  // determina el nombre de la tabla de donde se sacaran los registros
  public $numero_registro;               // identifica el numero de registro al que se esta haciendo referencia
  public $campos = array();              // devuelve los campos de la tabla
  public $accion;                        // puede tener alguno de estos cuatro valores INGRESAR, ELIMINAR, MODIFICAR, CONSULTAR
  public $datos = array();
  public $campoOcultar = NULL;
  public $valorOcultar = TRUE;
  public $validar_mensajes = array();
  public $ver_SQL = FALSE;
  public $GuardarSinHistorial = FALSE;

  public function ultimoIdIngresado() {
    $sql = 'SELECT 
                AUTO_INCREMENT as ultimoID
              FROM information_schema.TABLES
              WHERE 
                TABLE_SCHEMA = "' . $this->enlace_bd->nombre_bd . '" and 
                TABLE_NAME = "' . $this->nombre_tabla . '"';
    $array = NULL;
    $result = mysqli_query($this->enlace_bd, $sql);
    if ($this->ver_SQL) {
      echo '<br><br>' . $sql . '<br><br>';   ///TEST
    }
    if ($result) {
      $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
      mysqli_free_result($result);
      return $line['ultimoID'];
    } else {
      return FALSE;
    }
        
  }

  public function mensajeValidacion($mensaje) {
    array_push($this->validar_mensajes, $mensaje);
  }

  public function validar() {}

  private function obtener_campos() {
    $resultado = mysqli_query($this->enlace_bd, 'SHOW COLUMNS FROM ' . $this->nombre_tabla);
    if (!$resultado) {
      echo '{"resultado":"' . CANCELAR . '","mensaje":"';
      echo 'No se pudo ejecutar la consulta: ' . mysqli_error($this->enlace_bd);
      echo '"}';
      exit;
    }
    if (mysqli_num_rows($resultado) > 0) {
      while ($fila = mysqli_fetch_assoc($resultado)) {
        $this->campos[$fila['Field']] = NULL;
      }
    }
  }

  public function ejecutar($datos_recibidos = NULL) {
    global $base_datos;
    // print_r('datos_reciboso');
    if ($datos_recibidos != NULL) {
      $this->datos = $datos_recibidos;
    }
    $this->accion = $this->datos['accion'];
    if (isset($this->datos['numero_registro'])) {
      $this->numero_registro = $this->datos['numero_registro'];
    }
    if ($this->accion === MODIFICAR_CONJUNTO or $this->accion === ELIMINAR_CONJUNTO) {
      if (isset($this->datos['condicion_conjunto'])) {
        $this->condicion_conjunto = $this->datos['condicion_conjunto'];
      }
    }
    // print_enter('obtener_campos');
    $this->obtener_campos();
    $this->validar_mensajes = array();
    $this->validar();
    // print_enter('valido');
    switch ($this->accion) {
      case INGRESAR:
        $lista_campos = '';
        $lista_valores = '';
        foreach ($this->campos as $nombre_campo => $valor_campo) {
          if ($nombre_campo === $this->campoOcultar) {
            $this->datos[$nombre_campo] = FALSE;
          }
          if (isset($this->datos['datos'][$nombre_campo])) {
            $lista_campos = $lista_campos . $nombre_campo . ',';
            $lista_valores = $lista_valores . "'" . $this->datos['datos'][$nombre_campo] . "',";
          }
        }
        $lista_campos = substr($lista_campos, 0, strlen($lista_campos) - 1);
        $lista_valores = substr($lista_valores, 0, strlen($lista_valores) - 1);
        $sql = 'INSERT INTO ' . $this->nombre_tabla . ' (' . $lista_campos . ') VALUES (' . $lista_valores . ')';
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case INGRESAR_O_REEMPLAZAR:
        $lista_campos = '';
        $lista_valores = '';
        $lista_campos_valores = '';
        foreach ($this->campos as $nombre_campo => $valor_campo) {
          if ($nombre_campo === $this->campoOcultar) {
            $this->datos[$nombre_campo] = FALSE;
          }
          if (isset($this->datos['datos'][$nombre_campo])) {
            $lista_campos_valores = $lista_campos_valores . $nombre_campo . "='" . mysqli_real_escape_string($this->enlace_bd, $this->datos['datos'][$nombre_campo]) . "',";
            $lista_campos = $lista_campos . $nombre_campo . ',';
            $lista_valores = $lista_valores . "'" . mysqli_real_escape_string($this->enlace_bd, $this->datos['datos'][$nombre_campo]) . "',";
          }
        }
        $lista_campos = substr($lista_campos, 0, strlen($lista_campos) - 1);
        $lista_valores = substr($lista_valores, 0, strlen($lista_valores) - 1);
        $lista_campos_valores = substr($lista_campos_valores, 0, strlen($lista_campos_valores) - 1);

        $sql = 'INSERT INTO ' . $this->nombre_tabla . ' (' . $lista_campos . ') VALUES (' . $lista_valores . ')' . 
                ' ON DUPLICATE KEY UPDATE ' . $lista_campos_valores;
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case ELIMINAR:
        if ($this->campoOcultar == NULL) {
          $sql = 'DELETE FROM ' . $this->nombre_tabla . ' WHERE ' . $this->clave_unica . ' = ' . $this->numero_registro;
        } else {
          $sql = 'UPDATE ' . $this->nombre_tabla . ' SET ' . $this->campoOcultar . "='" . $this->valorOcultar . "' WHERE " . $this->clave_unica . ' = ' . $this->numero_registro;
        }
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case ELIMINAR_CONJUNTO:
        if ($this->campoOcultar == NULL) {
          $sql = 'DELETE FROM ' . $this->nombre_tabla . ' WHERE ' . $this->condicion_conjunto;
        } else {
          $sql = 'UPDATE ' . $this->nombre_tabla . ' SET ' . $this->campoOcultar . "='" . $this->valorOcultar . "' WHERE " . $this->condicion_conjunto;
        }
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case MODIFICAR:
        $lista_campos_valores = '';
        foreach ($this->campos as $nombre_campo => $valor_campo) {
          if (isset($this->datos['datos'][$nombre_campo])) {
            $lista_campos_valores = $lista_campos_valores . $nombre_campo . "='" . $this->datos['datos'][$nombre_campo] . "',";
          }
        }
        $lista_campos_valores = substr($lista_campos_valores, 0, strlen($lista_campos_valores) - 1);
        $sql = 'UPDATE ' . $this->nombre_tabla . ' SET ' . $lista_campos_valores .
                ' WHERE ' . $this->clave_unica . " = '" . $this->numero_registro . "'";
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case MODIFICAR_CONJUNTO:
        $lista_campos_valores = '';
        foreach ($this->campos as $nombre_campo => $valor_campo) {
          if (isset($this->datos['datos'][$nombre_campo])) {
            $lista_campos_valores = $lista_campos_valores . $nombre_campo . "='" . $this->datos['datos'][$nombre_campo] . "',";
          }
        }
        $lista_campos_valores = substr($lista_campos_valores, 0, strlen($lista_campos_valores) - 1);
        $sql = 'UPDATE ' . $this->nombre_tabla . ' SET ' . $lista_campos_valores .
                ' WHERE ' . $this->condicion_conjunto;
        if ($this->ver_SQL) {
          echo '<br><br>' . $sql . '<br><br>';   ///TEST
        }
        break;
      case INICIALIZAR:
        $resultado['resultado'] = ACEPTAR;
        $resultado['mensaje'] = '';
        $resultado['clave_unica'] = $this->clave_unica;
        break;
    }
    // print_r('sql listo');
    if ($this->accion != INICIALIZAR) {
      if (!isset($sql)) {
        $this->mensajeValidacion('El metodo ' . $this->accion . ' no pertenece a esta clase');
      }
      if (sizeof($this->validar_mensajes) == 0) {
        $result = mysqli_query($this->enlace_bd, $sql);
        if ($result) {
          $resultado['resultado'] = ACEPTAR;
          $resultado['mensaje'] = '';
          // print_r('todo bien');
        } else {
          $resultado['resultado'] = CANCELAR;
          $resultado['mensaje'] = 'Sus datos NO fueron actualizados... error: ' .  mysqli_error($this->enlace_bd); 
          $resultado['mensaje'] = 'Sus datos NO fueron actualizados... error: ' .  mysqli_error($this->enlace_bd) . ' - sql: ' . $sql;    //test
        }
        if ($this->nombre_tabla != 'historial_acciones' and !$this->GuardarSinHistorial) {
          $base_datos->funcGuardarRegistroHistorial($this->accion . '[' . $this->nombre_tabla . ']', $sql);
        }
      } else {
        $resultado['resultado'] = VALIDAR;
        $resultado['validar_mensajes'] = $this->validar_mensajes;
      }
    }
    return $resultado;
  }
}

?>
