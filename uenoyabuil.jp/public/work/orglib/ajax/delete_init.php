<?php

class init implements IF_Init{
	
	private $params;
	private $mode;
	private $lec_id;
	private $reserve_id;
	private $lec_style_preset_id;
	private $room_preset_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ 'lec_id' ] ) ){
			$this->lec_id              = $this->params[ POST ][ 'lec_id' ];
			$this->mode                = LECTURE;
		}
		
		if( isset( $this->params[ POST ][ 'reserve_id' ] ) ){
			$this->reserve_id          = $this->params[ POST ][ 'reserve_id' ];
			$this->mode                = RESERVATION;
		}
		
		if( isset( $this->params[ POST ][ 'lec_style_preset_id' ] ) ){
			$this->lec_style_preset_id = $this->params[ POST ][ 'lec_style_preset_id' ];
			$this->mode                = LEC_STYLE_PRESET;
		}
		
		if( isset( $this->params[ POST ][ 'room_preset_id' ] ) ){
			$this->room_preset_id      = $this->params[ POST ][ 'room_preset_id' ];
			$this->mode                = ROOM_PRESET;
		}
		
	}

	public function set_params(){
		
		// 削除実行
		switch( $this->mode ){
			case LECTURE:
				$ret = $this->delete_lecture();
				break;
			case RESERVATION:
				$ret = $this->delete_reservation();
				break;
			case LEC_STYLE_PRESET:
				$ret = $this->delete_lec_style_preset();
				break;
			case ROOM_PRESET:
				$ret = $this->delete_room_preset();
				break;
		}

		$arr_status_str = array( RESERVATION       => '講義予約情報',
								 LECTURE           => '連続した講義予約',
								 LEC_STYLE_PRESET  => '講義形態プリセット情報',
								 ROOM_PRESET       => '教室設定プリセット情報' );

		if( $ret ){
			$this->params[ 'status' ] = $arr_status_str[ $this->mode ] . 'を消去しました。';
		} else {
			$this->params[ 'status' ] = $arr_status_str[ $this->mode ] . 'の削除に失敗しました。';
		}

		// アクセスログ
		$mode    = strtolower( $this->mode );
		$id_name = str_replace( array( 'reservation', 'lecture' ), array( 'reserve', 'lec' ), strtolower( $this->mode ) . '_id' );
		$id      = $this->$id_name;
		$detail  = array( $id_name => $id );
		Common::access_log( "{$mode}_delete", 3, $detail );

		return $this->params;

	}

	// 講義情報削除（毎週のもの）
	private function delete_lecture(){

		$upd_pdo = new Upd_Pdo_Reserve();
		$ret  = $upd_pdo->delete_lec_rooms_all( array( $this->lec_id . '%' ) );
		$ret .= "\n" . $upd_pdo->delete_reservation_all( array( $this->lec_id ) );
		$ret .= "\n" . $upd_pdo->delete_lec_style_preset_sub( array( $this->lec_id ) );
		$ret .= "\n" . $upd_pdo->delete_lec_style_preset( array( $this->lec_id ) );
		$ret .= "\n" . $upd_pdo->delete_lecture_master( array( $this->lec_id ) );

		return $ret;
	}

	// 講義予約情報削除
	private function delete_reservation(){

		$upd_pdo = new Upd_Pdo_Reserve();
		$ret  = $upd_pdo->delete_lec_rooms( array( $this->reserve_id ) );
		$ret .= "\n" . $upd_pdo->delete_reservation( array( $this->reserve_id ) );
	
		return $ret;
	}

	// 教室プリセット情報削除
	private function delete_room_preset(){

		$upd_pdo = new Upd_Pdo_RoomPreset();
		return $upd_pdo->delete_room_preset( array( $this->room_preset_id ) );
	}

	// 講義形態プリセット情報削除
	private function delete_lec_style_preset(){

		$upd_pdo = new Upd_Pdo_LecStylePreset();
		$ret  = $upd_pdo->delete_lec_style_preset_sub( array( $this->lec_style_preset_id ) );
		$ret .= "\n" . $upd_pdo->delete_lec_style_preset( array( $this->lec_style_preset_id ) );

		return $ret;
	}
}
?>
