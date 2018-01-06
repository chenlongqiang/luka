<?php

require_once( '../../work/lib/RootDir.php' );

$classname = ucfirst( basename( $_SERVER[ 'SCRIPT_NAME' ], '.php' ) );
$page = new $classname();

if( $page ){

	$page->response();
			
} else {
		
	echo Env::ERROR;

}
?>
