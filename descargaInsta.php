<?php
   /*
   * descarga fotos de intagram:
   * puede descargar directo una url por ejemplo  https://www.instagram.com/p/BJYODoXAmWJ/
   * o bien tomar las direcciones de un archivo de texto, una dirección por línea
   * @ToRo 2016
   */
   $url = (isset($argv[1]) && preg_match("/(instagram|\.txt)/i",$argv[1])) ? $argv[1] : null;
   if(empty($url)) {
      die("\nUso: php $argv[0] FOTO_INSTAGRAM | TXT\n\n");
   }
   if(preg_match("/\.txt/i",$url)) {
      if(!is_file($url)) die("No existe una lista de direcciones en $url :(\n\n");
      $file = file($url);
      if(empty($file)) die("no hay nada en el archivo $url\n");
      foreach($file AS $url) {
         if(preg_match("/instagram/",$url)) {
            instagram($url);
         }
      }
   } else {
      instagram($url);
   }

   function instagram($url) {
      $url = trim($url);
      echo "Descargando: $url ...";
      //
      $agent= "Mozilla/5.0 (Windows NT 6.0; rv:16.0) Firefox/13.0"; //user agent
      $context = stream_context_create(['http'=>['user_agent'=> $agent ]]);
      $data = file_get_contents($url,false,$context);
      //
      preg_match("/\"owner\": {\"username\": \"(.*)\",\s\"is_unpublished\"/",$data,$y);
      $username = null;
      if(!empty($y[1])) $username = trim($y[1]);
      if(empty($username)) {
         preg_match("/taken-by=(.*)\" rel/",$data,$y);
         $username = trim($y[1]);
      }
      $path=(!empty($username))? "instagram/".$username."/": "instagram/";
      echo $username;
      if(!empty($path) && !is_dir($path)) mkdir($path,0755,true);
      //buscamos video
      preg_match("/property=\"og:video\" content=\"(.*)\"/",$data,$y);
      if(isset($y[1]) && preg_match("/^http/",$y[1])) {
         $imagen = $y[1];
         preg_match("/(.*)\/(.*)\.mp4/i",$imagen,$v);
         $name = empty($v[2]) ? uniqid() : $v[2];
         echo "! => video ".$name.".mp4 ";
         if(!is_file($path.$name.".mp4")) {
            file_put_contents($path.$name.".mp4", file_get_contents($imagen,false,$context));
         }
         echo " ok!\n";
      } else {
         //buscamos imagen
         preg_match("/property=\"og:image\" content=\"(.*)\"/",$data,$y);
         if(isset($y[1]) && preg_match("/^http/",$y[1])) {
            $imagen = $y[1];
            preg_match("/(.*)\/(.*)\.jpg/i",$imagen,$v);
            $name = empty($v[2]) ? uniqid() : $v[2];
            echo "! => imagen ".$name.".jpg ";
            if(!is_file($path.$name.".jpg")) {
               file_put_contents($path.$name.".jpg", file_get_contents($imagen,false,$context));
            }
            echo " ok!\n";
         }
      }
      //meta
      $meta="https://tar.mx/log/descargar-imagenes-de-instagram-con-php/\n";
      foreach(['url','title'] AS $k) {
         preg_match("/property=\"og:".$k."\" content=\"(.*)\"/",$data,$y);
         if(!empty($y[1])) $meta .= strtoupper($k).": ".$y[1]."\n";
      }
      file_put_contents($path.$name.".txt",$meta);
   }
?>
