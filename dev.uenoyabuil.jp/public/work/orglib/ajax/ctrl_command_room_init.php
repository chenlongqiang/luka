<?php
require_once LEC_DIR .     "/statusLecture.php";
require_once ROOM_DIR .    "/statusRoom.php";
require_once MACHINE_DIR . "/statusProjector.php";
require_once MACHINE_DIR . "/statusScreen.php";
require_once MACHINE_DIR . "/statusCodec.php";
require_once MACHINE_DIR . "/statusSwitcher.php";
require_once MACHINE_DIR . "/statusMixer.php";
require_once MACHINE_DIR . "/statusCodec.php";
require_once MACHINE_DIR . "/statusCamera.php";
require_once MACHINE_DIR . "/statusSceneChange.php";
require_once MACHINE_DIR . "/statusPdu.php";

class init{

	const CMD   = 'cmd';
	const DEBUG = false;

	private $params;
	private $cmd;
	private $reserve_id;
	private $room_id;
	private $room_preset_id;
	private $room_machine_id;
	private $room_device_id;
	private $codec_machine_id;
	private $from_device_id;
	private $to_device_id;
	private $ctrl_flg;
	private $level;
	private $monitor_no;
	private $camera_no;
	private $p;
	private $t;
	private $z;
	private $scene_name;
	private $name;
	private $ip_address;

	public function __construct( $params ){

		$this->params = $params;
		
		// POST データを変数に格納
		foreach( $this->params[ POST ] as $key => $value ){
			if( isset( $this->params[ POST ][ $key ] ) ) $this->$key = $value;
		}
	}

	public function set_params(){

		$funcname = $this->cmd;

		if( !init::DEBUG ){

			$ret = $this->$funcname();

			$arr_msg = Spyc::YAMLLoad( CONFIG_DIR . '/ctrl_msg.yaml' );

//			$this->params[ CODE ]   = 'code:' . $ret;
//			$this->params[ STATUS ] = 'msg:'  . $arr_msg[ $this->cmd ];
			$this->params[ STATUS ] = $ret; 

			// アクセスログ
			$arr_vars = get_object_vars( $this );
			foreach( $arr_vars as $key => $var ){
				if( $var ) $detail[ $key ] = $var;
			}
			if( !array_key_exists( ROOM_ID, $detail ) ) $detail[ ROOM_ID ] = $_SESSION[ ROOM_ID ];
			unset( $detail[ PARAMS ] );
			Common::access_log( $funcname, 2, $detail );

		}
		return $this->params;

	}

	// 教室起動
	private function roomStart()             { return statusRoom::roomStart( $this->reserve_id, $this->room_id, $this->room_preset_id ); } 
	// 教室停止
	private function roomStop()              { return statusRoom::roomStop( $this->room_id, $this->room_preset_id ); }
	// 教室設定プリセット変更
	private function roomChangePreset()      { return statusRoom::roomChangePreset( $this->room_id, $this->room_preset_id ); }

	// 教室起動（ローカル用）
	private function roomStartLocal()        { return statusRoom::roomStartLocal( $this->room_id, $this->room_preset_id ); } 
	// 教室停止（ローカル用）
	private function roomStopLocal()         { return statusRoom::roomStopLocal( $this->room_id ); }

	// プロジェクタランプON
	private function projectorLampOn()       { return statusProjector::projectorLampOn( $this->room_machine_id ); }
	// プロジェクタランプOFF
	private function projectorLampOff()      { return statusProjector::projectorLampOff( $this->room_machine_id ); }
	// スクリーンUP
	private function screenUp()              { return statusScreen::screenUp( $this->room_machine_id ); }
	// スクリーンDOWN
	private function screenDown()            { return statusScreen::screenDown( $this->room_machine_id ); }

	// プロジェクタランプON（ローカル用）
	private function projectorLampOnLocal()  { return statusProjector::projectorLampOnLocal( $this->room_machine_id ); }
	// プロジェクタランプOFF（ローカル用）
	private function projectorLampOffLocal() { return statusProjector::projectorLampOffLocal( $this->room_machine_id ); }
	// スクリーンUP（ローカル用）
	private function screenUpLocal()         { return statusScreen::screenUpLocal( $this->room_machine_id ); }
	// スクリーンDOWN（ローカル用）
	private function screenDownLocal()       { return statusScreen::screenDownLocal( $this->room_machine_id ); }

