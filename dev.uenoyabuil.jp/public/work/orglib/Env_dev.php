<?php
// セッション有効期限の設定（12時間）
define( 'SESSION_LIFETIME', 43200 );
define( 'SESSION_SAVEPATH', '/var/newkhi_sessions' );
ini_set( 'session.gc_maxlifetime', SESSION_LIFETIME ); 
ini_set( 'session.gc_probability', 1 ); 
ini_set( 'session.gc_divisor',     100 ); 
session_save_path( SESSION_SAVEPATH );

// ディレクトリ定義
define( 'ROOT_DIR', '/home/newkhi/saito/trunk' );			// 本番用に変更が必要

define( 'UI_DIR',     ROOT_DIR . '/ui' );
define( 'CTRL_DIR',   ROOT_DIR . '/ctrl' );
define( 'DB_DIR',     ROOT_DIR . '/db' );
define( 'STATUS_DIR', ROOT_DIR . '/status' );
define( 'COMMON_DIR', ROOT_DIR . '/common' );
define( 'MACHINE_LOG_DIR', '/tmp' );

define( 'LIB_DIR',           UI_DIR . '/lib' );
define( 'CONFIG_DIR',        UI_DIR . '/config' );
define( 'SQL_DIR' ,          UI_DIR . '/sql' );
define( 'TEMPLATE_DIR',      UI_DIR . '/templates' );
define( 'COMPILE_DIR' ,      UI_DIR . '/templates_c' );
define( 'AJAX_LIB_DIR',      UI_DIR . '/lib/ajax' );
define( 'AJAX_SQL_DIR' ,     UI_DIR . '/sql/ajax' );
define( 'AJAX_TEMPLATE_DIR', UI_DIR . '/templates/ajax' );
define( 'AJAX_COMPILE_DIR' , UI_DIR . '/templates_c/ajax' );

define( 'ROOM_DIR',          STATUS_DIR . '/room' );
define( 'LEC_DIR',           STATUS_DIR . '/lecture' );
define( 'MACHINE_DIR',       STATUS_DIR . '/machine' );
define( 'DTO_DIR',           DB_DIR . '/DTO' );

// インクルードパス設定
set_include_path( DB_DIR . '/UI' );

// アクセスログディレクトリ
define( 'AC_LOG_DIR', COMMON_DIR . '/ac_log' );
// アクセスログファイル名
define( 'AC_LOG_FILENAME',  'access_DATE.log' );
// アクセスログ要素デリミタ
define( 'AC_LOG_DELIMITER', ',' );
// アクセスログレベル
define( 'AC_LOG_LEVEL', 10 );

// エラーログディレクトリ
define( 'ERR_LOG_DIR', COMMON_DIR . '/err_log' );
// エラーログ要素デリミタ
define( 'ERR_LOG_DELIMITER', ',' );
// エラーログファイル名
define( 'ERR_LOG_FILENAME',  'error_DATE.log' );

// 機器ログファイル名
define( 'MACHINE_LOG_FILENAME',   'newkhi.log' );

// 必須ライブラリ読み込み
require_once( LIB_DIR .    '/Smarty/Smarty.class.php' );
require_once( COMMON_DIR . '/spyc.php' );

// 時限数
const PERIOD_NUM        = 5;

// 文字列定義
const NEWKHI            = 'newkhi';
const GET               = 'get';
const POST              = 'post';
const HIDDEN            = 'hidden';
const FILENAME          = 'filename';
const TITLE_TEXT        = 'title_text';
const PRE_TITLE_TEXT    = 'pre_title_text';
const HEADER_TEXT       = 'header_text';
const FOOTER_TEXT       = 'footer_text';
const CTRL_MODE         = 'ctrl_mode';
const ROOM_MODE         = 'room_mode';
const PERIOD_START_TIME = 'period_start_time';
const PERIOD_END_TIME   = 'period_end_time';
const PARAMS            = 'params';
const CODE              = 'code';
const STATUS            = 'status';
const MSG               = 'msg';
const HISTORY           = 'history';
const FROMPAGE          = 'frompage';
const DATA              = 'data';
const ERROR             = 'error';
const ERROR_CODE        = 'error_code';
const ERROR_MSG         = 'error_msg';
const USER_ID           = 'user_id';
const PASSWD            = 'passwd';
const LEVEL             = 'level';
const MODE              = 'mode';
const JS_LOAD           = 'js_load';
const RESERVE_ID        = 'reserve_id';
const ROOM_ID           = 'room_id';
const LEC_ID            = 'lec_id';
const INDEX             = 'index';
const SCHEDULE          = 'schedule';
const CONFIRM           = 'confirm';
const RESERVE           = 'reserve';
const RESERVATION       = 'reservation';
const LECTURE           = 'lecture';
const MULTIPOINT        = 'multipoint';
const CTRL_ROOM         = 'ctrl_rooom';
const CTRL_ROOM2        = 'ctrl_room2';
const CTRL_LOCAL        = 'ctrl_local';
const CTRL_LOCAL2       = 'ctrl_local2';
const LEC_PRESET        = 'lec_preset';
const LEC_STYLE_PRESET  = 'lec_style_preset';
const ROOM_PRESET       = 'room_preset';
const SEARCH            = 'search';
const MACHINE_LIST      = 'machine_list';
const SESSION_STATUS    = 'session_status';
const SESSION_NAME      = 'newkhi_dev';
const SESSION_URL       = '/saito/';
//const SESSION_URL       = '/newkhi/';

// フラグ系定義
const DISP_GROUP_NAME   = true;		// [index] 教室リストのグループ名表示フラグ（ 時計台・東京オフィスは false ）

$arr_room_mode = array(
	'clocktower'      => array( 'rooms' => array( 92 ),     'map_path' => './map/clocktower.inc'      ),
	'tokyo_office'    => array( 'rooms' => array( 91, 95 ), 'map_path' => './map/tokyo_office.inc'    ),
	'tokyo_office_r1' => array( 'rooms' => array( 93 ),     'map_path' => './map/tokyo_office_r1.inc' ),
	'tokyo_office_r3' => array( 'rooms' => array( 94 ),     'map_path' => './map/tokyo_office_r3.inc' ),
	'south200'        => array( 'rooms' => array( 41, 42 ), 'map_path' => './map/south200.inc'        ),
	'default'         => array( 'rooms' => array(),         'map_path' => './map/default.inc'         ),
);

// クラスのオートローディング
function __autoload( $name ){
	require_once( $name . '.php' );
}

?>
