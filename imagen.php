<?php 
  ini_set('display_errors', 1);
  function funcCrearImagenTexto($nombreImagen,
                                $ancho,
                                $alto,
                                $txt,
                                $colorTexto = 'FFFFFF', 
                                $colorGlobo = '770000') {

    $im = imagecreatetruecolor($ancho, $alto);
    $im = imagecreatefrompng('circulolindodelphotoshop.png');

    $colorGlobo = hexdec($colorGlobo);
    $colorTexto = hexdec($colorTexto);
    $negro = imagecolorallocate($im, 0, 0, 0);
    // $colorTexto = imagecolorallocate($im, 255, 255, 255);
    print_r($colorTexto);

    // Hacer el fondo transparente
    imagecolortransparent($im, $negro);

    $fontname = realpath('fonts/RobotoMono/RobotoMono-VariableFont_wght.ttf');

    $tb = imagettfbbox(24, 0, $fontname, $txt);
    $x = ceil(($ancho - $tb[2]) / 2); // lower left X coordinate for text
    $tb = imagettfbbox(24, 0, $fontname, $txt);
    $y = ceil(($alto - $tb[7]) / 2); // lower left y coordinate for text

    // Dibujar un rectángulo rojo
    imagefilledellipse($im, $ancho / 2, $alto / 2, $ancho, $alto, $colorGlobo);

    // dibujar las letras
    imagettftext($im, 24, 0, $x, $y, $colorTexto, $fontname, $txt); 

    // Guardar la imagen
    imagepng($im, 'img/' . $nombreImagen . '.png');
    imagedestroy($im);
  }
  
  funcCrearImagenTexto($_GET['imagen'], $_GET['ancho'], $_GET['alto'], $_GET['texto'], $_GET['colorTexto'], $_GET['colorGlobo']);
?>  