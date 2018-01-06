<?php
require_once LEC_DIR     . "/statusLecture.php";
require_once ROOM_DIR    . "/statusRoom.php";

class init implements IF_Init{
	
	private $params;

	public function __construct( $params ){

		$this->params = $params;
		
		// アクセスレベルチェック
		$ret = Common::chk_auth();

		if( !$ret[ CODE ] || $ret[ LEVEL ] !== 'STAFF' ){

			// アクセス権限がない場合エラー画面に遷移する。
			Common::goto_error( '000010' );
		}

	}

	public function set_params(){
		
		$room_status_list = $this->get_room_status_list();

		foreach( $room_status_list as &$row ){

			$row[ 'lec_rooms' ] = $this->get_lec_room_list( $row[ 'reserve_id' ], $row[ 'room_id' ] );

		}
		
		// 教室状態リスト
		$this->params[ 'room_status_list' ] = $room_status_list;

		// 状態データ[教室・講義・録画]
		$this->params[ HIDDEN ][ 'status_data' ] = $this->get_status_data();
		$this->params[ HIDDEN ][ 'flg_status'  ] = '';	// 今のところJSエラー回避用

		return $this->params;
	}

	// 教室状態用リスト取得
	private function get_room_status_list(){
	
		$ref_pdo = new Ref_Pdo_Management();
		return $ref_pdo->ref_room_status_list();
	}

	// 講義教室リスト取得
	private function get_lec_room_list( $reserve_id, $room_id ){
	
		$ref_pdo = new Ref_Pdo_Management();
		return $ref_pdo->ref_lec_room_list( array( $reserve_id, $room_id ) );
	}

	// 教室状態データ
	private function get_status_data(){

		$ref_pdo  = new Ref_Pdo_Management();
		$rooms = $ref_pdo->ref_room_ids();

		foreach( $rooms as &$row ){
			
			$row[ 'room_status' ] = statusRoom::getRoomStatus( $row[ 'room_id' ] );
			$row[ 'lec_status'  ] = statusLecture::getLectureStatus( $row[ 'reserve_id' ] );
			$row[ 'rec_status'  ] = $ref_pdo->ref_is_recording( array( $row[ 'reserve_id' ] ) );
			$row[ 'lec_name'    ] = $ref_pdo->ref_lec_name( array( substr( $row[ 'reserve_id' ], 0, 5 ) ) );
		}

		return isset( $rooms ) ? json_encode( $rooms ) : null;
	}
}
?>
