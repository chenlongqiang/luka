<?php

class Validation{
	
	private $checkName;	// 設定ファイル validation.yaml の HUSH名
	private $checkData;
	private $conf;
	private $data;

	public function __construct( $systemName ){
		
		// 何らかの原因でファイル名が取得できなかった場合
		if( !isset( $systemName ) ) return false;
	
		// バリデーション設定YAML読み込み
		if( isset( $systemName ) ) {
			
			$this->conf = Spyc::YAMLLoad( ROOT_DIR . $systemName . '/config/validation.yaml' );
	
		} else {

			$this->conf = Spyc::YAMLLoad( ROOT_DIR . 'lib/config/validation.yaml' );
		}
	}

	// チェック実行関数
	// 戻り値（HUSH値）
	// +-----------+---------------------------------
	// | code      | true(1)  : チェックOK
	// |           | false(0) : 一つ以上エラーあり
	// |           | -1       : その他エラー
	// +-----------+---------------------------------
	// | error     | HUSH値
	// |           |  error[ ﾌｨｰﾙﾄﾞ名 ] : エラーコード
	// +-----------+---------------------------------
	// | error_msg | HUSH値
	// |           |  error_msg[ ﾌｨｰﾙﾄﾞ名 ] : エラーメッセージ
	// +-----------+---------------------------------
	public function check( $data, $page ){

		$code  = true;
		$error = array();
		$cnt   = 0;
		
		$pageConf = $this->conf[ $page ];

		foreach( $pageConf as $field => $chk ){

			// カスタムチェック関数を実行
			if( $field === 'custom' ){

				foreach( $chk as $chkType => $param ){

					$chkParams = isset( $param[ Env::PARAMS ] ) ? $param[ Env::PARAMS ] : null;
					
					$ret = $this->$chkType( $data, $chkParams );	// 可変関数

					if( !$ret[ Env::CODE ] ){
						
						// 一つでもエラーなら 'code' = false
						$code = false;
						$tmp_errcode[ $field ][] = $ret  [ Env::ERROR ];
						$tmp_errmsg [ $field ][] = $param[ Env::ERROR_MSG ];
					}
				}

			} else {

				foreach( $chk as $chkType => $param ){

					// condition を満たすときのみチェックを実行
					if( isset( $param[ 'condition' ] ) ){

						foreach( $param[ 'condition' ] as $key => $value ){
							
							if( !isset( $data[ $key ] ) || $data[ $key ] != $value ) break 2;

						}
					}

					if( !isset( $data[ $field ] ) ){
					
						if( $chkType === 'unchecked' ){
								
							// ラジオボタン未選択時はフィールドデータが無い
							// エラーなら 'code' = false
							$code = false;
							$tmp_errcode[ $field ][] = 'UNSELECTED';
							$tmp_errmsg [ $field ][] = $param[ Env::ERROR_MSG ];

						} else {

							// その他エラー（フィールドが存在しない）
							$code = false;
							$tmp_errcode[ $field ][] = 'FIELD_NOT_EXIST';
							$tmp_errmsg [ $field ][] = '「' . $field . '」というフィールドのデータがありません';

						}

						break;

					} else {

						$chkParams = isset( $param[ Env::PARAMS ] ) ? $param[ Env::PARAMS ] : null;
						
						$ret = $this->$chkType( $data[ $field ], $chkParams );	// 可変関数

						if( !$ret[ Env::CODE ] ){
							
							// 一つでもエラーなら 'code' = false
							$code = false;
							$tmp_errcode[ $field ][] = $ret  [ Env::ERROR ];
							$tmp_errmsg [ $field ][] = $param[ Env::ERROR_MSG ];

						}
					}
				}
			}
			$error    [ $field ] = isset( $tmp_errcode[ $field ] ) ? implode( ';', $tmp_errcode[ $field ] ) : '';
			$error_msg[ $field ] = isset( $tmp_errmsg [ $field ] ) ? implode( ';', $tmp_errmsg [ $field ] ) : '';

		}

		return array( Env::CODE => $code, Env::ERROR => $error, Env::ERROR_MSG => $error_msg );

	}

// チェック関数群
// +-----+----------------------------------------
// | out | code  : true(1) : OK false(0) : NG
// |     | error : エラーコード
// +-----+----------------------------------------
//////////////////////////////////////////////////
	
	// 最小文字列チェック
	private function minlength( $str, $len = 0 ){

		if( strlen( $str ) > $len ){
			return array( Env::CODE => true,  Env::ERROR => '' );
		} else {
			return array( Env::CODE => false, Env::ERROR => 'MINLENGTH' );
		}

	}

	// NULLチェック
	private function isNull( $value, $params = null ){
		
		if( is_null( $value ) ){
			return array( Env::CODE => false, Env::ERROR => 'ISNULL' );
		} else {
			return array( Env::CODE => true,  Env::ERROR => '' );
		}

	}
	
	// ラジオボタン未選択の場合に使用
	private function unchecked( $value ){

		return $this->isNull( $value );
	}

	// セレクト項目未選択（-1）チェック
	private function unselected( $value ){

		if( $value == -1 ){
			return array( Env::CODE => false, Env::ERROR => 'UNSELECTED' );
		} else {
			return array( Env::CODE => true,  Env::ERROR => '' );
		}
	}

	// 形式チェック
	private function chkType( $str, $type ){


		// エラーがない場合
		$res = false;
		$ret = array( Env::CODE => true, Env::ERROR => '');

		switch( $type ){
			case 'YEAR':
				if( !preg_match( '/^20[1-9][0-9]$/', $str ) ) $ret = array( Env::CODE => false, Env::ERROR => 'TYPE_YEAR' );
				break;
			case 'MONTH':
				if( preg_match( '/[^0-9]/', $str ) )  $res = true;	// 半角数字チェック
				if( (int)$str > 12 || (int)$str < 1 ) $res = true;	// 数値範囲チェック
				if( $res ) $ret = array( Env::CODE => false, Env::ERROR => 'TYPE_MONTH' );
				break;
			case 'DAY':
				if( preg_match( '/[^0-9]/', $str ) )  $res = true;	// 半角数字チェック
				if( (int)$str > 31 || (int)$str < 1 ) $res = true;	// 数値範囲チェック
				if( $res ) $ret = array( Env::CODE => false, Env::ERROR => 'TYPE_DAY' );
				break;
			default :
				$ret = array( Env::CODE => false, Env::ERROR => 'PARAM ERROR' );
				break;
		}
		
		return $ret;
	}

	private function chkEngNum( $str ) {
		if( preg_match( '/[^0-9]/', $str ) ) {
			return array( Env::CODE => false, Env::ERROR => 'TYPE_ENG_NUM' );
		} else {
			return array( Env::CODE => true, Env::ERROR => '');
		}
	}
	
	// テスト用ダミー
	private function dummy( $value, $params ){
		return array( Env::CODE => false, Env::ERROR => 'DUMMY' );
	}

// | カスタムチェック関数
// +----------------------------------------------
	
}
?>
