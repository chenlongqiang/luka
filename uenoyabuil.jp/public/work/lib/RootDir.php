<?php
//define( 'ROOT_DIR'	, '/var/www/vhost/uenoyabuil.jp/public/work/' );
define( 'ROOT_DIR'	, dirname(__DIR__).'/');

set_include_path( get_include_path() . PATH_SEPARATOR . ROOT_DIR . 'lib'
									 . PATH_SEPARATOR . ROOT_DIR . 'db'
									 . PATH_SEPARATOR . ROOT_DIR . 'lib/Smarty/libs'
									 . PATH_SEPARATOR . ROOT_DIR . 'roomReservation' );

// 必須ライブラリのロード
require_once( 'Smarty.class.php' );
require_once( 'spyc.php' );

// クラスのオートローディング
function my_autoload( $name ){
	require_once( $name . '.class.php' );
}
spl_autoload_register( 'my_autoload' );

// 5.3 以降の lcfirst の代替
if (!function_exists("lcfirst")) {
	function lcfirst( $str ){
		return strtolower( substr( $str, 0, 1 ) ) . substr( $str, 1 );
	}
}
?>
