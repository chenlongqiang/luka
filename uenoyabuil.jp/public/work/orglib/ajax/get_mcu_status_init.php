<?php

require_once( STATUS_DIR . "/machine/statusMcu.php" );
require_once( STATUS_DIR . "/room/statusRoom.php" );
require_once( STATUS_DIR . "/lecture/statusLecture.php" );
require_once( DB_DIR . "/DAO/DaoRoomMachine.php" );

class init{


	public function __construct( $params ){

		$this->params = $params;
		
		if (isset( $_POST[ 'reserve_id' ] )) {
			$this->reserve_id = $_POST[ 'reserve_id' ];
		}
		
	}

	public function set_params(){

		// MCU状態取得
		$this->reserve_id;
		
		$this->params[ 'status' ] = $this->getStatus();
		
		return $this->params;

	}

	// MCUのステータスを取得する
	// 多地点制御のボタンにセットする文字列を配列にセットして、
	// JSON形式文字列に変換して返します。
	private function getStatus() {
		
		// MCUオブジェクトを取得してセット
		$this->set_mcu();
		
		// 講義開始判断
		$lec_started = $this->is_lec_started();
		
		$SERIES_9 = 1;
		$SERIES_7 = 2;
		$mcuMachineId = $this->mcu1->getRoomMachineId();
		$replyArray = array();
		
		// 遠隔講義教室リスト取得
		$lec_rooms_list = $this->get_lec_rooms_data();
		
		// 返却値初期化
		foreach( $lec_rooms_list as $lec_room ) {
		
			$room_id = $lec_room[ 'room_id' ];
			
			if ($lec_started) {
				$replyArray[ $room_id ][ 'lec_started' ] = "STARTED";
			} else {
				$replyArray[ $room_id ][ 'lec_started' ] = "NOT_STARTED";
			}
			
			$replyArray[ $room_id ][ 'speaker9' ] = "聴講";
			$replyArray[ $room_id ][ 'speaker7' ] = "聴講";
			$replyArray[ $room_id ][ 'mute' ] = "OFF";
			$replyArray[ $room_id ][ 'content' ] = "受信";
			$replyArray[ $room_id ][ 'connect' ] = "切断";
		}
		
		// 話者教室ID取得(話者、聴講の制御を行う)
		$speakerRoom9IdArr = statusMcu::mcuGetSpeakerRoom($mcuMachineId, $this->reserve_id, $SERIES_9);
		$speakerRoom7IdArr = statusMcu::mcuGetSpeakerRoom($mcuMachineId, $this->reserve_id, $SERIES_7);
		
		if ( $speakerRoom9IdArr != -1 ) {
			foreach($speakerRoom9IdArr as $speakerRoom9Id) {
				if ($speakerRoom9Id) {
					$replyArray[ $speakerRoom9Id ][ 'speaker9' ] = "話者";
				}
			}
		}
		
		if ( $speakerRoom7IdArr != -1 ) {
			foreach($speakerRoom7IdArr as $speakerRoom7Id) {
				if ($speakerRoom7Id) {
					$replyArray[ $speakerRoom7Id ][ 'speaker7' ] = "話者";
				}
			}
		}
		
		// ミュート教室取得(ON, OFF)
		// 9系、7系両方がミュート状態の時にONとなる
		$muteStatusArr = array();
		$muteRoomIdArr9 = statusMcu::mcuGetMuteRoom($mcuMachineId, $this->reserve_id, $SERIES_9);
		
		if ( $muteRoomIdArr9 && $muteRoomIdArr9 != -1 ) {
			foreach( $muteRoomIdArr9 as $muteRoomId ) {
				$muteStatusArr[ $muteRoomId ] = "ON";
			}
		}
		
		$muteRoomIdArr7 = statusMcu::mcuGetMuteRoom($mcuMachineId, $this->reserve_id, $SERIES_7);
		if ( $muteRoomIdArr7 && $muteRoomIdArr7 != -1 ) {
			foreach( $muteRoomIdArr7 as $muteRoomId ) {
				$muteStatusArr[ $muteRoomId ] = $muteStatus[ muteRoomId ] . "ON";
			}
		}
		
		foreach( $muteStatusArr as $muteRoomId => $muteStatus ) {
			if ( $muteStatus == "ONON" ) {
				$replyArray[ $muteRoomId ][ 'mute' ] = "ON";
			}
		}
		
		// Content送信教室取得(受信、送信)
		$contentSendRoomIdArr = statusMcu::mcuGetContentRoom($mcuMachineId, $this->reserve_id);
		
		if ( $contentSendRoomIdArr != -1 ) {
			foreach( $contentSendRoomIdArr as $contentSendRoomId ) {
				if ($contentSendRoomId) {
					$replyArray[ $contentSendRoomId ][ 'content' ] = "送信";
				}
			}
		}
		
		// 接続・切断
		// どちらか接続されていれば、接続とみなす。
		$connStatusArr = array();
		$connectRoomIdArr9 = statusMcu::mcuGetConnectRoom($mcuMachineId, $this->reserve_id, $SERIES_9);
		
		if ( is_array($connectRoomIdArr9) && $connectRoomIdArr9 != -1 ) {
			foreach( $connectRoomIdArr9 as $connectRoomId ) {
				if ($connectRoomId) {
					$connStatusArr[ $connectRoomId ] = "接続";
				}
			}
		}
		
		$connectRoomIdArr7 = statusMcu::mcuGetConnectRoom($mcuMachineId, $this->reserve_id, $SERIES_7);
		
		if ( is_array($connectRoomIdArr7) && $connectRoomIdArr7 != -1 ) {
			foreach( $connectRoomIdArr7 as $connectRoomId ) {
				if ($connectRoomId) {
					$connStatusArr[ $connectRoomId ] = "接続";
				}
			}
		}
		
		foreach( $connStatusArr as $connRoomId => $connStatus ) {
			if ( $connStatus == "接続" ) {
				$replyArray[ $connRoomId ][ 'connect' ] = "接続";
			}
		}
		
		return "mcu_status:{" . $this->toJSON($replyArray) . "}";
	}
	