	// 音量制御（単独）
	private function mixerVolumeCtrlSingle()       { return statusMixer::mixerVolumeCtrlSingle( $this->room_device_id, $this->room_id, $this->level ); } 
	// 音量制御（単独:ローカル用）
	private function mixerVolumeCtrlSingleLocal()  { return statusMixer::mixerVolumeCtrlSingleLocal( $this->room_device_id, $this->room_id, $this->level ); } 
	// 音量制御（グループ）
	private function mixerVolumeCtrlMulti()        { return statusMixer::mixerVolumeCtrlMulti( $this->room_device_id, $this->room_id, $this->level ); } 
	// 音量制御（グループ:ローカル用）
	private function mixerVolumeCtrlMultiLocal()   { return statusMixer::mixerVolumeCtrlMultiLocal( $this->room_device_id, $this->room_id, $this->level ); } 
	// ミュート（単独）
	private function mixerRoomMuteOnSingle()       { return statusMixer::mixerRoomMuteOnSingle( $this->room_device_id, $this->room_id ); } 
	private function mixerRoomMuteOffSingle()      { return statusMixer::mixerRoomMuteOffSingle( $this->room_device_id, $this->room_id ); } 
	// ミュート（単独:ローカル用）
	private function mixerRoomMuteOnSingleLocal()  { return statusMixer::mixerRoomMuteOnSingleLocal( $this->room_device_id, $this->room_id ); } 
	private function mixerRoomMuteOffSingleLocal() { return statusMixer::mixerRoomMuteOffSingleLocal( $this->room_device_id, $this->room_id ); } 
	// ミュート（グループ）
	private function mixerRoomMuteOnMulti()        { return statusMixer::mixerRoomMuteOnMulti( $this->room_device_id, $this->room_id ); } 
	private function mixerRoomMuteOffMulti()       { return statusMixer::mixerRoomMuteOffMulti( $this->room_device_id, $this->room_id ); } 
	// ミュート（グループ:ローカル用）
	private function mixerRoomMuteOnMultiLocal()   { return statusMixer::mixerRoomMuteOnMultiLocal( $this->room_device_id, $this->room_id ); } 
	private function mixerRoomMuteOffMultiLocal()  { return statusMixer::mixerRoomMuteOffMultiLocal( $this->room_device_id, $this->room_id ); } 
	// こちら側のビデオ表示
	private function codecMyVideoDisp()            { return statusCodec::codecMyVideoDisp( $this->room_machine_id, $this->monitor_no ); }
	// 相手側のビデオ表示
	private function codecYourVideoDisp()          { return statusCodec::codecYourVideoDisp( $this->room_machine_id, $this->monitor_no ); }
	// コンテント表示
	private function codecDisplayContent()         { return statusCodec::codecDisplayContent( $this->room_machine_id, $this->monitor_no ); }
	// 送信出力選択
	private function switcherCtrl()                { return statusSwitcher::switcherCtrl( $this->room_id, $this->from_device_id, $this->to_device_id, $this->ctrl_flg ); }
	// 送信出力選択（ローカル用）
	private function switcherCtrlLocal()           { return statusSwitcher::switcherCtrlLocal( $this->room_id, $this->from_device_id, $this->to_device_id, $this->ctrl_flg ); }
	// カメラPTZ制御
	private function cameraCtrlPtz()               { return statusCamera::cameraCtrlPtz( $this->room_machine_id, $this->camera_no, $this->p, $this->t, $this->z ); }
	// カメラPTZ制御（ローカル用）
	private function cameraCtrlPtzLocal()          { return statusCamera::cameraCtrlPtzLocal( $this->room_machine_id, $this->camera_no, $this->p, $this->t, $this->z ); }
	// カメラPTZ制御
	private function cameraCtrlPtzCont()           { return statusCamera::cameraCtrlPtzCont( $this->room_machine_id, $this->camera_no, $this->p, $this->t, $this->z ); }
	// カメラPTZ制御（ローカル用）
	private function cameraCtrlPtzContLocal()      { return statusCamera::cameraCtrlPtzContLocal( $this->room_machine_id, $this->camera_no, $this->p, $this->t, $this->z ); }

	// VBQuantamシーンチェンジ
	private function changeScene()                 { return statusSceneChange::changeScene( $this->room_machine_id, $this->scene_name ); }
	private function changeSceneLocal()            { return statusSceneChange::changeSceneLocal( $this->room_machine_id, $this->scene_name ); }
	// 再接続
	private function mcuConnectOther()             { return statusLecture::mcuConnectOther( $this->reserve_id, $this->name, $this->ip_address ); }
	private function p2pConnectOther()             { return statusLecture::p2pConnectOther( $this->reserve_id, $this->room_id, $this->ip_address ); }
	// 送信出力選択
	private function codecChangeSendDevice(){

		$ret  = statusSwitcher::switcherCtrl( $this->room_id, $this->from_device_id, $this->to_device_id, $this->ctrl_flg );
		$ret .= statusCodec::codecChangeSendDevice( $this->codec_machine_id, $this->from_device_id, $this->to_device_id );
	}

	// 機器再起動（電源OFF→ON）
	private function restartMachine()              { return statusPdu::restartMachine( $this->room_machine_id ); }
	// 機器再起動ローカル用（電源OFF→ON）
	private function restartMachineLocal()         { return statusPdu::restartMachineLocal( $this->room_machine_id ); }

	// テスト
	private function test(){  }
}
?>
