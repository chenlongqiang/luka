<?php

require_once ROOM_DIR . "/statusRoom.php";

class init{

	const MACHINE_NAME      = 'machine_name';
	const MACHINE_NAME_CODE = 'machine_name_code';
	private $params;
	private $room_id;
	private $room_preset_id;
	private $code;
	private $mode;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) ){
			$this->room_id        = $this->params[ POST ][ ROOM_ID ];
			$this->code           = $this->params[ POST ][ CODE ];
			$this->room_preset_id = $this->params[ POST ][ 'room_preset_id' ];
			$this->mode           = $this->params[ POST ][ MODE ];
		}

	}

	public function set_params(){

		// 映像機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
        $arr_replace_name = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

		// +-------------------------------------
		// | プロジェクタソース機器リスト取得
		// +-------------------------------------
			$arr_projector_source = $this->get_machines_data();

			// 機器名を追加
			foreach( $arr_projector_source as $key => &$value ){
				// 機器表示名を追加
				if( array_key_exists( $value[ init::MACHINE_NAME_CODE ], $arr_replace_name ) ){
					$value[ init::MACHINE_NAME ] = $arr_replace_name[ $value[ init::MACHINE_NAME_CODE ] ][ $this->room_id ];
				} else {
					$value[ init::MACHINE_NAME ] = $arr_machine_name[ $value[ init::MACHINE_NAME_CODE ] ];
				}
			}

		// 機器リスト
		$this->params[ 'machines' ]          = $arr_projector_source;
		// 機器名
		$this->params[ init::MACHINE_NAME ]  = $arr_machine_name[ $this->code ];
		$this->params[ CODE ]                = $this->code;
		// 教室機器ID
		$this->params[ 'room_machine_id' ]   = $this->get_room_machine_id();
		
		// スクリーンの教室機器ID
		$this->params[ 'screen_machine_id' ] = $this->get_screen_machine_id();

		// プロジェクタのソース（前2桁を教室IDに置き換える）
		$ret_source_id = $this->get_projector_source_id();
		$this->params[ 'selected_source' ]   = ( $ret_source_id && $ret_source_id != -1 ) ? $this->room_id . substr( $ret_source_id, -4 ) : -1;
		// スクリーンのステータス
		$this->params[ 'screen_status' ]     = $this->get_screen_status();
		// ランプのステータス
		$this->params[ 'lamp_status' ]       = $this->get_lamp_status();

		// 時計台用 4Kプロジェクタ
		// room_mode 設定
		$ret = Common::get_room_mode( $this->room_id );
		$this->params[ ROOM_MODE ]      = $ret[ ROOM_MODE ];
		if( $ret[ ROOM_MODE ] === 'clocktower' ) $this->params[ '4k_machine_id' ] = $this->get_4k_machine_id();

		return $this->params;

	}

	// 機器リスト取得関数
	private function get_machines_data(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		
		switch( $this->mode ){
			case 'remote':
				return  $ref_pdo->ref_video_source_list( array( $this->get_room_machine_id() ) );
				break;
			case 'local':
			
				switch( $this->params[ CTRL_MODE ] ){
					case 'NORMAL' :
						return  $ref_pdo->ref_video_source_list_local( array( $this->get_room_machine_id() ) );
						break;
					case 'CLOCKTOWER'   :
					case 'TOKYO_OFFICE' :
						return  $ref_pdo->ref_video_source_list_local_clock( array( $this->get_room_machine_id() ) );
						break;
				}

				break;
		}
	}

	// 教室機器ID取得関数
	private function get_room_machine_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_room_machine_id( array( $this->room_id, $this->code ) );
	}

	// スクリーンの教室機器ID取得関数
	private function get_screen_machine_id(){

		if( strpos( $this->code, 'Projector' ) !== false ){

			$code = str_replace( 'Projector', 'Screen', $this->code );

			$ref_pdo = new Ref_Pdo_CtrlRoom2();
			
			// 対象の機器が存在するかどうか
			$machine_exist = $ref_pdo->ref_room_machine_exist( array( $this->room_id, $code ) );

			if( !$machine_exist ){
				return false;
			} else {
				return  $ref_pdo->ref_room_machine_id( array( $this->room_id, $code ) );
			}
		} else {

			return false;
		}
	}

	// プロジェクタのソース機器ID取得関数
	private function get_projector_source_id(){

		switch( $this->mode ){
			case 'remote':
				$status  = statusRoom::getRoomStatus( $this->room_id );		
				break;
			case 'local':
				$status  = statusRoom::getRoomStatusLocal( $this->room_id );		
				break;
		}

		$ref_pdo = new Ref_Pdo_CtrlRoom2();

		if( strpos( $this->code, 'Projector' ) !== false ){

			$selector  = strtolower( str_replace( 'Projector', '', $this->code ) );
			$funcname  = 'ref_projector_source_id_' . $selector;
			$funcname2 = 'ref_projector_source_id_' . $selector . '2';

		} else {

			$selector  = strtolower( $this->code );
			$funcname  = 'ref_source_id_' . $selector;
			$funcname2 = 'ref_source_id_' . $selector . '2';
		}

		if( $status == 2 ){
			// 起動中
			$ret = $ref_pdo->$funcname( array( $this->room_id ) );
		} else {
			// 起動中以外
			$ret = $ref_pdo->$funcname2( array( $this->room_preset_id ) );
		}

		return $ret;

	}

	// スクリーンのステータス取得関数
	private function get_screen_status(){
		

		if( strpos( $this->code, 'Projector' ) !== false ){

			$ref_pdo = new Ref_Pdo_CtrlRoom2();

			$code = str_replace( 'Projector', 'Screen', $this->code );

			// 対象の機器が存在するかどうか
			$machine_exist = $ref_pdo->ref_room_machine_exist( array( $this->room_id, $code ) );

			if( !$machine_exist ){

				return false;

			} else {

				$code     = strtolower( str_replace( 'Projector', '', $this->code ) );
				$funcname = 'ref_screen_status_' . $code;
				
				return $ref_pdo->$funcname( array( $this->room_id ) );
			}
		} else {
		
			return false;
		}
	}

	// ランプのステータス取得関数
	private function get_lamp_status(){
		
		if( strpos( $this->code, 'Projector' ) !== false ){

			$ref_pdo = new Ref_Pdo_CtrlRoom2();
		
			$code     = strtolower( str_replace( 'Projector', '', $this->code ) );
			$funcname = 'ref_lamp_status_' . $code;
			
			return $ref_pdo->$funcname( array( $this->room_id ) );

		} else if( strpos( $this->code, 'VN_Quantam' ) !== false ) {
		
			$ref_pdo = new Ref_Pdo_CtrlRoom2();
			return $ref_pdo->ref_lamp_status_4k( array( $this->room_id ) );

		} else {
			
			return false;
		}
	}

	// 4Kプロジェクタ教室機器ID取得関数
	private function get_4k_machine_id(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_4k_machine_id();
	}

}
?>
