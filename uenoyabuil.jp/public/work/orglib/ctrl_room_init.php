<?php
require_once ROOM_DIR . "/statusRoom.php";
require_once DB_DIR   . "/DAO/DaoRoomMaster.php";

class init{
	
	const CLETURERER_CAMERA = 1;
	const STUDENT_CAMERA = 2;

	const VIDEO_LIST    = 'video_list';
	const SEND_LIST     = 'send_list';
	const RECEIVE_LIST  = 'receive_list';
	const VIDEO_COUNT   = 'video_count';
	const SEND_COUNT    = 'send_count';
	const RECEIVE_COUNT = 'receive_count';
	const TOTAL_COUNT   = 'total_count';
	const MACHINE_NAME  = 'machine_name';
	private $params;
	private $room_id;
	private $reserve_id;

	public function __construct( $params ){
		$this->params = $params;

		if( $_SESSION[ ROOM_ID ] ){

			$this->room_id = $_SESSION[ ROOM_ID ];

		}else{

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000004' ); 
		}

		if( $_SESSION[ RESERVE_ID ] ){

			$this->reserve_id = $_SESSION[ RESERVE_ID ];

		}else{

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000003' ); 
		}
	}

	public function set_params(){
		
		// 教室ID
		$this->params[ ROOM_ID ]           = $this->room_id;
		$this->params[ HIDDEN ][ ROOM_ID ] = $this->room_id;
		
		// 教室名
		$this->params[ 'room_name' ] = $this->get_room_name();

		// 講義予約ID
		$this->params[ HIDDEN ][ RESERVE_ID ] = $this->reserve_id;

		// 初期ROOM_PRESET_ID取得
		$this->params[ HIDDEN ][ 'room_preset_id' ] = $this->get_room_preset_id();

		$codec_1 = $this->get_codec_1_id();
		$codec_2 = $this->get_codec_2_id();
		$this->params[ HIDDEN ][ 'codec_1' ] = $codec_1[ 'room_machine_id' ];

		// 教室プリセットリスト取得
		$this->params[ 'preset' ] = $this->get_preset_data();

		// 教室起動状態取得
		$this->params[ HIDDEN ][ 'room_status' ] = statusRoom::getRoomStatus( $this->room_id );

		// ビデオマーカーの有無
		if( $this->params[ 'is_videomarker_exist' ] = $this->is_videomarker_exist() ){

			// ビデオマーカー room_machine_id 取得
			$this->params[ HIDDEN ][ 'videomarker_id' ] = $this->get_videomarker_id();
		}

		// room_mode 設定
		$ret = Common::get_room_mode( $this->room_id );
		$this->params[ ROOM_MODE ]      = $ret[ ROOM_MODE ];

		// YAML読み込み
        $machine_name_list = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
        $replace_name_list = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );
		if( preg_match( '/clocktower|^tokyo_office/',$this->params[ ROOM_MODE ] ) ){
			$codec_conv_list = Spyc::YAMLLoad( CONFIG_DIR . '/codec_machine_conv_clocktower.yaml' );
		} else {
			$codec_conv_list = Spyc::YAMLLoad( CONFIG_DIR . '/codec_machine_conv.yaml' );
		}

		// +-------------------------------------
		// | 映像機器リスト取得
		// +-------------------------------------
		$this->params[ init::VIDEO_LIST ]  = $this->get_video_list();
		$this->params[ init::VIDEO_COUNT ] = count( $this->params[ init::VIDEO_LIST ] );
		
		// 映像機器名をYAMLから取得
		$disp_group = null;

		foreach( $this->params[ init::VIDEO_LIST ] as &$v_value ){
			// グループの先頭に値を設定
			if( $v_value[ 'disp_group' ] && $v_value[ 'disp_group' ] != $disp_group ) $v_value[ 'group_first' ] = true;
			$disp_group = $v_value[ 'disp_group' ];
			// 機器表示名を追加
			if( array_key_exists( $v_value[ 'machine_name_code' ], $replace_name_list ) ){
				$v_value[ 'machine_caption' ] = $replace_name_list[ $v_value[ 'machine_name_code' ] ][ $this->room_id ];
			} else {
				$v_value[ 'machine_caption' ] = $machine_name_list[ $v_value[ 'machine_name_code' ] ];
			}
			// name属性用
			$v_value[ 'machine_fieldname' ] = $this->convert_name_code( $v_value[ 'machine_name_code' ], 'all' );
			$v_value[ 'machine_prefix' ]    = $this->convert_name_code( $v_value[ 'machine_name_code' ], 'pre' );
			// スクリーン機器IDを追加
			$v_value[ 'screen_machine_id' ] = $this->get_screen_machine_id( $v_value[ 'machine_name_code' ] );

			// ついでに映像ソースデバイスリストも取得 
			$v_value[ 'source_list' ] = $this->get_video_source_list( $v_value[ 'room_machine_id' ] );
			// 映像機器名をYAMLから
			foreach( $v_value[ 'source_list' ] as &$source_list_v_value ){
				// 機器表示名を追加
				if( array_key_exists( $source_list_v_value[ 'machine_name_code' ], $replace_name_list ) ){
					$source_list_v_value[ 'machine_caption' ] = $replace_name_list[ $source_list_v_value[ 'machine_name_code' ] ][ $this->room_id ];
				} else {
					$source_list_v_value[ 'machine_caption' ] = $machine_name_list[ $source_list_v_value[ 'machine_name_code' ] ];
				}
			}
		}

