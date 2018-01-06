<?php
require_once ROOM_DIR . "/statusRoom.php";
require_once DB_DIR   . "/DAO/DaoRoomMaster.php";

class init implements IF_Init{

	private $params;
	private $room_id;
	private $reserve_id;
	private $room_type_id;

	public function __construct( $params ){
		$this->params = $params;

		if ( isset( $this->params[ POST ] ) && isset( $this->params[ POST ][ ROOM_ID ] ) ){

			$this->room_id = $this->params[ POST ][ ROOM_ID ];
			$_SESSION[ ROOM_ID ] = $this->room_id;

		} else if ( isset( $_SESSION[ ROOM_ID ] ) ){

			$this->room_id = $_SESSION[ ROOM_ID ];

		} else {

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000004' ); 
		}

		if ( isset( $this->params[ POST ] ) && isset( $this->params[ POST ][ RESERVE_ID ] ) ){

			$this->reserve_id = $this->params[ POST ][ RESERVE_ID ];
			$_SESSION[ RESERVE_ID ] = $this->reserve_id;

		} else if ( isset( $_SESSION[ RESERVE_ID ] ) ){

			$this->reserve_id = $_SESSION[ RESERVE_ID ];

		} else {

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000003' ); 
		}
	}

	public function set_params(){

		// 教室ID
		$this->params[ ROOM_ID ] = $this->room_id;
		$this->params[ HIDDEN ][ ROOM_ID ] = $this->room_id;

		// 講義予約ID
		$this->params[ HIDDEN ][ RESERVE_ID ] = $this->reserve_id;

		// 教室名
		$this->params[ 'room_name' ] = $this->get_room_name();

		// ROOM_TYPE_ID取得
		$this->room_type_id = $this->get_room_type_id();

		// 教室プリセットリスト取得
		$this->params[ 'preset' ] = $this->get_preset_data();

		// 初期ROOM_PRESET_ID取得
		$this->params[ HIDDEN ][ 'room_preset_id' ] = $this->get_room_preset_id();

		// 教室起動状態取得
		$this->params[ HIDDEN ][ 'room_status' ]    = $this->getRoomStatus();

		// 拡声音用データ取得
		$this->params[ 'master_device_id' ]  = $this->get_sound_id( 'device', 'master' );
		$this->params[ 'master_group_id' ]   = $this->get_sound_id( 'group',  'master' );
		$this->params[ HIDDEN ][ 'master_status' ]     = $this->get_sound_status();

		// ステータス格納用項目
		$this->params[ HIDDEN ][ 'room2_status' ] = $this->getRoom2Status();
		$this->params[ HIDDEN ][ 'flg_status' ]   = 'timer';
	
		// 教室機器一覧
		$this->params[ HIDDEN ][ 'machines' ] = implode( '', $this->get_room_machine_list() );

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

		// ビデオマーカーの有無
		if( $this->params[ 'is_videomarker_exist' ] = $this->is_videomarker_exist() ){

			// ビデオマーカー room_machine_id 取得
			$this->params[ HIDDEN ][ 'videomarker_id' ] = $this->get_videomarker_id();
		}

		// room_mode 設定
		$ret = Common::get_room_mode( $this->room_id );
		$this->params[ ROOM_MODE ]      = $ret[ ROOM_MODE ];
		$this->params[ self::MAP_PATH ] = $ret[ self::MAP_PATH ]; 

		// シーンチェンジャー用データ取得
		if( $ret[ ROOM_MODE ]=== 'clocktower' ){

			$this->params[ 'vn_quantam_preset_list' ]    = Spyc::YAMLLoad( CONFIG_DIR . '/vn_quantam_preset.yaml' );
			$this->params[ HIDDEN ][ 'scenechanger_id' ] = $this->get_scenechanger_machine_id();
		}

		return $this->params;

	}

	// 教室プリセットリスト取得関数
	private function get_preset_data(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_room_preset( array( $this->room_type_id ) );
	}

    // ROOM_TYPE_ID取得関数
    private function get_room_type_id(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();
        return $ref_pdo->ref_room_type_id( array( $this->room_id ) );
    }

    // 教室状態取得
    private function getRoomStatus() { return statusRoom::getRoomStatus( $this->room_id ); }

