<?php

require_once DB_DIR   . "/DAO/DaoRoomMachine.php";
require_once ROOM_DIR . "/statusRoom.php";

class init implements IF_Init{
	
	private $params;
	private $reserve_id;

	public function __construct( $params ){
		$this->params = $params;

		if( $_SESSION[ RESERVE_ID ] ){

			$this->reserve_id = $_SESSION[ RESERVE_ID ];

		} else {

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000003' ); 
		}
	}

	public function set_params(){
		
		// ステータス格納用
		$this->params[ HIDDEN ][ 'reserve_id'        ] = $this->reserve_id;
		$this->params[ HIDDEN ][ 'multipoint_status' ] = $this->getMultiPointStatus();
		$this->params[ HIDDEN ][ 'flg_status'        ] = 'timer';
		
		// 講義予約データ取得
		$this->params[ 'lec_reserve_data' ] = $this->get_lec_reserve_data();
		
		// 遠隔講義教室リスト取得
		$this->params[ 'lec_rooms_list' ]  = $this->get_lec_rooms_data();
		$this->params[ 'lec_rooms_count' ] = count( $this->params[ 'lec_rooms_list' ] );
		
		// 教室制御画面のURL
		$this->params[ 'ctrl_room_url' ]  = 'crtl_room.php';

		// MCU1、MCU2のリンク用URL
		$this->set_mcu_addresses();
		
		return $this->params;
	}

	// 講義予約データ取得関数
	private function get_lec_reserve_data(){
		
		$ref_pdo = new Ref_Pdo_MultiPoint();
		$ret = $ref_pdo->ref_lec_data( array( $this->reserve_id ) );

		$obj_date = new DateTime( $ret[ 'lec_date' ] );
		$ret[ 'lec_date' ] = $obj_date->format( 'Y年n月j日' );
	
		return $ret;
	}

	// 遠隔講義教室リスト取得関数
	private function get_lec_rooms_data(){
		
		$ref_pdo = new Ref_Pdo_MultiPoint();
		$ret = $ref_pdo->ref_multipoint( array( $this->reserve_id ) );

		foreach( $ret as &$value ){
			$room_mode = Common::get_room_mode( $value[ ROOM_ID ] );
			$value[ ROOM_MODE ] = $room_mode[ ROOM_MODE ];
		}

		return $ret;
	}
	
	// MCU1、MCU2のリンク用URLセット
	private function set_mcu_addresses() {
		
		$DaoRoomMachine = new DaoRoomMachine();
		
		$mcu1_rec = $DaoRoomMachine->getRoomMachineIdByMachineId( 1, 10 );
		$mcu2_rec = $DaoRoomMachine->getRoomMachineIdByMachineId( 2, 10 );
		
		foreach($mcu1_rec as $mcu) {
			$this->params[ 'mcu1_url' ] = 'http://' . $mcu->getIpAddress() . '/';
		}
		
		foreach($mcu2_rec as $mcu) {
			$this->params[ 'mcu2_url' ] = 'http://' . $mcu->getIpAddress() . '/';
		}
	}
	
	// 教室状態取得 
	private function getRoomStatus(){
		
		$ref_pdo = new Ref_Pdo_Confirm();
		$room_list = $ref_pdo->ref_lec_room_ids( array( $this->reserve_id ) );

		foreach( $room_list as &$value ){
			$ret[ $value ] = statusRoom::getRoomStatus( $value );
		}

		return $ret;
	}

	// 多地点制御画面用ステータスデータ
	private function getMultiPointStatus(){

		$ret[ 'lamp_status'    ] = $this->getRoomStatus();
		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}
}
?>