		// グループ数
		$this->params[ 'cnt_video_group' ]   = $disp_group;

		$codec   = array();
		$codec[ $codec_1[ 'machine_name_code' ] ] = $codec_1[ 'room_machine_id' ];
		if( !preg_match( '/clocktower|^tokyo_office/',$this->params[ ROOM_MODE ] ) ) $codec[ $codec_2[ 'machine_name_code' ] ] = $codec_2[ 'room_machine_id' ];

		// +-------------------------------------
		// | 送信機器リスト取得
		// +-------------------------------------
		$this->params[ init::SEND_LIST ]  = $this->get_send_list();
		$this->params[ init::SEND_COUNT ] = count( $this->params[ init::SEND_LIST ] );

		// 送信機器名をYAMLから取得
		foreach( $this->params[ init::SEND_LIST ] as &$s_value ){
			// 機器表示名を追加
			$s_value[ 'machine_caption' ]  = $machine_name_list[ $s_value[ 'machine_name_code' ] ];
			$s_value[ 'codec_machine_id' ] = $codec[ $codec_conv_list[ $s_value[ 'machine_name_code' ] ][ 'codec' ] ];

			// ついでに映像ソースデバイスリストも取得 
			$s_value[ 'source_list' ] = $this->get_send_source_list( $s_value[ 'room_machine_id' ] );
			// 映像機器名をYAMLから
			foreach( $s_value[ 'source_list' ] as &$source_list_s_value ){
				// 機器表示名を追加
				if( array_key_exists( $source_list_s_value[ 'machine_name_code' ], $replace_name_list ) ){
					$source_list_s_value[ 'machine_caption' ] = $replace_name_list[ $source_list_s_value[ 'machine_name_code' ] ][ $this->room_id ];
				} else {
					$source_list_s_value[ 'machine_caption' ] = $machine_name_list[ $source_list_s_value[ 'machine_name_code' ] ];
				}
			}
		}

		// +-------------------------------------
		// | 受信機器リスト取得
		// +-------------------------------------
		$this->params[ init::RECEIVE_LIST ]  = $this->get_receive_list();
		$this->params[ init::RECEIVE_COUNT ] = count( $this->params[ init::RECEIVE_LIST ] );

		// 送信機器名をYAMLから取得
		foreach( $this->params[ init::RECEIVE_LIST ] as &$s_value ){
			// 機器表示名を追加
			$s_value[ 'codec_machine_id' ] = $codec[ $codec_conv_list[ $s_value[ 'machine_name_code' ] ][ 'codec' ] ];
			$s_value[ 'machine_caption' ]  = $machine_name_list[ $s_value[ 'machine_name_code' ] ];
			$s_value[ 'monitor_no' ]       = $codec_conv_list[ $s_value[ 'machine_name_code' ] ][ 'monitor_no' ];
			$s_value[ 'no' ]               = str_replace( 'Receive', '', $s_value[ 'machine_name_code' ] );
		}

		// +-------------------------------------
		// | 音響制御機器リスト
		// +-------------------------------------
		$arr_sound_device = $this->get_sound_device_list();