	// ROOM_PRESET_ID取得関数
    private function get_room_preset_id(){

        $ref_pdo = new Ref_Pdo_CtrlRoom2();

        //  返り値： 0 : 教室未使用
        //          -1 : 電源異常あり
        //           1 : プリセット投入中
        //           2 : プリセット制御完了
		$roomStatus = $this->getRoomStatus();
        if( $roomStatus == 0 || $roomStatus == -1 || $roomStatus == 3 ){

            // 未起動の場合は、プリセットテーブルから取得
			return $ref_pdo->ref_room_preset_id( array( $this->room_id, $this->reserve_id ) );

        } else if( $roomStatus == 1 || $roomStatus == 2 ){

            // プリセット投入中・プリセット制御完了は、制御から取得
            return $ref_pdo->ref_ctrl_room_preset_id( array( $this->room_id ) );
        }

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
				return $ret[ 'room_device_id' ];
				break;
			case 'machine':
				return $ret[ 'room_machine_id' ];
				break;
			case 'group':
				return $ret[ 'sound_group_id' ];
				break;
		}
	}
	
	// 拡声音量ステータス取得
	private function get_sound_status(){

        $ref_pdo = new Ref_Pdo_Ctrl();

        //  返り値： 0 : 教室未使用
        //          -1 : 電源異常あり
        //           1 : プリセット投入中
        //           2 : プリセット制御完了
		$roomStatus = $this->getRoomStatus();
        if( $roomStatus == 0 || $roomStatus == -1 || $roomStatus == 3 ){

            // 未起動の場合は、プリセットテーブルから取得
			$ret = $ref_pdo->ref_room_preset_master_sound_data( array( $this->room_id, $this->reserve_id ) );

        } else if( $roomStatus == 1 || $roomStatus == 2 ){

            // プリセット投入中・プリセット制御完了は、制御から取得
            $ret = $ref_pdo->ref_room_ctrl_master_sound_data( array( $this->room_id ) );
        }

        $ret_str = '';
        foreach( $ret as $key => $value ){
            $ret_str .= $ret_str == '' ? "$key=$value" : "&$key=$value";
        }
        return $ret_str;
	}

	private function get_room_name() {
		$roomDao = new DaoRoomMaster();
		return $roomDao->getRoomNameByRoomId( $this->room_id );
	}

	private function get_room_machine_list(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_room_machine_list( array( $this->room_id ) );
	}

	// 教室制御（遠隔）画面用ステータスデータ
	private function getRoom2Status(){
		
		$ret[ 'room_status' ] = statusRoom::getRoomStatus( $this->room_id );
		
        $ref_pdo  = new Ref_Pdo_CtrlRoom2();
		$ref_pdo2 = new Ref_Pdo_Ctrl();

		// 機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
		$arr_replace_name = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

		// プロジェクタ machine_name_code 取得
		$arr_prj_codes = $ref_pdo2->ref_projector_machine_name_code( array( $this->room_id ) );

		$ret[ 'current_preset' ] = $this->get_room_preset_id(); 

		foreach( $arr_prj_codes as $value ){

			if( strpos( $value, 'Projector' ) !== false ){

				$code = strtolower( str_replace( 'Projector', '', $value ) );

				$funcname  = 'ref_projector_source_' . $code;
				$funcname2 = 'ref_projector_source_' . $code . '2';

			} else {

				$code = strtolower( $value );

				$funcname  = 'ref_source_' . $code;
				$funcname2 = 'ref_source_' . $code . '2';

			}

			if( $ret[ 'room_status' ] == 2 ){
				// 起動中
				$source = $ref_pdo->$funcname ( array( $this->room_id ) );
			} else {
				// 起動中以外
				$source = $ref_pdo->$funcname2( array( $this->get_room_preset_id() ) );
			}

			// 機器表示名を追加
			if( $source && array_key_exists( $source, $arr_replace_name ) ){
				if( $source ) $ret[ 'projector_source' ][ $code ] = $arr_replace_name[ $source ][ $this->room_id ];
			} else {
				if( $source ) $ret[ 'projector_source' ][ $code ] = $arr_machine_name[ $source ];
			}
		}

		// VN_QuantamプリセットID取得
		$room_mode = Common::get_room_mode( $this->room_id );
		if( $room_mode[ ROOM_MODE ] === 'clocktower' ){
			switch( $this->getRoomStatus() ){
				case  0 :
				case -1 :
					$ret[ 'vn_quantam_preset_id' ] = $ref_pdo->ref_vn_quantam_preset_id2( array( $this->get_room_preset_id() ) );
					break;
				case  1 :
				case  2 :
				case  3 :
					$ret[ 'vn_quantam_preset_id' ] = $ref_pdo->ref_vn_quantam_preset_id( array( $this->room_id ) );
					break;
			}
		}

		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	private function get_scenechanger_machine_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_scenechanger_machine_id( array( $this->room_id ) );
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
