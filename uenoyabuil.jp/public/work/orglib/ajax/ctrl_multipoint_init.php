<?php

require_once( STATUS_DIR . "/machine/statusMcu.php" );
require_once( STATUS_DIR . "/room/statusRoom.php" );
require_once( COMMON_DIR . "/Log.php" );

/*
 * 多地点制御(主にMCUに対して制御)を行います。
 * 
 * 【制御内容】
 * 
 * 9系話者切替
 * 		指定した教室を話者に切り替えます。
 * 7系話者切替
 * 		指定した教室を話者に切り替えます。
 * 9系話者無し
 * 		全ての教室を聴講にします。
 * 7系話者無し
 * 		全ての教室を聴講にします。
 * ミュート
 * 		指定した教室のミュートオン・オフを切り替えます。
 * Content送信
 * 		指定した教室をContent送信に切り替えます。
 * 接続・切断
 * 		指定した教室を講義から接続・切断します。
 * 
 */
class init{
	
	private $log;

	public function __construct( $params ){

		$this->params = $params;
		$this->log = new Log();
		
	}

	public function set_params(){

		if ( isset($_POST[ 'action' ]) ) {
			$this->action = $_POST[ 'action' ];
			$this->set_mcu();
		}
		
		$this->params[ 'status' ] = $this->ctrlMultipoint();

		return $this->params;

	}
	
	// 多地点制御を行います。
	// @return 制御結果 JSON形式の文字列で返します。
	//					{ status : 'OK'または'ERR'の文字列
	//					  msg    : メッセージ }
	private function ctrlMultipoint() {
		
		$this->log->write(LOG_INFO, "-- start -- " . "ctrl_multipoint_init::" . __FUNCTION__ . "().");
		$this->log->write(LOG_DEBUG, "---- action = " . $this->action );
		$status = "{}";
		
		try {
			$mcuMachineId = $this->mcu1->getRoomMachineId();
			
			// パラメータの取得
			$room_id = $this->getParam( 'room_id' );
			$meet_no = $this->getParam( 'meet_no' );
			$reserve_id = $this->getParam( 'reserve_id' );
			$current_value = $this->getParam( 'current_value' );
			
			if ( $this->action == "set_speaker" ) {
				//$ret = statusMcu::mcuChangeSpeaker($mcuMachineId, $reserve_id, $meet_no, $room_id);
	$ret = 0;
				$status = $this->getReturnMessage( '話者変更しました。', $ret );
			} else if ( $this->action == "set_no_speaker" ) {
				//$ret = statusMcu::mcuNoSpeaker($mcuMachineId, $reserve_id, $meet_no)
	$ret = 0;
				$status = $this->getReturnMessage( '話者無しにしました。', $ret );
			} else if ( $this->action == "set_mute" ) {
	$ret = 0;
				if ($current_value == "ON") {
					//$ret = statusMcu::mcuMuteOff($mcuMachineId, $reserve_id, $room_id);
					$status = $this->getReturnMessage( 'ミュートオフにしました。', $ret );
				} else if ($current_value == "OFF") {
					//$ret = statusMcu::mcuMuteOn($mcuMachineId, $reserve_id, $room_id);
					$status = $this->getReturnMessage( 'ミュートしました。', $ret );
				}
			} else if ( $this->action == "set_content" ) {
	$ret = 0;
				if ($current_value == "受信") {
	//				$ret = statusRoom::roomStartContent( $room_id );
					$status = $this->getRoomReturnMessage( '送信に設定しました。', $ret );
	//			} else if ($current_value == "送信") {
	//				$status = $this->getReturnMessage( '受信に設定しました。', $ret );
				}
			} else if ( $this->action == "set_connection" ) {
	$ret = 0;
				if ($current_value == "接続") {
					//$ret = statusMcu::mcuCodecDisconnect($mcuMachineId, $reserve_id, $room_id);
					$status = $this->getReturnMessage( '切断しました。', $ret );
				} else if ($current_value == "切断") {
					//$ret = statusMcu::mcuCodecConnect($mcuMachineId, $reserve_id, $room_id);
					$status = $this->getReturnMessage( '接続しました。', $ret );
				}
			}
			
			$this->log->write(LOG_DEBUG, "---- ret = " . $ret );
			$this->log->write(LOG_INFO, "-- end -- " . "ctrl_multipoint_init::" . __FUNCTION__ . "().");
			
		} catch (Exception $e) {
			$this->log->write(LOG_ERR, "Exception " . $e->getMessage() );
			$status = "{status : 'ERR', msg : 'システムエラーが発生しました。' }";
		}
		return $status;
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
	
	// 機器制御戻りメッセージをJSON形式で返します。
	// @param $ok_msg 結果がOKの場合のメッセージをセットします。
	// @param $ret_code 結果の値をセットします。
	// @return {status : 'OK', msg: '' }
	private function getReturnMessage( $ok_msg, $ret_code ) {
		if ( $ret_code == 0 ) {
			$status = "{status : 'OK', msg: '" . $ok_msg . "' }";
		} else if ( $ret_code == -2 ) {
			$status = "{status : 'ERR', msg : '機器制御中です。'}";
		} else if ( $ret_code == -3 ) {
			$status = "{status : 'ERR', msg : '機器状態の設定に失敗しました。'}";
		} else if ( $ret_code == -4 ) {
			$status = "{status : 'ERR', msg : '機器の制御に失敗しました。'}";
		}
		
		return $status;
	}

	// 教室制御戻りメッセージをJSON形式で返します。
	// @param $ok_msg 結果がOKの場合のメッセージをセットします。
	// @param $ret_code 結果の値をセットします。
	// @return {status : 'OK', msg: '' }
	private function getRoomReturnMessage( $ok_msg, $ret_code ) {
		if ( $ret_code == 0 ) {
			$status = "{status : 'OK', msg: '" . $ok_msg . "' }";
		} else if ( $ret_code == -2 ) {
			$status = "{status : 'ERR', msg : '教室制御中です。'}";
		} else if ( $ret_code == -3 ) {
			$status = "{status : 'ERR', msg : '教室状態の設定に失敗しました。'}";
		} else if ( $ret_code == -4 ) {
			$status = "{status : 'ERR', msg : '教室の制御に失敗しました。'}";
		}
		
		return $status;
	}
	
	// POSTから指定したパラメータを取得して返します。
	// 存在しない場合は null を返します。
	private function getParam( $param_name ) {
		if ( isset( $_POST[ $param_name ] ) ) {
			$param = $_POST[ $param_name ];
			$this->log->write(LOG_DEBUG, "---- " . $param_name . " = " . $param );
			return $param;
		}
		return null;
	}
}
?>
