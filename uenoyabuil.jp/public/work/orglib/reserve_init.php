<?php

class init{
	
	const NEWLEC      = 'newlec';
	const SELF        = 'self';
	const INSERT      = 'insert';
	const UPDATE      = 'update';
	const COPY        = 'copy';
	const SYSTEM      = 'system';
	const RESERVATION = 'reservation';
	const LEC_PRESETS = 'lec_presets';
	const ROOMS       = 'rooms';
	const MCU         = 'mcu';
	const PERIOD_NUM  = 'period_num';
	private $params;

	public function __construct( $params ){

		$this->params = $params;
//var_dump( $_SESSION );
		// アクセスレベルチェック
		$ret = Common::chk_auth();

		if( !$ret[ CODE ] || $ret[ LEVEL ] !== 'STAFF' ){

			// アクセス権限がない場合エラー画面に遷移する
			Common::goto_error( '000010' );
		}

		if( isset( $this->params[ POST ][ MODE ] ) && $this->params[ POST ][ MODE ] !== init::SYSTEM ){
			// mode が post された場合
			$this->params[ MODE ] = $this->params[ POST ][ MODE ];
			$this->params[ HIDDEN ][ MODE ] = $this->params[ MODE ];

		} else {
			// post されなかった場合
			if( isset( $_SESSION[ MODE ] ) ) $this->params[ MODE ] = $_SESSION[ MODE ];
		}

		// MODE が取得できない場合エラー画面に遷移する
		if( !isset( $this->params[ MODE ] ) ) Common::goto_error( '000005' );
	}

