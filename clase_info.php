<?php
include_once 'constantes.php';

// $LocalidadesInfo->enlace_bd = $base_datos->enlace_bd;

class Info {

  public $clave_unica = '';                   // identifica la clave unica mediante la cual se ubica el registro de la tabla
  public $numero_registro;               // identifica el numero de registro al que se esta haciendo referencia
  public $sql = '';
  public $orden = '';
  public $enlace_bd; // = $base_datos->enlace_bd;
  public $datos = FALSE;
  public $ultimo_resultado = FALSE;
  public $ver_SQL = FALSE;
  public $params = array();

  public function funcLeerSQL($archivoSQL = '') {
    $this->sql = implode('', file($archivoSQL));
  }

  public function ejecutar($datos_recibidos = '') {
    $muestra_consulta = false;
    if ($this->sql === '') {
      $resultado['resultado'] = CANCELAR;
      $resultado['mensaje'] = 'No hay sql para hacer la consulta';
    } else {
      if (isset($datos_recibidos['numero_registro'])) {
        $this->numero_registro = $datos_recibidos['numero_registro'];
        if (isset($datos_recibidos['clave_unica'])) {
          $this->clave_unica = $datos_recibidos['clave_unica'];
          $muestra_consulta = true;
        } else {
          $resultado['resultado'] = CANCELAR;
          $resultado['hayDatos'] = FALSE;
          $resultado['mensaje'] = 'No se puede consultar un registro sin la clave_unica';
        }
      } else {
        $muestra_consulta = true;
      }
      if ($muestra_consulta === true) {
        $resultado = $this->datos();
      }
    }
    return $resultado;
  }

  public function agregarParametros($campo, $valor) {
    $this->params['@' . $campo] = "'" . $valor . "'";
  }

  public function datos() {
    global $base_datos;
    $sqlFinal = $this->sql;
    if ($this->clave_unica != '') {
      if (stripos($sqlFinal, 'where') > 0) {
        $and = ' and ';
      } else {
        $and = ' where ';
      }

      $sqlFinal .= $and . $this->clave_unica . '=' . $this->numero_registro;
    }

    if (count($this->params) != 0) {
      $sqlFinal = str_replace(array_keys($this->params), $this->params, $sqlFinal);
    };
    
    if ($this->ver_SQL) {
      echo $sqlFinal;
    }
    
    $array = NULL;
    $result = mysqli_query($this->enlace_bd, $sqlFinal);
    if ($result) {
      while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $array[] = $line;
      }
      mysqli_free_result($result);
    }
    $this->datos = NULL;
    $this->noHayDatos = TRUE;
    if ($array === NULL) {
      if (mysqli_error($this->enlace_bd)) {
        $resultado['resultado'] = CANCELAR;
        $resultado['mensaje'] = 'Fallo sql';
        //$resultado['mensaje'] = 'fallo sql: ' . $this->sql . ', error: ' . mysqli_error($this->enlace_bd);   //test
      } else {
        $resultado['resultado'] = ACEPTAR;
        $resultado['mensaje'] = 'No hay registros para la sql ';
        // $resultado['mensaje'] = 'No hay registros para la sql ' . $sqlFinal;   //test
      }
      $resultado['hayDatos'] = FALSE;
    } else {
      $this->noHayDatos = FALSE;
      $resultado['resultado'] = ACEPTAR;
      $resultado['hayDatos'] = TRUE;
      $resultado['datos'] = $array;
      $this->datos = $array;
    }
    $this->ultimo_resultado = $resultado;
    return $resultado;
  }
}
?>