<?php
$domain=explode(".",$_SERVER["HTTP_HOST"]);
$userIp=$_SERVER['REMOTE_ADDR'];
$dbName='ubigreen';
$dbSchema='gisclient_3';
$dbUser = 'postgres';
$dbPwd = 'postgres';
$userSchema=$dbSchema;
$charSet='UTF-8';

$segnalazioniSchema = "segnalazioni_test";


define('DB_NAME',$dbName);
define('DB_SCHEMA',$dbSchema);
if (!defined('USER_SCHEMA')) define('USER_SCHEMA', $userSchema);
define('CHAR_SET',$charSet);
define('DB_USER',$dbUser); //Superutente;
define('DB_PWD',$dbPwd);
define('DB_HOST','127.0.0.1');
define('DB_PORT','5432');
define('PRINT_SERVICE_PWD','printservice$');



//Utente scritto sul file .map;
define('MAP_USER','mapserver');
define('MAP_PWD','mapserver');
define('SUPER_USER','davide');

#define('PRINT_RELATIVE_URL_PREFIX', 'http://consorziobonifica.rr.nu'); // se GISCLIENT_OWS_URL � relativo, questo prefisso viene aggiunto in fase di stampa
define('POSTGIS_TRANSOFRM_GEOMETRY','postgis_transform_geometry');
	
$gMapKey = '';
$bingKey = 'AlFQX6e7DTj28pq390UUNj_uSgyuEsgouug_LZqPHs_NH7kk_WvrEnn7GXheP7sQ';
