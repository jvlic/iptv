<?php

/**
 * @author JV
 * @copyright 2025
 */
define('INCLUDE_CHECK',true);
if(!defined('PATH')){
    define("PATH", $_SERVER["DOCUMENT_ROOT"]);
}
require(PATH.'/login_panel/connect.php');

  
  //create m3u
  $content='<?xml version="1.0" encoding="utf-8" ?><!DOCTYPE tv SYSTEM "http://www.teleguide.info/download/xmltv.dtd">
<tv generator-info-name="TVH_W/0.8" generator-info-url="http://www.teleguide.info/">';
   $jq="SELECT * FROM iptv_shared";
        $data_jq=array();
        $result=$link->selectDB_fetchALL($jq,$data_jq);
        $i=1;
        foreach($result as $row){
           // <channel id="1">
//<display-name lang="ru">Первый канал</display-name>
//</channel>

            $content.='
            <channel id="'.$i.'">
            <display-name lang="ru">'.$row['name'].'</display-name>
            </channel>';
            $i++;
                }
            $content.='<programme start="20250606220000 +0300" stop="20250606231000 +0300" channel="1">
<title lang="ru">Что? Где? Когда? (Летняя серия игр) (12+)</title>
</programme>
<programme start="20250606231000 +0300" stop="20250607003000 +0300" channel="1">
<title lang="ru">Наша новая музыка (12+)</title>
</programme>
<programme start="20250607003000 +0300" stop="20250607011000 +0300" channel="1">
<title lang="ru">Подкаст.Лаб (Жизнь замечательных) (12+)</title>
</programme>
<programme start="20250607011000 +0300" stop="20250607015000 +0300" channel="1">
<title lang="ru">Подкаст.Лаб (Драгоценные истории) (12+)</title>
</programme>
</tv>';
//save file
$filename = '../IPTV_epg.xml';
            if (!$handle = fopen($filename, 'w')) {
                 echo "Cannot open file ($filename)";
                 exit;
            }
        
            // Write $somecontent to our opened file.
            if (fwrite($handle, $content) === FALSE) {
                echo "Cannot write to file ($filename)";
                exit;
            }
        
           // echo "Success, wrote ($somecontent) to file ($filename)";
        
            fclose($handle);
?>