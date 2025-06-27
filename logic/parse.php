<?php

/**
 * @author JV
 * @copyright 2025
 */
define('INCLUDE_CHECK',true);
if(!defined('PATH')){
    define("PATH", $_SERVER["DOCUMENT_ROOT"]);
}
error_reporting(E_ALL);
$flag_test=true;
//$flag_test=false;
?>
<head><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta name="Keywords" content="parse"/>
  <meta name="description" content="parse file"/>
<title>parse_iptv</title>
 <link rel="shortcut icon" href="/favicon.ico"/> 
 <link rel="stylesheet" type="text/css" href="/css/style.css" media="all" />
 </head>
 <?php

 
require(PATH.'/login_panel/connect.php');
$file_name="../../IPTV_SHARED.m3u";
//$file_name="../IPTV_SHARED_2025.m3u";

$icount=0;
$insertValues = array();
$mass=array();
if ( ($handle_o = fopen($file_name, "r") ) !== FALSE ) {
//while ( ($data_o = fgetcsv($handle_o, 1000, ",")) !== FALSE) {
    while ( ($data_o = fgets($handle_o)) !== FALSE) {
  // echo $data_o.'<br />';
        if(!empty($data_o)){
        if(preg_match('/^#EXTINF/u',$data_o)){
            $mass[]=$insertValues;
             $insertValues = array();
             $extinf=strstr($data_o,',');
             $name=substr($extinf,1);
             $insertValues['EXTINF']=$data_o;
             $insertValues['name']=$name;
                
        }
        if (preg_match('/^#EXTVLCOPT/u',$data_o)){
            $insertValues['user_agent']=$data_o;
        }
        if (preg_match('/^(http|https)/u',$data_o)){
            $insertValues['link']=$data_o;
        }
        }
    //  var_export ($insertValues);
     // print $insertValues['ext'].".".$values;
      print "<p></p>";
     
  }
  $mass[]=$insertValues;
 // var_export ($mass);
  fclose($handle_o);
  

  }
 // var_export($mass);
  $date_prin= date("Y-m-d H:i:s");                   
  foreach($mass as $val) {
    if(count($val)>0){
        $flag_update=false;
        $name=$val['name'];
        $name = str_replace(array("\r", "\n"), '', $name);  
        //echo 'name-'.$val['name'].'<br />';
        //echo $val['EXTINF'].' '.$val['link'].'<br />';
        $jq="SELECT * FROM iptv_shared where (name=:name)";
        $data_jq=array('name'=>$name);
        $result=$link->selectDB_fetchALL($jq,$data_jq);
        //var_export($result);
        if (count($result)==1){
            
            foreach($result as $row){
                if($row['EXTINF']!=$val['EXTINF']){
                    $flag_update=true;
                    echo '<p class="update_text"><br />update '.$name.' <br /> change '.$row['EXTINF'] .'<br />on '.$val['EXTINF']."</p>";
                }
                if($row['link']!=$val['link']){
                    $flag_update=true;
                    echo '<p class="update_text"><br />update '.$name.' <br />change link '.$row['link'] .'<br /> on '.$val['link']."</p>";
                }
                if($flag_update==true){
                    //echo "<p class='update_text'>update<br /> ".$name.'</p>';
                    $jq="UPDATE iptv_shared SET EXTINF=:EXTINF,link=:link,date_update=:date_update  where (name=:name)";
        $data_jq=array('name'=>$name,'EXTINF'=>$val['EXTINF'],'link'=>$val['link'],'date_update'=>$date_prin);
              if($flag_test==false){
                $result2=$link->updateDB($jq,$data_jq);
              }    
           }
        }
        }
        if (count($result)==0){
       //     echo "<p class='insert_text'><br />insert<br /> ".$name." link ".$val['link']."</p>";
            $jq="INSERT INTO iptv_shared (EXTINF,name,link,date_update)VALUES(:EXTINF,:name,:link,:date_update)";
        $data_jq=array('name'=>$name,'EXTINF'=>$val['EXTINF'],'link'=>$val['link'],'date_update'=>$date_prin);
        if($flag_test==false){
               // $result2=$link->insertTransaction($jq,$data_jq);
        }         
    }
     if (count($result)>2){
        echo "<br /> We have anomally,row >2 in".$name;
         foreach($result as $row){
            echo $row['id_canal'].'<br />';
         }
         }
    }
  }
  
  //create m3u
  $content='';
   $jq="SELECT * FROM iptv_shared";
        $data_jq=array();
        $result=$link->selectDB_fetchALL($jq,$data_jq);
        foreach($result as $row){
            #EXTINF:-1 tvg-id="pervy" tvg-logo="http://i120.fastpic.org/big/2022/0731/5a/975757b7227dd15519c6a0e0d5f0065a.png" catchup="append" catchup-days="3" catchup-source="?offset=-${offset}&utcstart=${timestamp}" group-title="Эфирные",Первый канал
#EXTVLCOPT:http-user-agent=SmartTV
http://zabava-htlive.cdn.ngenix.net/hls/CH_1TVSD/variant.m3u8

            $content.=$row['EXTINF']."#EXTVLCOPT:http-user-agent=SmartTV"."\r\n".$row['link']."\r\n";
                }
//save file
if($flag_test==false){
$filename = '../IPTV_SHARED_2025.m3u';
$filename_backup = '../../IPTV_SHARED_2025_old.m3u';
if(file_exists($file_name)){
    copy($filename,$filename_backup);
}
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
}
?>