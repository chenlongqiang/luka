<?php
require_once ROOM_DIR .    "/statusRoom.php";
require_once DB_DIR .    "/DAO/DaoRoomMaster.php";

class init{

	const CMD = 'cmd';

	private $params;
	private $cmd;
	private $room_id;
	private $room_preset_id;

	public function __construct( $params ){

		$this->params = $params;
		
		// POST データを変数に格納
		foreach( $this->params[ POST ] as $key => $value ){
			if( isset( $this->params[ POST ][ $key ] ) ) $this->$key = $value;
		}
	}

	public function set_params(){

		$funcname = $this->cmd;
		$ret = $this->$funcname();

		$this->params[ 'status' ] = $ret;

		return $this->params;

	}

	// 教室状態取得
	private function getRoomStatus() { return statusRoom::getRoomStatusLocal( $this->room_id ); }

	// 教室機器ステータス取得
	private function getMachineStatus(){
		
		$ref_pdo = new Ref_Pdo_Ctrl();
		$daoRoom = new DaoRoomMaster();
	
		$room_type_id = $daoRoom->getRoomTypeIdByRoomId( $this->room_id );
		
		//  返り値： 0 : 教室未使用
		//          -1 : 電源異常あり
		//           1 : プリセット投入中
		//           2 : プリセット制御完了
		if( $this->getRoomStatus() == 0 || $this->getRoomStatus() == -1 ){	

			// 未起動の場合は、プリセットから取得
			$ret = $ref_pdo->ref_room_status_local( array( $room_type_id ) );

		} else if( $this->getRoomStatus() == 1 || $this->getRoomStatus() == 2 ){

			// プリセット投入中・プリセット制御完了は、制御から取得
			$ret = $ref_pdo->ref_ctrl_status_local( array( $this->room_id ) );
		}

		$ret_str = '';
		foreach( $ret as $key => $value ){
		
			// 教室ごとではなく、タイプごとに設定するので、
			// 有効な機器IDが付いていないが、頭2ケタは教室IDで残り4ケタは統一されているので
			// ここで変換する。
			if ( strpos($key, "projector_select") !== false ) {
				$value = $this->replaceMachineId( $value );
			}
			
			$ret_str .= $ret_str == '' ? "$key=$value" : "&$key=$value";
		}
		return $ret_str;
	}

	private function replaceMachineId( $original ){
	
		if ( $original == "-1" ) {
			return $original;
		}
		
		$unicode = substr( $original, 2, 4);
		$newcode = $this->room_id . "" . $unicode;
		
		return $newcode;
	}
}
?>
