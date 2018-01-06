<?php
require_once LEC_DIR     . "/statusLecture.php";
require_once ROOM_DIR    . "/statusRoom.php";
require_once MACHINE_DIR . "/statusMcu.php";
require_once DB_DIR      . "/DAO/DaoRoomMachine.php" ;

class init implements IF_Init{

	const CMD = 'cmd';

	private $params;
	private $cmd;
	private $reserve_id;
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

	// 講義状態取得（スケジュール画面用） 
	private function getLectureStatus(){
		
		// 講義ステータスランプ
		$ref_pdo = new Ref_Pdo_Schedule();
		$lecture_list = $ref_pdo->ref_today_reserve_id();

		foreach( $lecture_list as &$value ){
			$lec_status[ $value ] = statusLecture::getLectureStatus( $value );
		}
		
		if( isset( $lec_status ) ) $ret[ 'lec_status' ] = $lec_status;

		// 他の日付の講義リスト
		$auth = Common::chk_auth();
		if( $auth[ CODE ] ){
			$other_day_lec_list = $ref_pdo->ref_other_day_lec();
		} else {
			$other_day_lec_list = $ref_pdo->ref_other_day_lec_by_room( array( Common::get_my_room_id_2() ) );
		}

		if( count( $other_day_lec_list ) ) $ret[ 'other_day_lec_list' ] = $other_day_lec_list;

		return isset( $ret ) ? json_encode( $ret ) : ''; 
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

	// 講義状態取得（1講義分） 
	private function getLectureStatusOne(){ return statusLecture::getLectureStatus( $this->reserve_id ); }

	// 録画サーバーステータス取得
	private function getRecorderStatus() {
	
		//$mcuId = $this->getMcuMachineId();
		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );
		
		//  返り値： 0 : 未録画中 | 1 : 録画中 | -1 : 他講義が録画中
		if( $mcu_id ) return statusMcu::mcuGetRecorderStatus( $mcu_id, $this->reserve_id );
	}
	
