<?php

class Validation{
	
	private $checkname;	// 設定ファイル validation.yaml の HUSH名
	private $check_data;
	private $data;

	public function __construct( $checkname, $data ){
		
		// 何らかの原因でファイル名が取得できなかった場合
		if( !isset( $checkname ) ) return false;
	
		$this->checkname = $checkname;
		$this->data      = $data;

		// バリデーション設定YAML読み込み
		$cond = Spyc::YAMLLoad( CONFIG_DIR . '/validation.yaml' );

		if( !array_key_exists( $checkname, $cond ) ) return false;

		$this->check_data = $cond[ $checkname ];

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
	public function check(){

		$code  = true;
		$error = array();
		$cnt   = 0;

		foreach( $this->check_data as $field => $chk ){

			// カスタムチェック関数を実行
			if( $field === 'custom' ){

				foreach( $chk as $chk_type => $param ){

					$chk_params = isset( $param[ PARAMS ] ) ? $param[ PARAMS ] : null;
					
					$ret = $this->$chk_type( $this->data, $chk_params );	// 可変関数

					if( !$ret[ CODE ] ){
						
						// 一つでもエラーなら 'code' = false
						$code = false;
						$tmp_errcode[ $field ][] = $ret[ ERROR ];
						$tmp_errmsg[ $field ][]  = $param[ ERROR_MSG ];
					}
				}
			} else {

				foreach( $chk as $chk_type => $param ){

					// condition を満たすときのみチェックを実行
					if( isset( $param[ 'condition' ] ) ){

						foreach( $param[ 'condition' ] as $key => $value ){
							
							if( !isset( $this->data[ $key ] ) || $this->data[ $key ] != $value ) break 2;

						}
					}

					if( !isset( $this->data[ $field ] ) ){
					
						if( $chk_type === 'unchecked' ){
								
							// ラジオボタン未選択時はフィールドデータが無い
							// エラーなら 'code' = false
							$code = false;
							$tmp_errcode[ $field ][] = 'UNSELECTED';
							$tmp_errmsg[ $field ][]  = $param[ ERROR_MSG ];

						} else {

							// その他エラー（フィールドが存在しない）
							$code = false;
							$tmp_errcode[ $field ][] = 'FIELD_NOT_EXIST';
							$tmp_errmsg[ $field ][]  = '「' . $field . '」というフィールドのデータがありません';

						}

						break;

					} else {

						$chk_params = isset( $param[ PARAMS ] ) ? $param[ PARAMS ] : null;
						
						$ret = $this->$chk_type( $this->data[ $field ], $chk_params );	// 可変関数

						if( !$ret[ CODE ] ){
							
							// 一つでもエラーなら 'code' = false
							$code = false;
							$tmp_errcode[ $field ][] = $ret[ ERROR ];
							$tmp_errmsg[ $field ][]  = $param[ ERROR_MSG ];

						}
					}
				}
			}
			$error[ $field ]     = isset( $tmp_errcode[ $field ] ) ? implode( ';', $tmp_errcode[ $field ] ) : '';
			$error_msg[ $field ] = isset( $tmp_errmsg[ $field ] )  ? implode( ';', $tmp_errmsg[ $field ]  ) : '';

		}

		return array( CODE => $code, ERROR => $error, ERROR_MSG => $error_msg );

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
			return array( CODE => true,  ERROR => '' );
		} else {
			return array( CODE => false, ERROR => 'MINLENGTH' );
		}

	}

	// NULLチェック
	private function isNull( $value, $params = null ){
		
		if( is_null( $value ) ){
			return array( CODE => false, ERROR => 'ISNULL' );
		} else {
			return array( CODE => true,  ERROR => '' );
		}

	}
	
	// ラジオボタン未選択の場合に使用
	private function unchecked( $value ){

		return $this->isNull( $value );
	}

	// セレクト項目未選択（-1）チェック
	private function unselected( $value ){

		if( $value == -1 ){
			return array( CODE => false, ERROR => 'UNSELECTED' );
		} else {
			return array( CODE => true,  ERROR => '' );
		}
	}

	// 形式チェック
	private function chkType( $str, $type ){


		// エラーがない場合
		$res = false;
		$ret = array( CODE => true, ERROR => '');

		switch( $type ){
			case 'YEAR':
				if( !preg_match( '/^20[1-9][0-9]$/', $str ) ) $ret = array( CODE => false, ERROR => 'TYPE_YEAR' );
				break;
			case 'MONTH':
				if( preg_match( '/[^0-9]/', $str ) )  $res = true;	// 半角数字チェック
				if( (int)$str > 12 || (int)$str < 1 ) $res = true;	// 数値範囲チェック
				if( $res ) $ret = array( CODE => false, ERROR => 'TYPE_MONTH' );
				break;
			case 'DAY':
				if( preg_match( '/[^0-9]/', $str ) )  $res = true;	// 半角数字チェック
				if( (int)$str > 31 || (int)$str < 1 ) $res = true;	// 数値範囲チェック
				if( $res ) $ret = array( CODE => false, ERROR => 'TYPE_DAY' );
				break;
			default :
				$ret = array( CODE => false, ERROR => 'PARAM ERROR' );
				break;
		}
		
		return $ret;
	}

	// 講義名重複チェック（新規モード用）
	private function reservation_lec_exist( $value ){

		$ref_pdo = new Ref_Pdo_Reserve();
		$ret     = $ref_pdo->check_lec_exist( array( $value ) );

		$code    = (int)$ret ? false : true;

		$error_code = $code ? '' : 'RESERVATION_DUPLICATED';
		return array( CODE => $code, ERROR => $error_code );
	}

