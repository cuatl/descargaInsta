<?php
   /*
   * extrae los url de un perfil de instagram, es manual
   * 1. recorrer el perfil del usuario para que aparezcan las imagenes (thumbnails)
   * 2. recorrer recorrer recorrer
   * 3. seguir recorriendo hasta donde quieras
   * 4. en inspeccionar elemento navega hasta body -> span -> seccion -> article -> div ... 
   * es el contenedor de cada fila de fotos, copiar ese contenido (botón derecho, Copy , Copy outerHTML)
   * 5. pegar ese contenido en un archivo de texto. Ejecutar este script tomando como argumento tal archivo
   * digamos extractURL.php temporal.txt > lista.txt -- esto va a generar un listado  en el 
   * archivo "lista.txt"
   * @toro 2016
   */
   if(!isset($argv[1]) || !is_file($argv[1])) die("\nUso: ".$argv[0]." temporal.txt > lista.txt\n\n");
   //
   $data= file_get_contents($argv[1]);
   preg_match_all("/href=\"\/p\/([A-Za-z0-9_\-]{1,})+\/\?taken/",$data,$y);
   if(!empty($y[1])) {
      foreach($y[1] AS $k) {
         echo "https://www.instagram.com/p/".$k."/\r\n";
      }
   }
?>