	// 録画サーバー残量取得
	private function getRecorderUsageRate() {
	
		//$mcuId = $this->getMcuMachineId();
		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );
		if( $mcu_id ) return statusMcu::mcuGetRecorderUsageRate( $mcu_id );
	}

	// 講義確認画面用ステータスデータ
	private function getConfirmStatus(){
		
		// 時計台、品川オフィスか否かのフラグ
		$flg = false;
		foreach( $this->get_lec_rooms_data2() as $value ){
			$room_mode = Common::get_room_mode( $value );
			if( $room_mode === 'clocktower' || strpos( $room_mode[ ROOM_MODE ], 'tokyo_office' ) !== false ){
				$flg = true;
				break;
			}
		}

		$ref_pdo = new Ref_Pdo_Ctrl();

		$ret[ 'lec_status'     ] = $this->getLectureStatusOne();
		$ret[ 'lamp_status'    ] = $this->getRoomStatus();
		$ret[ 'current_preset' ] = $ref_pdo->ref_lec_style_preset_id( array( $this->reserve_id ) ); 

		if( !$flg ){
			$ret[ 'recorder_status'     ] = $this->getRecorderStatus();
			$ret[ 'recorder_usage_rate' ] = $this->getRecorderUsageRate();
		}

		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// 多地点制御画面用ステータスデータ
	private function getMultiPointStatus(){

		$ret[ 'lamp_status'    ] = $this->getRoomStatus();
		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// 教室制御（遠隔）講師画面用ステータスデータ
	private function getRoom2Status(){

		$ret[ 'room_status' ] = statusRoom::getRoomStatus( $this->room_id );
		
        $ref_pdo  = new Ref_Pdo_CtrlRoom2();
		$ref_pdo2 = new Ref_Pdo_Ctrl();

		// プロジェクタ machine_name_code 取得
		$arr_prj_codes = $ref_pdo2->ref_projector_machine_name_code( array( $this->room_id ) );

		// 機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
		$arr_replace_name = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

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

			if( $ret[ 'room_status' ] == 2 || $ret[ 'room_status' ] == 1 ){
				// 起動中
				$source = $ref_pdo->$funcname ( array( $this->room_id ) );
			} else {
				// 起動中以外
				$source = $ref_pdo->$funcname2( array( $this->room_preset_id ) );
			}

			// 機器表示名を追加
			if( $source && array_key_exists( $source, $arr_replace_name ) ){
				if( $source ) $ret[ 'projector_source' ][ $code ] = $arr_replace_name[ $source ][ $this->room_id ];
			} else {
				if( $source ) $ret[ 'projector_source' ][ $code ] = $arr_machine_name[ $source ];
			}
		}

		if( $ret[ 'room_status' ] == 2 || $ret[ 'room_status' ] == 1 ){
			// 起動中
			$ret[ 'current_preset' ] = $ref_pdo2->ref_room_preset_id( array( $this->room_id ) ); 
		} else {
			// 起動中以外
			$ret[ 'current_preset' ] = $ref_pdo2->ref_room_preset_id2( array( $this->room_id, $this->reserve_id ) ); 
		}

		// VN_QuantamプリセットID取得
		$room_mode = Common::get_room_mode( $this->room_id );
		if( $room_mode[ ROOM_MODE ] === 'clocktower' ){
			switch( $ret[ 'room_status' ] ){
				case  0 :
				case -1 :
					$ret[ 'vn_quantam_preset_id' ] = $ref_pdo->ref_vn_quantam_preset_id2( array( $this->room_preset_id ) );
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

	// 教室制御（遠隔）詳細画面用ステータスデータ
	private function getRoomStatusDetail(){

		$ret[ 'room_status' ] = statusRoom::getRoomStatus( $this->room_id );
		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// 教室制御（ローカル）講師画面用ステータスデータ
	private function getLocal2Status(){

		$ret[ 'room_status' ] = statusRoom::getRoomStatusLocal( $this->room_id );

        $ref_pdo  = new Ref_Pdo_CtrlRoom2();
		$ref_pdo2 = new Ref_Pdo_Ctrl();

		// 機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
		$arr_replace_name = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

		// プロジェクタ machine_name_code 取得
		$arr_prj_codes = $ref_pdo2->ref_projector_machine_name_code( array( $this->room_id ) );

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
				$source = $ref_pdo->$funcname2( array( $this->room_preset_id ) );
			}

			// 他の講義で使用中の場合、講義IDを取得
			if( $ret[ 'room_status' ] == -1 ) $ret[ 'active_reserve_id' ] = $this->get_active_reserve_id();

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
			switch( $ret[ 'room_status' ] ){
				case  0 :
				case -1 :
					$ret[ 'vn_quantam_preset_id' ] = $ref_pdo->ref_vn_quantam_preset_id2( array( $this->room_preset_id ) );
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

	// 他の講義で使用中の場合、その講義のIDを取得
	private function get_active_reserve_id(){
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_active_reserve_id( array( $this->room_id ) );
	}

	// 教室制御（ローカル）詳細画面用ステータスデータ
	private function getLocalStatus(){

		$ret[ 'room_status' ] = statusRoom::getRoomStatusLocal( $this->room_id );
		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// 遠隔講義教室リスト取得関数2
	private function get_lec_rooms_data2(){

		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_lec_rooms2( array( $this->reserve_id ) );
	}

	// 教室状態データ（教室状態一覧用）
	private function getRoomStatusList(){

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

	// 講義・教室状態データ（システムステータス用）
	private function getSystemStatusList(){

		$ref_pdo  = new Ref_Pdo_Management();
		$lecs  = $ref_pdo->ref_active_lec_ids();
		$rooms = $ref_pdo->ref_active_room_ids();

		foreach( $lecs  as $value ){
			$ret[ 'lec_status'  ][ $value ] = statusLecture::getLectureStatus( $value );
		}

		foreach( $rooms as $value ){
			$ret[ 'room_status' ][ $value ] = statusRoom::getRoomStatus( $value );
		}

		return isset( $ret ) ? json_encode( $ret ) : null;
	}
}
?>