	public function set_params(){

		// 確認モード時は、セッションに値を格納 mode = 'self' は不要？？
		if( $this->params[ MODE ] === init::NEWLEC || $this->params[ MODE ] === init::SELF ){ 
			
			// セッションに格納 不要？
			if( $this->params[ MODE ] === init::SELF ) $_SESSION[ init::NEWLEC ][ init::RESERVATION ] = $this->params[ POST ];

			// JS で利用するため SESSION変数に格納されたデータを HIDDEN に追加
			foreach( $_SESSION[ init::NEWLEC ][ init::RESERVATION ] as $key => $value ){
				
				$this->params[ HIDDEN ][ $key ] = $value;

			}
	
			// デフォルト設定された講義形態プリセットIDを追加
			if( isset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] ) ){
				foreach( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] as $value ){

					if( $value[ 'is_default' ] == 'true' ) $this->params[ HIDDEN ][ 'lec_style_preset_id' ] = $value[ 'lec_style_preset_id' ];
				}
			}

		} else if( $this->params[ MODE ] === init::UPDATE || $this->params[ MODE ] === init::COPY ){
			
			$_SESSION[ MODE ] = $this->params[ MODE ];
			$this->params[ HIDDEN ][ MODE ]   = $this->params[ MODE ];
			$this->params[ HIDDEN ][ 'edit' ] = '';

			if( isset( $this->params[ POST ][ RESERVE_ID ] ) || isset( $_SESSION[ RESERVE_ID ] ) ){

				if( isset( $this->params[ POST ][ RESERVE_ID ] ) ){
			
					$reserve_id =  $this->params[ POST ][ RESERVE_ID ];
					$_SESSION[ RESERVE_ID ] = $reserve_id;

				} else {

					$reserve_id =  $_SESSION[ RESERVE_ID ];
				}

				// 予約IDを HIDDEN にわたす
				$this->params[ 'reserve_id' ] = $reserve_id;

				if( isset( $_SESSION[ $this->params[ MODE ] ][ init::RESERVATION ] ) ){
			
					// SESSION データがある場合は SESSION からフォームにセット
					$data = $_SESSION[ $this->params[ MODE ] ][ init::RESERVATION ];
					$this->params[ HIDDEN ][ 'is_set_session' ] = true;
	
				} else {
				
					// DBから予約情報を取得
					$data = $this->get_reserve_data( $reserve_id );
					// フォーム用に変換
					$data = $this->set_formdata( $data, $this->params[ MODE ] );
				}

				// JS で利用するため データを HIDDEN に追加
				$this->params[ HIDDEN ][ RESERVE_ID ] = $reserve_id;
				foreach( $data as $key => $value ){
					
					$this->params[ HIDDEN ][ $key ] = $value;

				}
				// 教室リスト
				$ret = $this->get_room_list( $reserve_id );
				$this->params[ 'room_list' ] = $ret;

			} else {

				// アクセス権限がない場合エラー画面に遷移する。
				Common::goto_error( '000003' );
			}
		}

		// 講義形態プリセットが登録されている場合のフラグ
		if(  isset( $_SESSION[ init::NEWLEC ][ 'lec_presets' ] ) || $this->params[ MODE ] == init::UPDATE || $this->params[ MODE ] == init::COPY ){
			$this->params[ HIDDEN ][ 'is_preset_added' ] = 'true';
			$this->params[ 'goto_preset_btn_caption' ] = '講義形態プリセット管理画面へ';
		} else {
			$this->params[ HIDDEN ][ 'is_preset_added' ] = 'false';
			$this->params[ 'goto_preset_btn_caption' ] = 'プリセット新規追加';
		}

		// 教室リスト取得
		$this->params[ init::ROOMS ]  = $this->get_room_data();
		// MCUリスト取得
		$this->params[ init::MCU ]    = $this->get_mcu_list();

		// JS で利用するので、各時限の開始時刻、終了時刻を hidden に追加
		if( isset( $this->params[ PERIOD_START_TIME ] ) ){
			$this->params[ HIDDEN ][ PERIOD_START_TIME ] = implode( '|', $this->params[ PERIOD_START_TIME ] );
			$this->params[ HIDDEN ][ PERIOD_END_TIME ]   = implode( '|', $this->params[ PERIOD_END_TIME ] );
		}

		$this->params[ self::PERIOD_NUM ] = 5;
		return $this->params;

	}

	// 全教室リスト取得関数
	private function get_room_data(){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_all_rooms();
		
	}

	// 追加された教室リスト取得関数
	private function get_room_list( $reserve_id ){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_room_list( array( $reserve_id ) );
		
	}

	// 予約データ取得関数
	private function get_reserve_data( $reserve_id ){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_reserve_data( array( $reserve_id ) );
		
	}

	// MCUリスト取得関数
	private function get_mcu_list(){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_mcu_list();
	}

	// データをフォーム用に変換
	private function set_formdata( $data, $mode ){

		// そのままでいい項目(UPDATE)
		$arr_no_conv_update = array( 'lec_name'           ,
									 'start_date'         ,
									 'end_date'           ,
									 'is_weekly'          ,
									 'start_period'       ,
									 'end_period'         ,
									 'is_set_period'      ,
									 'is_no_auto_stop'    ,
									 'mcu_select_flg'     ,
									 'own_mcu_code'       ,
									 'other_mcu_ip'       ,
									 'other_mcu_tel'      ,
									 'lec_style_preset_id'  );
		
		// そのままでいい項目(COPY)
		$arr_no_conv_copy   = array( //'lec_name'           , <- 講義名、時間はコピーしない
									 'start_period'       ,
									 'end_period'         ,
									 'is_set_period'      ,
									 'is_no_auto_stop'    ,
									 'mcu_select_flg'     ,
									 'own_mcu_code'       ,
									 'other_mcu_ip'       ,
									 'other_mcu_tel'      ,
									 'lec_style_preset_id'  );
		
		// そのままでいい項目を代入
		$arr_name = 'arr_no_conv_' . $mode;
		foreach( $$arr_name as $value ){
			$ret[ $value ] = $data[ $value ];
		}

		// 講義日
		$lec_date = new DateTime( $data[ 'lec_date' ] );
		
		if( $mode == 'update' ){
			// 講義日
			$ret[ 'lec_y'   ] = $lec_date->format( 'Y' );
			$ret[ 'lec_m'   ] = $lec_date->format( 'm' );
			$ret[ 'lec_d'   ] = $lec_date->format( 'd' );
		} else if( $mode == 'copy' ) {
			// 講義日 = 開始日
			$ret[ 'start_y' ] = $lec_date->format( 'Y' );
			$ret[ 'start_m' ] = $lec_date->format( 'm' );
			$ret[ 'start_d' ] = $lec_date->format( 'd' );
			// 終了日
			if( $data[ 'end_date' ] !== '0000-00-00' ){
				$end_date = new DateTime( $data[ 'end_date' ] );
				$ret[ 'end_y'   ] = $end_date->format( 'Y' );
				$ret[ 'end_m'   ] = $end_date->format( 'm' );
				$ret[ 'end_d'   ] = $end_date->format( 'd' );
			}
			// 毎週フラグ
			if( $data[ 'is_weekly' ] ) $ret[ 'is_weekly' ] = 'on';
		}

		// 開始時刻
		$start_time = new DateTime( $data[ 'start_time' ] );
		
		// 終了時刻
		$end_time   = new DateTime( $data[ 'end_time' ] );
		
		// 開始、終了時刻
		$ret[ 'hour_start' ] = $start_time->format( 'H' );
		$ret[ 'min_start'  ] = $start_time->format( 'i' );
		$ret[ 'hour_end'   ] = $end_time->format( 'H' );
		$ret[ 'min_end'    ] = $end_time->format( 'i' );
		
		// 録画フラグ
		if( $data[ 'is_record' ] ) $ret[ 'is_record' ] = 'on';
		
		// 録画開始オフセット
		$rec_start_offset = new DateTime( $data[ 'rec_start_offset' ] );
		$ret[ 'rec_start_offset' ] = $rec_start_offset->format( 'i' );
		
		// 録画終了オフセット
		$rec_end_offset   = new DateTime( $data[ 'rec_end_offset' ] );
		$ret[ 'rec_end_offset'   ] = $rec_end_offset->format( 'i' );
		
		// 接続開始オフセット
		$connect_start_offset = new DateTime( $data[ 'connect_start_offset' ] );
		$ret[ 'connect_start_offset' ] = $connect_start_offset->format( 'i' );
		
		// 接続終了オフセット
		$connect_end_offset = new DateTime( $data[ 'connect_end_offset' ] );
		$ret[ 'connect_end_offset' ] = $connect_end_offset->format( 'i' );

		// 他システム
		if( $data[ 'other_system_list' ] ){
		
			$rows = explode( '|', $data[ 'other_system_list' ] );

			foreach( $rows as $key => $row ){
				$row_data = explode( ',', $row );
				$ret[ 'other_ip_' .   ( $key + 1 ) ] = trim( $row_data[ 0 ] );
				$ret[ 'other_name_' . ( $key + 1 ) ] = isset( $row_data[ 1 ] ) ? trim( $row_data[ 1 ] ) : null;
			}
		}
		
		// MCUフラグ
		if( $data[ 'is_use_mcu' ]  ) $ret[ 'is_use_mcu' ] = 'on';
		
		return $ret;
	}

}
?>
