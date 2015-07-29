<?php
/*
GisClient map browser

Copyright (C) 2008 - 2009  Roberto Starnini - Gis & Web S.r.l. -info@gisweb.it

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

/************ Session Name ********/
define('GC_SESSION_NAME', 'gisclient3'); // se definito, viene chiamato session_name() prima di session_start();

ini_set('max_execution_time',90);
ini_set('memory_limit','512M');
//error_reporting (E_ERROR | E_PARSE);
//error_reporting  (E_ALL);
error_reporting  (E_ALL & ~E_STRICT);
ini_set('display_errors', 'On');

define('LONG_EXECUTIONE_TIME',300);
define('LONG_EXECUTION_MEMORY','512M');
//define('EXTERNAL_LOGIN_KEY', 'pas0d8ufypasod8fy09872hp4irja.shdfkauyfgo-sdygfo987wyr');
define('TAB_DIR', 'it');
//define('FORCE_LANGUAGE', 'it'); // Questi valori devono corrispondere a (it, de, en, ..)

/*******************Installation path *************************/
define('ROOT_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('OPENLAYERS','http://cdnjs.cloudflare.com/ajax/libs/openlayers/2.13.1/OpenLayers.js');
define('PROJ_LIB',"/usr/share/proj");
define('IMAGE_PATH',ROOT_PATH.'tmp/ms_tmp/');
define('IMAGE_URL','/tmp/');
/*******************                  *************************/

require_once (ROOT_PATH."config/config.db.php");
//require_once (ROOT_PATH."lib/postgres.php");
require_once (ROOT_PATH."lib/debug.php");
require_once (ROOT_PATH.'lib/gcapp.class.php');


define('PUBLIC_HOST',sprintf("%s://%s",$_SERVER["REQUEST_SCHEME"],$_SERVER["SERVER_NAME"]));
define('PUBLIC_URL', PUBLIC_HOST.'/gisclient/');
define('MAP_URL', PUBLIC_URL.'map/');
//define('PRIVATE_MAP_URL', PUBLIC_URL.'map/index.php');

/*******************OWS service url *************************/
define('TILECACHE_CFG','/etc/tilecache.cfg');
define('TILES_CACHE','/tmp/tilecache');
define('TILECACHE_URL','http://localhost/cgi-bin/tilecache.py');//NON E' OBBLIGATORIO
/*define('GISCLIENT_OWS_URL',PUBLIC_URL.'services/ows.php');//NON E' OBBLIGATORIO*/
define('GISCLIENT_LOCAL_OWS_URL',PUBLIC_URL.'services/ows.php?');//NON E' OBBLIGATORIO (CHIAMATA DA TILECACHE)
//define('GISCLIENT_OWS_URL',PUBLIC_HOST.'/cgi-bin/mapserv?map=/apps/gisclient-3/map/ubigreen/ubigreen_ubi_one.map');
define('GISCLIENT_OWS_URL',PUBLIC_HOST.'/cgi-bin/mapserv?');

/********************* MAPPROXY ***************/
define('MAPSERVER_URL', PUBLIC_HOST.'/cgi-bin/mapserv');
define('MAPSERVER_BINARY_PATH', '/usr/lib/cgi-bin/mapserv');
define('MAPPROXY_PATH', '/opt/mapproxy/');
define('MAPPROXY_URL', '/');
define('MAPPROXY_CACHE_PATH', '/data/tiles/');
define('MAPPROXY_CACHE_TYPE', 'mbtiles'); //SUPPORTED:file/mbtiles/sqlite
define('MAPPROXY_DEMO', true);
define('MAPPROXY_GRIDS_NUMLEVELS', 20);

/**************** PRINT - EXPORT***************/
define('GC_PRINT_TPL_DIR', ROOT_PATH.'public/services/print/');
define('GC_PRINT_TPL_URL', PUBLIC_URL.'services/print/');
define('GC_PRINT_IMAGE_SIZE_INI', ROOT_PATH.'config/print_image_size.ini');
define('GC_WEB_TMP_DIR', ROOT_PATH.'public/services/tmp/print/');
define('GC_WEB_TMP_URL', PUBLIC_URL.'services/tmp/print/');
//define('GC_PRINT_LOGO_SX', 'http://parcoticino.server2/map/images/logo_sx.png');
//define('GC_PRINT_LOGO_DX', 'http://192.168.0.13/author-r3client/default/images/logo_dx.gif');
define('GC_FOP_LIB', ROOT_PATH.'lib/fop.php');
define('GC_FOP_CMD', '/usr/bin/fop');
define('GC_PRINT_SAVE_IMAGE', true); // baco mapscript: il saveImage a volte funziona solo specificando il nome del file, altre volte funziona solo se NON si specifica il nome del file
define('PRINT_RELATIVE_URL_PREFIX', 'http://sit.bonificamarche.it/'); // se GISCLIENT_OWS_URL è relativo, questo prefisso viene aggiunto in fase di stampa


/******************* TINYOWS **************/
define('TINYOWS_PATH', '/var/www/cgi-bin');
define('TINYOWS_EXEC', 'tinyows');
define('TINYOWS_FILES', ROOT_PATH.'tinyows/');
define('TINYOWS_SCHEMA_DIR', '/usr/share/tinyows/schema/');
define('TINYOWS_ONLINE_RESOURCE', PUBLIC_URL.'services/tinyows/'); // aggiungere ? o & alla fine


/*************  REDLINE ***************/
define('REDLINE_SCHEMA', 'public');
define('REDLINE_TABLE', 'annotazioni');
define('REDLINE_SRID', '32632');
define('REDLINE_FONT', 'dejavu-sans');


/****** PRINT VECTORS ********/
define('PRINT_VECTORS_TABLE', 'print_vectors');
define('PRINT_VECTORS_SRID', 32632);
define('MAPFILE_MAX_SIZE', '20000');


//if (!defined('SKIP_INCLUDE') || SKIP_INCLUDE !== true) {

//}

//Author
define('ADMIN_PATH',ROOT_PATH.'public/admin/');

//debug
if(!defined('DEBUG_DIR')) define('DEBUG_DIR',ROOT_PATH.'config/debug/');
if(!defined('DEBUG')) define('DEBUG', 0); // Debugging 0 off 1 on

//if (!defined('SKIP_INCLUDE') || SKIP_INCLUDE !== true) {
require_once (ROOT_PATH."config/login.php");
//}	

/****************** QUERY REPORTS ***************+*/
define('MAX_REPORT_ROWS',5000);
define('REPORT_PROJECT_NAME','REPORT');
define('REPORT_MAPSET_NAME','report');
define('FONT_LIST','fonts');
define('MS_VERSION','');

define('CATALOG_EXT','SHP,TIFF,TIF,ECW');//elenco delle estensioni caricabili sul layer
define('DEFAULT_ZOOM_BUFFER',100);//buffer di zoom in metri in caso non venga specificato layer.tolerance
define('MAX_HISTORY',6);//massimo numero di viste memorizzate
define('MAX_OBJ_SELECTED',2000);//massimo numero di oggetti selezionabili
define('WIDTH_SELECTION', 4);//larghezza della polilinea di selezione
define('TRASP_SELECTION', 50);//trasparenza della polilinea di selezione
define('COLOR_SELECTION', '255 0 255');//colore della polilinea di selezione
define('MAP_BG_COLOR', '255 255 255');//colore dello sfondo per default
define('EDIT_BUTTON', 'edit');

define('DEFAULT_TOLERANCE',4);//Raggio di ricerca in caso non venga specificato layer.tolerance
define('LAYER_SELECTION','__sel_layer');//Nome per i layer di selezione
define('LAYER_IMAGELABEL','__image_label');//Nome per il layer testo sulla mappa
define('LAYER_READLINE','__readline_layer');
define('DATALAYER_ALIAS_TABLE','__data__');//nome riservato ad alias per il nome della tabella del layer (usato dal sistema nelle query, non ci devono essere tabelle con questo nome)
define('WRAP_READLINE','\\');
define('COLOR_REDLINE','0 0 255');//Colore Line di contorno oggetti poligono o linea selezionati
define('OBJ_COLOR_SELECTION','255 255 0');//Colore Line di contorno oggetti poligono o linea selezionati
define('MAP_DPI', 90.714);//Tiles standard resolutions
define('TILE_SIZE',256);//Mapserver map resolution
define('PDF_K',2);//Mapserver map resolution

define('DEFAULT_SCALE_LIST','2000000 1000000 500000 250000 200000 100000 50000 25000 10000 5000 2000 1000 500 250');
//  /opt/mapproxy/bin/mapproxy-util scales --dpi MAP_DPI 2000000 1000000 500000 250000 200000 100000 50000 25000 10000 5000 2000 1000 500 250

//define('OWS_CACHE_TTL', 60); // Map cache (Prevent OL bug for multiple request)
//define('OWS_CACHE_TTL_OPEN', 4*60*60); // Map cache for the 1st open of the map
//define('DYNAMIC_LAYERS', 'g_prati.prati,g_cooperative.data_wiese,g_cooperative.data_wiese'); // comma separated list of dynamic layers (same url different result)


/****************** DATA MANAGER ***************+*/
define('USE_DATA_IMPORT', true);
define('USE_PHP_EXCEL', true);
define('MEASURE_AREA_COL_NAME', 'gc_area'); 
define('MEASURE_LENGTH_COL_NAME', 'gc_length'); 
define('COORDINATE_X_COL_NAME', 'gc_coordinate_x');
define('COORDINATE_Y_COL_NAME', 'gc_coordinate_y'); 
define('LAST_EDIT_USER_COL_NAME', 'gc_last_edit_user'); 
define('LAST_EDIT_DATE_COL_NAME', 'gc_last_edit_date');
define('CURRENT_EDITING_USER_TABLE', 'gc_current_editing_user');

define('UPLOADED_FILES_PRIVATE_PATH', ROOT_PATH.'files/'); 
define('UPLOADED_FILES_PUBLIC_PATH', ROOT_PATH.'public/services/files/'); 
define('UPLOADED_FILES_PUBLIC_URL', PUBLIC_URL.'services/files/');


/****************** LEGEND ***************+*/
define('LEGEND_ICON_W',24);
define('LEGEND_ICON_H',16);
define('LEGEND_POINT_SIZE',15);
define('LEGEND_LINE_WIDTH',1);
define('LEGEND_POLYGON_WIDTH',2);
define('PRINT_PDF_FONT','times');


define('CLIENT_LOGO', '/images/logo-consorzio.png');

// Cache in ows.php
define('OWS_CACHE_TTL', 60); // Map cache (Prevent OL bug for multiple request)
define('OWS_CACHE_TTL_OPEN', 4*60*60); // Map cache for the 1st open of the map
//define('DYNAMIC_LAYERS', ''); // comma separated list of dynamic layers (same url different result)