		// 機器名を追加
		$disp_group = null;
		foreach( $arr_sound_device as $key => $a_value ){
			// グループの先頭に値を設定
			if( $a_value[ 'disp_group' ] && $a_value[ 'disp_group' ] != $disp_group ) $arr_sound_device[ $key ][ 'group_first' ] = true;
			$disp_group = $a_value[ 'disp_group' ];
			// 機器表示名を追加
			if( array_key_exists( $a_value[ 'comment' ], $replace_name_list ) && array_key_exists( $this->room_id, $replace_name_list[ $a_value[ 'comment' ] ] ) ){
				$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $replace_name_list[ $a_value[ 'comment' ] ][ $this->room_id ];
			} else {
				$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $machine_name_list[ $a_value[ 'comment' ] ];
			}
			// Send PC、Local PC を PC1、PC2 に読み替え
			if( strpos( $a_value[ 'comment' ], 'Send PC' ) !== false  ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( "Send PC-",  "PC1_", $a_value[ 'comment' ]);
			if( strpos( $a_value[ 'comment' ], 'Local PC' ) !== false ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( 'Local PC-', 'PC2_', $a_value[ 'comment' ]);
			// comment を小文字に変換
			$arr_sound_device[ $key ][ 'field_name' ] = $this->convert_name_code( $a_value[ 'comment' ] );
		}

		$this->params[ 'sound_devices' ]     = $arr_sound_device;
		$this->params[ 'cnt_sound_devices' ] = count( $arr_sound_device );
		// グループ数
		$this->params[ 'cnt_sound_group' ]   = $disp_group;

		// 拡声音用
		$this->params[ 'master_device_id' ]  = $this->get_sound_id( 'device', 'master' );
		$this->params[ 'master_group_id' ]   = $this->get_sound_id( 'group',  'master' );

		// 送信音用
		$this->params[ 'send_9_device_id' ]  = $this->get_sound_id( 'device', 'send', '9' );
		$this->params[ 'send_9_group_id' ]   = $this->get_sound_id( 'group',  'send', '9' );

		// 受信音用
		$this->params[ 'receive_9_device_id' ]  = $this->get_sound_id( 'device', 'receive', '9' );
		$this->params[ 'receive_9_group_id' ]   = $this->get_sound_id( 'group',  'receive', '9' );

		// 送信音用
		$this->params[ 'send_7_device_id' ]  = $this->get_sound_id( 'device', 'send', '7' );
		$this->params[ 'send_7_group_id' ]   = $this->get_sound_id( 'group',  'send', '7' );

		// 受信音用
		$this->params[ 'receive_7_device_id' ]  = $this->get_sound_id( 'device', 'receive', '7' );
		$this->params[ 'receive_7_group_id' ]   = $this->get_sound_id( 'group',  'receive', '7' );
	
		$this->params[ init::TOTAL_COUNT ] = $this->params[ init::SEND_COUNT ] + $this->params[ init::RECEIVE_COUNT ];
		
		// ステータス格納用項目
		$this->params[ HIDDEN ][ 'room_status_detail' ] = $this->getRoomStatus();
		$this->params[ HIDDEN ][ 'flg_status' ]   = 'timer';
		
		// カメラNO
		$this->params[ HIDDEN ][ 'camera_no' ] = '1';

		// +-------------------------------------
		// | 再接続ボタン用データ
		// +-------------------------------------
		$is_use_mcu = $this->get_is_use_mcu();
		
		if( $is_use_mcu ){

			// MCUを使用する場合
			$mcu_select_flg = $this->get_mcu_select_flg();

			switch( $mcu_select_flg ){
				// 自MCU
				case 1:

					$this->params[ 'method' ] = 'own_mcu';
					$other_system_list = $this->get_other_system_list();
				
					if( $other_system_list ){

						$other_system_list = explode( '|', $other_system_list );

						foreach( $other_system_list as $key=>$value ){
							
							$other_system_data = explode( ',', $value );
							$this->params[ 'other_system_list' ][ $key ][ 'ip_address' ] = $other_system_data[ 0 ];
							$this->params[ 'other_system_list' ][ $key ][ 'name' ]       = $other_system_data[ 1 ];

						}
					}

					break;

				// 他MCU
				case 2:

					$this->params[ 'method' ] = 'other_mcu';
					$this->params[ 'ip_address_1' ] = $this->get_other_mcu_ip();
					break;
			}
		} else {
			
			// P2Pの場合	
			$this->params[ 'method' ] = 'p2p';

			$room_count = $this->get_room_count();
			if( $room_count == 2 ){

				$codec_ips = $this->get_another_room_codec_ip();
				$this->params[ 'ip_address_1' ] = $codec_ips[ 0 ];
				if( isset( $codec_ips[ 1 ] ) ) $this->params[ 'ip_address_2' ] = $codec_ips[ 1 ];

			} else {
				
				$other_system_list  = explode( '|', $this->get_other_system_list() );
				$other_system_first = ( $other_system_list ) ? explode( ',', $other_system_list[ 0 ] ) : null;
				$this->params[ 'ip_address_1' ] = ( $other_system_first ) ? $other_system_first[ 0 ] : '';

			}
		}
		
		return $this->params;
	}

	// 教室プリセットリスト取得関数
	private function get_preset_data(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
        $room_type_id =  $ref_pdo->ref_room_type_id( array( $this->room_id ) );
		return $ref_pdo->ref_room_preset( array( $room_type_id ) );
	}

	// 映像機器リスト取得関数
	private function get_video_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_video_list( array( $this->room_id ) );
	}
	
