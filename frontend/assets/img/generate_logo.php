<?php
// Script para generar un logo de muestra para la universidad

// Establecer el tipo de contenido como imagen PNG
header('Content-Type: image/png');

// Crear imagen
$width = 400;
$height = 200;
$im = imagecreatetruecolor($width, $height);

// Colores
$blue = imagecolorallocate($im, 0, 102, 204);
$white = imagecolorallocate($im, 255, 255, 255);
$gold = imagecolorallocate($im, 255, 215, 0);
$darkblue = imagecolorallocate($im, 0, 51, 153);

// Fondo
imagefilledrectangle($im, 0, 0, $width, $height, $darkblue);

// Círculo central
imagefilledellipse($im, $width/2, $height/2, 150, 150, $blue);
imagefilledellipse($im, $width/2, $height/2, 130, 130, $white);

// Texto de la universidad
$text = "UNIVERSIDAD";
$font = 5; // Usar una fuente predefinida
$textwidth = imagefontwidth($font) * strlen($text);
imagestring($im, $font, ($width - $textwidth) / 2, 60, $text, $blue);

$text = "TECNOLÓGICA";
$textwidth = imagefontwidth($font) * strlen($text);
imagestring($im, $font, ($width - $textwidth) / 2, 80, $text, $blue);

$text = "EST. 1985";
$textwidth = imagefontwidth($font) * strlen($text);
imagestring($im, $font, ($width - $textwidth) / 2, 120, $text, $gold);

// Dibujar un libro abierto en el centro
$bookpoints = [
    $width/2 - 40, $height/2,
    $width/2 + 40, $height/2,
    $width/2 + 35, $height/2 + 20,
    $width/2 - 35, $height/2 + 20
];
imagefilledpolygon($im, $bookpoints, 4, $gold);

// División del libro
imageline($im, $width/2, $height/2, $width/2, $height/2 + 20, $blue);

// Guardar la imagen al archivo
imagepng($im, __DIR__ . '/universidad_logo.png');

// Mostrar la imagen
imagepng($im);

// Liberar memoria
imagedestroy($im);
?>