	// 起動中の講義の有無チェック（全削除用）
	private function check_running_lecture( $lec_id ){
	
		$ref_pdo = new Ref_Pdo_Confirm();
		$ret     = $ref_pdo->ref_running_lecture( array( $lec_id ) );

		$code    = (int)$ret ? false : true;

		$error_code = $code ? '' : 'RUNNING_LECTURE_EXIST';
		return array( CODE => $code, ERROR => $error_code );
	}

	// テスト用ダミー
	private function dummy( $value, $params ){
		return array( CODE => false, ERROR => 'DUMMY' );
	}

// | カスタムチェック関数
// +----------------------------------------------
	
	// 講義予約重複チェック（新規モード用）
	private function reservation_duplicate_newlec( $data ){

		$code = true;
		$ref_pdo = new Ref_Pdo_Reserve();

		$lec_date   = $data[ 'start_y' ] . '-' . $data[ 'start_m' ] . '-' . $data[ 'start_d' ];
		$end_date   = $data[ 'end_y' ]   . '-' . $data[ 'end_m' ]   . '-' . $data[ 'end_d' ];
		$start_time = $data[ 'hour_start' ] . ':' . $data[ 'min_start' ] . ':00';
		$end_time   = $data[ 'hour_end' ]   . ':' . $data[ 'min_end' ]   . ':00';

		$rooms = explode( '|', $data[ 'rooms' ] );
		foreach( $rooms as $room_id ){

			if( isset( $data[ 'is_weekly' ] ) && $data[ 'is_weekly' ] === 'on' ){
		
				$obj_start_date = new DateTime( $lec_date );
				$obj_end_date   = new DateTime( $end_date );
				$arr_date       = array();			
				while( $obj_start_date < $obj_end_date ){
					$arr_date[] = $obj_start_date->format( 'Y-m-d' );
					$obj_start_date->add( new DateInterval( 'P1W' ) );
				}

				$ret = 0;
				foreach( $arr_date as $date ){
					$params = array( $room_id, $date, $end_time, $start_time );
					$ret   += $ref_pdo->check_duplicate_newlec( $params );
				}

			} else {

				$params = array( $room_id, $lec_date, $end_time, $start_time );
				$ret    = $ref_pdo->check_duplicate_newlec( $params );
			}

			if( (int)$ret ) $code = false;
		}

		$error_code = $code ? '' : 'RESERVATION_DUPLICATED';
		return array( CODE => $code, ERROR => $error_code );
	}

	// 講義予約重複チェック（編集モード用）
	private function reservation_duplicate_edit( $data ){

		$code = true;
		$ref_pdo = new Ref_Pdo_Reserve();

		$reserve_id = $data[ 'reserve_id' ];
		$lec_date   = $data[ 'lec_y' ] . '-' . $data[ 'lec_m' ] . '-' . $data[ 'lec_d' ];
		$start_time = $data[ 'hour_start' ] . ':' . $data[ 'min_start' ] . ':00';
		$end_time   = $data[ 'hour_end' ]   . ':' . $data[ 'min_end' ]   . ':00';

		$params = array( $reserve_id, $lec_date, $end_time, $start_time, $reserve_id );
		$ret    = $ref_pdo->check_duplicate_edit( $params );

		if( (int)$ret ) $code = false;

		$error_code = $code ? '' : 'RESERVATION_DUPLICATED';
		return array( CODE => $code, ERROR => $error_code );
	}

	// 講義予約重複チェック（複製モード用）
	private function reservation_duplicate_copy( $data ){

		$code = true;
		$ref_pdo = new Ref_Pdo_Reserve();

		$reserve_id = $data[ 'reserve_id' ];
		$lec_date   = $data[ 'start_y' ] . '-' . $data[ 'start_m' ] . '-' . $data[ 'start_d' ];
		$end_date   = $data[ 'end_y' ]   . '-' . $data[ 'end_m' ]   . '-' . $data[ 'end_d' ];
		$start_time = $data[ 'hour_start' ] . ':' . $data[ 'min_start' ] . ':00';
		$end_time   = $data[ 'hour_end' ]   . ':' . $data[ 'min_end' ]   . ':00';

		if( isset( $data[ 'is_weekly' ] ) && $data[ 'is_weekly' ] === 'on' ){
	
			$obj_start_date = new DateTime( $lec_date );
			$obj_end_date   = new DateTime( $end_date );
			$arr_date       = array();			
			while( $obj_start_date < $obj_end_date ){
				$arr_date[] = $obj_start_date->format( 'Y-m-d' );
				$obj_start_date->add( new DateInterval( 'P1W' ) );
			}

			$ret = 0;
			foreach( $arr_date as $date ){
				$params = array( $date, $end_time, $start_time, $reserve_id );
				$ret   += $ref_pdo->check_duplicate_copy( $params );
			}

		} else {

			$params = array( $lec_date, $end_time, $start_time, $reserve_id );
			$ret    = $ref_pdo->check_duplicate_copy( $params );
		}

		if( (int)$ret ) $code = false;

		$error_code = $code ? '' : 'RESERVATION_DUPLICATED';
		return array( CODE => $code, ERROR => $error_code );
	}

	// 講義教室が異なる同一講義名の講義存在チェック（編集・複製モード用）
	private function reservation_lec_exist_def_rooms( $data ){

		$ref_pdo = new Ref_Pdo_Reserve();
		$ret     = $ref_pdo->check_lec_exist( array( $data[ 'lec_name' ] ) );

		if( $ret ){
			$ret = $ref_pdo->check_lec_exist_def_rooms( array( $data[ 'reserve_id' ], $data[ 'lec_name' ] ) );
		}

		$code = (int)$ret ? false : true;

		$error_code = $code ? '' : 'RESERVATION_DUPLICATED';
		return array( CODE => $code, ERROR => $error_code );
	}
}
?>