	// 機器ソースデバイスリスト取得関数
	private function get_video_source_list( $room_machine_id ){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_video_source_list( array( $room_machine_id ) );
	}

	// 送信リスト取得関数
	private function get_send_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_send_list( array( $this->room_id ) );
	}
	
	// 受信リスト取得関数
	private function get_receive_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_receive_list( array( $this->room_id ) );
	}
	
	// 送信ソースデバイスリスト取得関数
	private function get_send_source_list( $room_machine_id ){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_send_source_list( array( $room_machine_id ) );
	}

	// ROOM_PRESET_ID取得関数
    private function get_room_preset_id(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_room_preset_id( array( $this->room_id, $this->reserve_id ) );
    }

	// CODEC1（HDX9002）ID
    private function get_codec_1_id(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_codec_1_id( array( $this->room_id ) );
    }

	// CODEC2（HDX7002）ID
    private function get_codec_2_id(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_codec_2_id( array( $this->room_id ) );
    }

	// CODEC（時計台、品川オフィス用 HDX9004 ID（複数対応といって2つまで））
    private function get_codec_ids(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_codec_ids( array( $this->room_id ) );
    }

	// プロジェクタに対するスクリーン機器IDを取得
    private function get_screen_machine_id( $projector_name_code ){

		$screen_name_code = str_replace( 'Projector', 'Screen', $projector_name_code );

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_room_machine_id( array( $this->room_id, $screen_name_code ) );
    }

	// 音響制御機器リスト取得関数
	private function get_sound_device_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_sound_device( array( $this->room_id ) );
	}

	// 音響制御用ID取得関数
	private function get_sound_id( $id_type, $target, $hdx = '' ){
		
		// id_type : device / machine / group
		// target  : master / send / receive
		// hdx     : 7 / 9
		$funcname = 'ref_sound_device_id_' . $target;
		if( $hdx ) $funcname .= '_' . $hdx;

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		$ret = $ref_pdo->$funcname( array( $this->room_id ) );

		switch( $id_type ){
			case 'device':
				return isset( $ret[ 'room_device_id' ] ) ? $ret[ 'room_device_id' ] : null;
				break;
			case 'machine':
				return isset( $ret[ 'room_machine_id' ] ) ? $ret[ 'room_machine_id' ] : null;
				break;
			case 'group':
				return isset( $ret[ 'sound_group_id' ] ) ? $ret[ 'sound_group_id' ] : null;
				break;
		}
	}
	
	private function get_room_name() {
		$roomDao = new DaoRoomMaster();
		return $roomDao->getRoomNameByRoomId( $this->room_id );
	}

	// 教室制御（遠隔）画面用ステータスデータ
	private function getRoomStatus(){
		
		$ret[ 'room_status' ] = statusRoom::getRoomStatus( $this->room_id );
		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// machine_name_code を フィールド名に変換する
	private function convert_name_code( $name_code, $mode = 'all' ){

		$arr_field_name = Spyc::YAMLLoad( CONFIG_DIR . '/namecode_conv.yaml' );
		
		$ret = $arr_field_name[ $name_code ];
		
		if( $mode === 'pre' && strpos( $ret, 'projector' ) ){

			$prefix = strstr( $ret, '_projector', true );
		
			return $prefix;
		}

		return $ret;

	}
	
	// is_use_mcu 取得関数
	private function get_is_use_mcu(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_is_use_mcu( array( $this->reserve_id ) );
	}

	// mcu_select_flg 取得関数
	private function get_mcu_select_flg(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_mcu_select_flg( array( $this->reserve_id ) );
	}

	// 他MCU IPアドレス 取得関数
	private function get_other_mcu_ip(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_other_mcu_ip( array( $this->reserve_id ) );
	}

	// 他システムIPアドレス 取得関数
	private function get_other_system_list(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_other_system_list( array( $this->reserve_id ) );
	}

	// 接続教室数取得関数
	private function get_room_count(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_room_count( array( $this->reserve_id ) );
	}

	// 相手側教室コーデックIP取得関数
	private function get_another_room_codec_ip(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_another_room_codec_ip( array( $this->reserve_id, $this->room_id ) );
	}
	
	// ビデオマーカーの有無
	private function is_videomarker_exist(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return (bool)$ref_pdo->ref_videomarker_count( array( $this->room_id ) );
	}

	// ビデオマーカー room_machine_id 取得関数
	private function get_videomarker_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_videomarker_id( array( $this->room_id ) );
	}
}
?>
