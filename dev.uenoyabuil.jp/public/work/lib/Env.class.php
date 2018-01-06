<?php

class Env{

	// ハッシュ文字列定義
	const CMD				= 'cmd';
	const ERROR				= 'error';
	const ERROR_MSG			= 'error_msg';
	const CODE				= 'code';
	const DATA				= 'data';
	const MESSAGE			= 'message';
	const PARAMS			= 'params';
	const CONFIG			= 'config';
	const FILENAME 			= 'filename';
	const SESSION_NAME		= 'session_name';
	const SESSION_URL		= 'session_url';
	const SUB_SYSTEM_LIST	= 'sub_system_list';
	const TEMPLATE_FILE		= 'template_file';
	// テンプレートファイル拡張子
	const TEMPLATE_FILE_EXT	= 'template_file_ext';

	/**
	 * 設定ファイルの読み込み
	 */
	public static function loadEnv( $systemDir = null ){

		if( isset( $systemDir ) ){
			return Spyc::YAMLLoad( ROOT_DIR . $systemDir . '/config/env.yaml' );
		} else {
			return Spyc::YAMLLoad( ROOT_DIR . 'config/env.yaml' );
		}

	}
}
?>