	// 遠隔講義教室リスト取得関数
	private function get_lec_rooms_data(){
		
		$ref_pdo = new Ref_Pdo_MultiPoint();
		return $ref_pdo->ref_multipoint( array( $this->reserve_id ) );
	}
	
	// MCU1、MCU2のオブジェクトセット
	private function set_mcu() {
		
		$DaoRoomMachine = new DaoRoomMachine();
		
		$mcu1_rec = $DaoRoomMachine->getRoomMachineIdByMachineId(1, 10);
		$mcu2_rec = $DaoRoomMachine->getRoomMachineIdByMachineId(2, 10);
		
		foreach($mcu1_rec as $mcu) {
			$this->mcu1 = $mcu;
		}
		
		foreach($mcu2_rec as $mcu) {
			$this->mcu2 = $mcu;
		}
		
	}
	
	// 配列をJSONに変換する
	private function toJSON($data) {
		$json = array();
		foreach ($data as $string => $value) {
			$key = $string . ":";
			
			if (is_array($value)) {
				$json[] = sprintf("%s {%s}", $key, $this->toJSON($value));
			} else {
				$json[] = sprintf("%s '%s'", $key, addslashes($value));
			}
		}
		return implode(", ", $json);
	}

	// 講義状態取得
	private function is_lec_started() {
		
		$statusLecture = new statusLecture();
		
		//  返り値： 0 : 講義未開始
		//          -1 : 電源異常あり
		//           1 : プリセット投入中
		//           2 : プリセット制御完了
		$status = $statusLecture->getLectureStatus( $this->reserve_id );
		
		if ( $status == 2 ) {
			return true;
		}
		
		return false;
	}
}
?>
