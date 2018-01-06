<?php

class init{

	const STATUS          = 'status';
	const NEWLEC          = 'newlec';
	const RESERVATION     = 'reservation';
	const LEC_PRESETS     = 'lec_presets';
	const LEC_PRESETS_SUB = 'lec_presets_sub';
	const LECTURE_MASTER  = 'lecture_master';
	const LEC_ROOMS       = 'lec_rooms';
	private $params;
	private $mode;
	private $reserve_id;
	private $max_other_length;
	private $real_lec_style_preset_id;
	private $new_lec_style_preset_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ MODE ] ) ){
			$this->mode = $this->params[ POST ][ MODE ];
			unset( $this->params[ POST ][ MODE ] );
		}

		if( $this->mode == 'newlec' && isset( $this->params[ POST ] ) ) $_SESSION[ init::NEWLEC ][ init::RESERVATION ] = $this->params[ POST ];

		if( isset( $this->params[ POST ][ 'reserve_id' ] ) ) $this->reserve_id = $this->params[ POST ][ 'reserve_id' ];

		// 他システムの最大数
		$this->max_other_length = 100;
	}

	public function set_params(){

		$ret_data     = $this->set_data( $this->mode );
		$data         = $ret_data[ DATA ];
		$flg_same_lec = $ret_data[ 'flg_same_lec' ];
		$lec_style_preset_sortkey = $ret_data[ 'lec_style_preset_sortkey' ];

		// 更新SQL実行
		$ret = $this->excute( $this->mode, $data , $flg_same_lec, $lec_style_preset_sortkey );

		// 毎週チェック ON の場合展開を実行
		$count = 0;
		if( $this->mode != 'update' && $data[ init::RESERVATION ][ 'is_weekly' ] !== false ){

			$start_date = new DateTime( $data[ init::RESERVATION ][ 'start_date' ] );
			$end_date   = new DateTime( $data[ init::RESERVATION ][ 'end_date' ] );
			
			while( $start_date < $end_date ){
				$arr_date[] = $start_date->format( 'Y-m-d' );
				$start_date->add( new DateInterval( 'P1W' ) );
				$count++;
			}
			unset( $arr_date[ 0 ] );					// 初日を取り除く
			$arr_date = array_values( $arr_date );		// 数値添字を振りなおす

			$ret .= $this->excute_weekly( $data, $arr_date, $flg_same_lec );
		}

		// 結果をアサイン
		if( is_array( $ret ) ){
			$this->params[ init::STATUS ] = implode( "\n", $ret );
		} else {
			$this->params[ init::STATUS ] = $ret;
		}

		// アクセスログ
		$reserve_id = $count ? $data[ init::RESERVATION ][ RESERVE_ID ] . "（{$count}件）" : $data[ init::RESERVATION ][ RESERVE_ID ];
		$detail     = array( RESERVE_ID => $reserve_id );
		Common::access_log( "reservation_{$this->mode}", 3, $detail );

		return $this->params;

	}

	// 更新SQL実行
	private function excute( $mode, $data, $flg_same_lec = false, $lec_style_preset_sortkey ){

		$upd_pdo  = new Upd_Pdo_Reserve();
		$ret = '';

		switch( $mode ){

			// 新規の場合、複製の場合
			case 'insert':
			case 'copy':

				// 講義テーブルに挿入
				if( !$flg_same_lec ) $ret .= $upd_pdo->insert_lecture_master( $data[ init::LECTURE_MASTER ] ) . " : 講義マスタ\n";

				// 講義予約テーブルに挿入
				$ret .= $upd_pdo->insert_reservation( $data[ init::RESERVATION ] ) . " : 講義予約\n";
			
				// 講義教室設定テーブルに挿入
				for( $i = 0; $i < count( $data[ init::LEC_ROOMS ] ); $i++ ){
					$ret .= $upd_pdo->insert_lec_rooms( $data[ init::LEC_ROOMS ][ $i ] ) . " : 講義教室設定\n";
				}

				if( !$flg_same_lec ){

					// 講義形態プリセットテーブルに挿入
					foreach( $data[ init::LEC_PRESETS ] as $l_key => $l_value ){
						$ret .= $upd_pdo->insert_lec_style_preset( $l_value ) . " : 講義形態プリセット\n";
						
						// 講義形態プリセットID
						$lec_style_preset_id = $upd_pdo->lastInsertId();

						// 講義形態プリセットSUBテーブルに挿入
						foreach( $data[ init::LEC_PRESETS_SUB ][ $l_key ] as $r_key => $r_value ){
							$r_value[ 'lec_style_preset_id' ] = $lec_style_preset_id ;
							$ret .= $upd_pdo->insert_lec_style_preset_sub( $r_value ) . " : 講義形態プリセットSUB\n";
						}
						
						if( $mode == 'insert' && $data[ init::RESERVATION ][ 'lec_style_preset_id' ] == $l_key ){
							// 実IDをセット
							$this->real_lec_style_preset_id = $lec_style_preset_id;
						} else if( $mode == 'copy' && $l_value[ 'sort_key' ] == $lec_style_preset_sortkey ){
							// 新IDをセット
							$this->new_lec_style_preset_id = $lec_style_preset_id;
						}
					}
				}
				
				// 講義形態プリセットIDを更新
				if( $mode == 'insert' ){

					$tmp_data = array( 'reserve_id' => $data[ init::RESERVATION ][ 'reserve_id' ], 'lec_style_preset_id' => $this->real_lec_style_preset_id );
					$this->update_lec_style_preset_id( $tmp_data );

				} else if( $mode == 'copy' && !$flg_same_lec ){

					$tmp_data = array( 'reserve_id' => $data[ init::RESERVATION ][ 'reserve_id' ], 'lec_style_preset_id' => $this->new_lec_style_preset_id );
					$this->update_lec_style_preset_id( $tmp_data );

				}

				break;

			// 変更の場合
			case 'update';

				// 講義テーブルに挿入
				$ret .= $upd_pdo->update_lecture_master( $data[ init::LECTURE_MASTER ] ) . " : 講義マスタ\n";
				// 講義予約テーブルを更新
				$ret .= $upd_pdo->update_reservation( $data[ init::RESERVATION ] ) . " : 講義予約\n";
				//$ret = $upd_pdo->update_reservation( $data[ init::RESERVATION ] );
				break;
		}

		return $ret;
	}

	// 講義予約展開実行
	private function excute_weekly( $data, $days ){
		
		if( $this->mode == 'insert' ) $data[ init::RESERVATION ][ 'lec_style_preset_id' ] = $this->real_lec_style_preset_id;

		$upd_pdo  = new Upd_Pdo_Reserve();
		$ret = '';

		for( $i = 0 ; $i < count( $days ) ; $i++ ){

			// 講義日を代入
			$data[ init::RESERVATION ][ 'lec_date' ] = $days[ $i ];

			// 講義予約IDを代入
			$lec_id     = substr( $data[ init::RESERVATION ][ 'reserve_id' ], 0, 5 );
			$reserve_id = (int)substr( $data[ init::RESERVATION ][ 'reserve_id' ], -3 ) + 1;
			$data[ init::RESERVATION ][ 'reserve_id' ] = $lec_id . sprintf( "%03d", $reserve_id );

			// 講義予約テーブルに挿入
			//$ret .= $upd_pdo->insert_reservation( $data[ init::RESERVATION ] ) . " : 講義予約\n";
			$tmp_ret = $upd_pdo->insert_reservation( $data[ init::RESERVATION ] );

			// 講義教室設定テーブルに挿入
			for( $j = 0; $j < count( $data[ init::LEC_ROOMS ] ); $j++ ){
				$data[ init::LEC_ROOMS ][ $j ][ 'reserve_id' ] = $data[ init::RESERVATION ][ 'reserve_id' ];
				$ret .= $upd_pdo->insert_lec_rooms( $data[ init::LEC_ROOMS ][ $j ] ) . " : 講義教室設定\n";
			}
		}

		return $ret;
	}

	// 同一講義名の講義IDを取得
	private function get_same_name_lec_id( $lec_name ){

		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_same_lec_name( array( $lec_name ) );
	}

	// 最終講義IDを取得
	private function get_last_lec_id(){

		$ref_pdo = new Ref_Pdo_Reserve();
		if( $ret = $ref_pdo->ref_last_lec_id() ){
			return $ret;
		} else {
			return '00000';
		}
	}

	// 最終講義予約IDを取得
	private function get_last_reserve_id( $lec_id ){

		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_last_reserve_id( array( $lec_id . '%' ) ); 
	}

	// 仮の講義形態プリセットIDを実IDに更新
	private function update_lec_style_preset_id( $data ){
		
		$upd_pdo = new Upd_Pdo_Reserve();
		return $upd_pdo->update_lec_style_preset_id( $data ); 
	}

	// 講義教室設定データを取得
	private function get_lec_rooms( $reserve_id ){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_lec_rooms( array( $reserve_id ) ); 
	}

	// 講義形態プリセットデータを取得
	private function get_lec_style_preset( $reserve_id ){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_lec_style_preset( array( $reserve_id ) ); 
	}

	// 講義形態プリセットSUBデータを取得
	private function get_lec_style_preset_sub( $lec_style_preset_id ){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_lec_style_preset_sub( array( $lec_style_preset_id ) ); 
	}

	// 選択された講義形態プリセットの sort_key を取得
	private function get_lec_style_preset_sortkey( $lec_style_preset_id ){

		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_lec_style_preset_sortkey( array( $lec_style_preset_id ) ); 
	}

	// フォームデータをテーブル用に整える
	private function set_data( $mode ){

		// デフォルト設定 YAML読み込み
        $data[ init::RESERVATION ] = Spyc::YAMLLoad( CONFIG_DIR . '/default_reserve.yaml' );
		
		// POSTデータを使用
		$form_data[ init::RESERVATION ] = $this->params[ POST ];

		// チェックボックスの値を調整
		foreach( $form_data[ init::RESERVATION ] as $key => $value ){
			if( $value == 'on' ){
				$data[ init::RESERVATION ][ $key ] = true;
			}
		}

		// 新規講義予約の場合、講義ID（連番で5桁の文字列）、講義予約ID（講義ID＋連番3桁の文字列）
		if( $mode == 'insert' || $mode == 'copy' ){
			
			// 同一講義で予約するかどうかのフラグ
			// 同一講義なら、講義形態プリセットは流用
			$flg_same_lec = false;
			$ret = $this->get_same_name_lec_id( $form_data[ init::RESERVATION ][ 'lec_name' ] );
			if( $ret ){
				// 講義IDを取得
				$lec_id = (int)$ret;
				$flg_same_lec = true;
			} else {
				// 最終講義IDを取得
				$ret = $this->get_last_lec_id();
				$lec_id = (int)$ret + 1;
			}

			// 取得した講義ID 又は 最終講義IDを用いて講義予約IDを生成
			$ret = $this->get_last_reserve_id( sprintf( "%05d", $lec_id ) );
			if( $ret ){
				$reserve_id = (int)substr( $ret, -3 ) + 1;
			} else {
				//$reserve_id = ( $form_data[ init::RESERVATION ][ 'is_weekly' ] !== false ) ? 1 : 0;
				$reserve_id = ( $data[ init::RESERVATION ][ 'is_weekly' ] !== false ) ? 1 : 0;
			}
			// データセット
	// +-----------------------------------------
	// | 講義マスタ用データ
	// +-----------------------------------------
			$data[ init::LECTURE_MASTER ][ 'lec_id' ]   = sprintf( "%05d", $lec_id );
			$data[ init::LECTURE_MASTER ][ 'lec_name' ] = $form_data[ init::RESERVATION ][ 'lec_name' ];
	// +-----------------------------------------
	// | 講義予約用データ
	// +-----------------------------------------
			$data[ init::RESERVATION ][ 'lec_id' ]      = sprintf( "%05d", $lec_id );
			$data[ init::RESERVATION ][ 'reserve_id']   = sprintf( "%05d", $lec_id ) . sprintf( "%03d", $reserve_id );

		} else if( $mode == 'update' ){
			$flg_same_lec = false;
	// +-----------------------------------------
	// | 講義マスタ用データ
	// +-----------------------------------------
			$data[ init::LECTURE_MASTER ][ 'lec_id' ]   = substr( $form_data[ init::RESERVATION ][ 'reserve_id' ], 0, 5 );
			$data[ init::LECTURE_MASTER ][ 'lec_name' ] = $form_data[ init::RESERVATION ][ 'lec_name' ];
	// +-----------------------------------------
	// | 講義予約用データ
	// +-----------------------------------------
			$data[ init::RESERVATION ][ 'lec_id' ]      = substr( $form_data[ init::RESERVATION ][ 'reserve_id' ], 0, 5 );
			$data[ init::RESERVATION ][ 'reserve_id']   = $form_data[ init::RESERVATION ][ 'reserve_id' ];

		}

		// データをテーブル用に整える
		// 実施日
		if( $mode == 'insert' || $mode == 'copy'){
			$data[ init::RESERVATION ][ 'lec_date' ] = $form_data[ init::RESERVATION ][ 'start_y' ] . '-' .
													   $form_data[ init::RESERVATION ][ 'start_m' ] . '-' .
													   $form_data[ init::RESERVATION ][ 'start_d' ];
		
			// 毎週フラグが true の場合、開始日、終了日をセット
			if( $data[ init::RESERVATION ][ 'is_weekly' ] !== false ){
				$data[ init::RESERVATION ][ 'start_date' ] = $data[ init::RESERVATION ][ 'lec_date' ];
				$data[ init::RESERVATION ][ 'end_date' ] = $form_data[ init::RESERVATION ][ 'end_y' ] . '-' .
														   $form_data[ init::RESERVATION ][ 'end_m' ] . '-' .
														   $form_data[ init::RESERVATION ][ 'end_d' ];
			}
		}else if( $mode == 'update' ){
			$data[ init::RESERVATION ][ 'lec_date' ]   = $form_data[ init::RESERVATION ][ 'lec_y' ] . '-' .
														 $form_data[ init::RESERVATION ][ 'lec_m' ] . '-' .
														 $form_data[ init::RESERVATION ][ 'lec_d' ];
			$data[ init::RESERVATION ][ 'is_weekly' ]  = $form_data[ init::RESERVATION ][ 'is_weekly' ];
			$data[ init::RESERVATION ][ 'start_date' ] = $form_data[ init::RESERVATION ][ 'start_date' ];
			$data[ init::RESERVATION ][ 'end_date' ]   = $form_data[ init::RESERVATION ][ 'end_date' ];
		}

		// 時限フラグが true の場合、開始時限、終了時限をセット
		if( $data[ init::RESERVATION ][ 'is_set_period' ] = $form_data[ init::RESERVATION ][ 'is_set_period' ] ){
			$data[ init::RESERVATION ][ 'start_period' ] = $form_data[ init::RESERVATION ][ 'start_period' ];
			$data[ init::RESERVATION ][ 'end_period' ]   = $form_data[ init::RESERVATION ][ 'end_period' ];
		}

		// 開始時刻、終了時刻をセット
		$data[ init::RESERVATION ][ 'start_time' ] = $form_data[ init::RESERVATION ][ 'hour_start' ] . ':' .
													 $form_data[ init::RESERVATION ][ 'min_start' ]  . ':00';
		$data[ init::RESERVATION ][ 'end_time' ]   = $form_data[ init::RESERVATION ][ 'hour_end' ]   . ':' .
													 $form_data[ init::RESERVATION ][ 'min_end' ]    . ':00';

		// 録画フラグが true の場合、録画開始オフセット、録画終了終了をセット
		if( $data[ init::RESERVATION ][ 'is_record' ] ){
			$data[ init::RESERVATION ][ 'rec_start_offset' ] = '00:' . $form_data[ init::RESERVATION ][ 'rec_start_offset' ] . ':00';
			$data[ init::RESERVATION ][ 'rec_end_offset' ]   = '00:' . $form_data[ init::RESERVATION ][ 'rec_end_offset' ]   . ':00';
		}

		// 接続開始オフセット、接続終了オフセットをセット
		if( $form_data[ init::RESERVATION ][ 'connect_start_offset' ] !== -1 ){
			$data[ init::RESERVATION ][ 'connect_start_offset' ] = '00:' . $form_data[ init::RESERVATION ][ 'connect_start_offset' ] . ':00';
		}
		if( $form_data[ init::RESERVATION ][ 'connect_end_offset' ] !== -1 ){
			$data[ init::RESERVATION ][ 'connect_end_offset' ]   = '00:' . $form_data[ init::RESERVATION ][ 'connect_end_offset' ]   . ':00';
		}
		// MCU関連
		if( $data[ init::RESERVATION ][ 'is_use_mcu' ] ){
			$data[ init::RESERVATION ][ 'mcu_select_flg' ] = $form_data[ init::RESERVATION ][ 'mcu_select_flg' ];
			$data[ init::RESERVATION ][ 'own_mcu_code' ]   = $form_data[ init::RESERVATION ][ 'own_mcu_code' ];
			$data[ init::RESERVATION ][ 'other_mcu_ip' ]   = $form_data[ init::RESERVATION ][ 'other_mcu_ip' ];
			$data[ init::RESERVATION ][ 'other_mcu_tel' ]  = $form_data[ init::RESERVATION ][ 'other_mcu_tel' ];
		}

		// 他システムはCSV形式で
		$data[ init::RESERVATION ][ 'other_system_list' ] = '';
		$cnt_other = 0;
		for( $i = 0 ; $i < $this->max_other_length ; $i++ ){
			if( isset( $form_data[ init::RESERVATION ][ 'other_ip_' . $i ] ) && $form_data[ init::RESERVATION ][ 'other_ip_' . $i ] !== '' ){
				if( $cnt_other >= 1 ) $data[ init::RESERVATION ][ 'other_system_list' ] .= '|';
				$data[ init::RESERVATION ][ 'other_system_list' ] .= $form_data[ init::RESERVATION ][ 'other_ip_' . $i ]   . ',' .
																	 $form_data[ init::RESERVATION ][ 'other_name_' . $i ];
				$cnt_other++;
			}
		}

		// 講義形態プリセットIDをセット（新規の場合は仮ID）
		$data[ init::RESERVATION ][ 'lec_style_preset_id' ] = $form_data[ init::RESERVATION ][ 'lec_style_preset_id' ];
		$lec_style_preset_sortkey = $this->get_lec_style_preset_sortkey( $data[ init::RESERVATION ][ 'lec_style_preset_id' ] );

		if( $mode == 'insert' ){
	// +-----------------------------------------
	// | 講義教室設定用データ
	// +-----------------------------------------
			$rooms = explode( '|', $form_data[ init::RESERVATION ][ 'rooms' ] );
			foreach( $rooms as $key => $value ){
				$data[ init::LEC_ROOMS ][ $key ][ 'room_id' ]          = $value;
				$data[ init::LEC_ROOMS ][ $key ][ 'reserve_id' ]       = $data[ init::RESERVATION ][ 'reserve_id'];
				$data[ init::LEC_ROOMS ][ $key ][ 'meeting_room_flg' ] = 1;	// とりあえず9系
			}

	// +-----------------------------------------
	// | 講義形態プリセット用データ
	// +-----------------------------------------

			// SESSION変数データ
			$form_data[ init::LEC_PRESETS ] = $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ];

			foreach( $form_data[ init::LEC_PRESETS ] as $l_key => $l_value ){
				$data[ init::LEC_PRESETS ][ $l_key ][ 'lec_style_preset_name' ] = $l_value[ 'lec_style_preset_name' ];
				$data[ init::LEC_PRESETS ][ $l_key ][ 'lec_id' ]                = $data[ init::LECTURE_MASTER ][ 'lec_id' ];
				$data[ init::LEC_PRESETS ][ $l_key ][ 'sort_key' ]              = $l_value[ 'sort_key' ];
				$data[ init::LEC_PRESETS ][ $l_key ][ 'remarks' ]               = $l_value[ 'remarks' ];
				if( isset( $l_value[ 'is_default' ] ) && $l_value[ 'is_default' ] == 'on' ){
					$data[ init::LEC_PRESETS ][ $l_key ][ 'is_default' ] = true;
				} else {
					$data[ init::LEC_PRESETS ][ $l_key ][ 'is_default' ] = false;
				}

	// +-----------------------------------------
	// | 講義形態プリセットSUB用データ
	// +-----------------------------------------
				foreach( $l_value[ 'rooms' ] as $r_key => $r_value ){
					$data[ init::LEC_PRESETS_SUB ][ $l_key ][ $r_key ][ 'room_id' ]        = $r_value[ 'room_id' ];
					$data[ init::LEC_PRESETS_SUB ][ $l_key ][ $r_key ][ 'room_preset_id' ] = $r_value[ 'room_preset_id' ];
					$data[ init::LEC_PRESETS_SUB ][ $l_key ][ $r_key ][ 'is_lecturer_9' ]  = $r_value[ 'is_lecturer_9' ];
					$data[ init::LEC_PRESETS_SUB ][ $l_key ][ $r_key ][ 'is_lecturer_7' ]  = $r_value[ 'is_lecturer_7' ];
				}
			}
		} else if( $mode == 'copy' ){
	// +-----------------------------------------
	// | 講義教室設定用データ
	// +-----------------------------------------

			$data[ init::LEC_ROOMS ] = $this->get_lec_rooms( $this->reserve_id );

			foreach( $data[ init::LEC_ROOMS ] as &$value ){
				// 新しい講義予約IDを代入
				$value[ 'reserve_id' ] = $data[ init::RESERVATION ][ 'reserve_id' ];
			}
			
			if( !$flg_same_lec ){
	// +-----------------------------------------
	// | 講義形態プリセット用データ
	// +-----------------------------------------

				$data[ init::LEC_PRESETS ] = $this->get_lec_style_preset( $this->reserve_id );

				foreach( $data[ init::LEC_PRESETS ] as $key => &$value ){
						
					// 新しい講義IDを代入
					$value[ 'lec_id' ] = $data[ init::RESERVATION ][ 'lec_id' ];

	// +-----------------------------------------
	// | 講義形態プリセットSUB用データ
	// +-----------------------------------------
					$tmp_id = $value[ 'lec_style_preset_id' ];
					$data[ init::LEC_PRESETS_SUB ][ $key ] = $this->get_lec_style_preset_sub( $tmp_id );

					// lec_style_preset_id は消去
					unset( $data[ init::LEC_PRESETS ][ $key ][ 'lec_style_preset_id' ] );
				}
			}
		}
		return array( DATA => $data, 'flg_same_lec' => $flg_same_lec, 'lec_style_preset_sortkey' => $lec_style_preset_sortkey );
	}
}
?>
