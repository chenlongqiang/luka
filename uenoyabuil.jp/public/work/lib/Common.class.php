<?php

// 共通関数群クラス
final class Common{

	const SID      = 'sid';
	const USER_ID  = 'user_id';
	const IP_LIST  = 'ip_list';
	const ALLOW    = 'allow';
	const DENY     = 'deny';
	const IP       = 'ip';
	const REMARKS  = 'remarks';
	const ROOMS    = 'rooms';
	const MAP_PATH = 'map_path';

	public static function test(){
		echo 'test';
	}

	// ログ出力関数
	// +----+------------------------------------
	// | in | data     : ダンプするデータ配列
	// |    | filename : 保存ファイル名（省略すると同じディレクトリに日付で保存）
	// |    | mode     : fopen のモード設定（規定値 "w"）
	// +----+------------------------------------
	public static function _log( $data, $filename = null, $mode = "w" ){

		if( !isset( $data ) )     return;
		if( !isset( $filename ) ) $filename = __DIR__ . '/log/' . date( 'YmdAHis', time() ) . '.log';

		if( is_array( $data ) ){

			$ret = Spyc::YAMLDump( $data );

		} else {

			$ret = strval( $data );

		}

		$fp = fopen( $filename, $mode );
		fwrite( $fp, $ret );
		fclose( $fp );

	}

	// セッション開始
	public static function _session_start( $name = SESSION_NAME ){

		session_name( $name );
		session_set_cookie_params( 0, SESSION_URL );
		session_start();
	}

	// 認証チェック
	public static function chk_auth(){

		if( isset( $_SESSION[ self::SID ] ) && $_SESSION[ self::SID ] === session_id() ){
			$code  = true;
			$level = $_SESSION[ LEVEL ];
		} else {
			$code  = false;
			$level = null;
		}

		return array( CODE => $code, LEVEL => $level );
	}
}
?>
