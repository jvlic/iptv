<?php

if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');
if(!defined('PATH')){
    define("PATH", $_SERVER["DOCUMENT_ROOT"]);
}
require(PATH.'/login_panel/connect_pdo.php');
	



/* Конфигурация базы данных */

require(PATH.'/login_panel/base_setting.php');

/* Конец секции */
/* Конфигурация базы данных */


//$link = mysql_connect($db_host,$db_user,$db_pass) or die('Невозможно установить соединение с базой данных');

//mysql_select_db($db_database,$link);
//mysql_query("SET names UTF8");

$link=new DBTransaction($db_host, $db_user,$db_pass,$db_database);



define('ENVIRONMENT', 'development'); 
//define('ENVIRONMENT', 'production'); 
if (defined('ENVIRONMENT'))
{
//	echo ENVIRONMENT;
    switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL & ~E_NOTICE);
          //  echo "develo";
		break;
	
		//case 'testing':
		case 'production':
			error_reporting(0);
           //  echo "prod";
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}

?>