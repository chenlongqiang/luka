<?php
require_once LEC_DIR     . "/statusLecture.php";
require_once ROOM_DIR    . "/statusRoom.php";
require_once MACHINE_DIR . "/statusMcu.php";

class init{

	private $params;
	private $reserve_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) && isset( $this->params[ POST ][ RESERVE_ID ] ) ){

			$this->reserve_id = $this->params[ POST ][ RESERVE_ID ];
			$_SESSION[ RESERVE_ID ] = $this->reserve_id;

		} else if( isset( $_SESSION[ RESERVE_ID ] ) ){

			$this->reserve_id = $_SESSION[ RESERVE_ID ];

		} else {

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000003' );
		}
	}

	public function set_params(){

		// JSで利用するので HIDDEN に追加
		$this->params[ HIDDEN ][ RESERVE_ID ] = $this->reserve_id;

		// 講義予約データ取得
		$this->params[ 'lec_reserve_data' ]   = $this->get_lec_reserve_data();

		// 講義形態プリセットリスト取得
		$this->params[ 'lec_preset_list' ]    = $this->get_lec_preset_data();

		// 遠隔講義教室リスト取得
		$this->params[ 'lec_rooms_list' ]     = $this->get_lec_rooms_data();

		// 遠隔講義他システムリスト取得
		$arr_other_system_list = array();

		$ret = $this->get_other_system_data();
		if( $ret ){

			$ret = explode( '|', $ret );
			foreach( $ret as $key => $row ){
				$ret_data = explode( ',', $row );
				$arr_other_system_list[ $key ][ 'ip' ]   = $ret_data[ 0 ];
				$arr_other_system_list[ $key ][ 'name' ] = $ret_data[ 1 ];
			}

			$this->params[ 'other_system_list' ]  = $arr_other_system_list;
		}

		// MCU使用フラグ
		$is_use_mcu = $this->get_is_use_mcu(); 
		$this->params[ 'flg_use_mcu'  ]   = $is_use_mcu;

		// 録画を表示するか否かのフラグ
		if( $this->params[ CTRL_MODE ] === 'CLOCKTOWER' || $this->params[ CTRL_MODE ] === 'TOKYO_OFFICE' ){
			
			$this->params[ 'flg_rec_ctrl' ]   = false;

		} else if( $is_use_mcu ) {

			$this->params[ 'flg_rec_ctrl' ]   = true;

		} else {

			$this->params[ 'flg_rec_ctrl' ]   = false;
		}
		
		// 教室制御画面のURL
		$this->params[ 'ctrl_room_url' ]  = 'crtl_room.php';

		// 講義形態プリセット管理画面のURL
		$this->params[ 'lec_preset_url' ] = 'lec_preset.php';

		// ステータス格納用項目
		$this->params[ HIDDEN ][ 'lec_status'          ] = statusLecture::getLectureStatus( $this->reserve_id );
		$this->params[ HIDDEN ][ 'lec_style_preset_id' ] = $this->get_lec_style_preset_id();
		$this->params[ HIDDEN ][ 'confirm_status'      ] = $this->getConfirmStatus( $this->params[ 'flg_rec_ctrl' ] );
		$this->params[ HIDDEN ][ 'flg_status'          ] = 'timer';
		$this->params[ HIDDEN ][ 'recorder_status'     ] = ''; 
		
		return $this->params;

	}

	// 講義予約データ取得関数
	private function get_lec_reserve_data(){

		$ref_pdo = new Ref_Pdo_Confirm();

		$ret = $ref_pdo->ref_confirm_lec_data( array( $this->reserve_id ) );

		$obj_date = new DateTime( $ret[ 'lec_date' ] );
		$ret[ 'lec_date' ] = $obj_date->format( 'Y年n月j日' );

		return $ret;
	}

	// 講義形態プリセットリスト取得関数
	private function get_lec_preset_data(){

		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_lec_style_preset( array( $this->reserve_id ) );
	}

	// 遠隔講義教室リスト取得関数
	private function get_lec_rooms_data(){

		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_lec_rooms( array( $this->reserve_id ) );
	}

	// 遠隔講義教室リスト取得関数2
	private function get_lec_rooms_data2(){

		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_lec_rooms2( array( $this->reserve_id ) );
	}

	// 初期講義形態プリセットID取得
	private function get_lec_style_preset_id(){
		
		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_lec_style_preset_id( array( $this->reserve_id ) );
	}

	// 講義確認画面用ステータスデータ
	private function getConfirmStatus( $flg_rec ){
		
		$ref_pdo = new Ref_Pdo_Ctrl();

		$ret[ 'lec_status'     ]      = $this->getLectureStatusOne();
		$ret[ 'lamp_status'    ]      = $this->getRoomStatus();
		$ret[ 'current_preset' ]      = $ref_pdo->ref_lec_style_preset_id( array( $this->reserve_id ) ); 
		
		if( $this->params[ CTRL_MODE ] !== 'CLOCKTOWER' && $this->params[ CTRL_MODE ] !== 'TOKYO_OFFICE' ){

			if( $flg_rec ){
				$ret[ 'recorder_status'     ] = $this->getRecorderStatus();
				$ret[ 'recorder_usage_rate' ] = $this->getRecorderUsageRate();
			}
		}

		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

	// 録画サーバーステータス取得
	private function getRecorderStatus() {
	
		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );

		//  返り値： 0 : 未録画中 | 1 : 録画中 | -1 : 他講義が録画中
		if( $mcu_id ) return statusMcu::mcuGetRecorderStatus( $mcu_id, $this->reserve_id );
	}
	
	// 録画サーバー残量取得
	private function getRecorderUsageRate() {
	
		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );

		if( $mcu_id ) return statusMcu::mcuGetRecorderUsageRate( $mcu_id );
	}
	
	// 講義状態取得（1講義分） 
	private function getLectureStatusOne(){ return statusLecture::getLectureStatus( $this->reserve_id ); }

	// 教室状態取得 
	private function getRoomStatus(){
		
		$ref_pdo = new Ref_Pdo_Confirm();
		$room_list = $ref_pdo->ref_lec_room_ids( array( $this->reserve_id ) );

		foreach( $room_list as &$value ){
			$ret[ $value ] = statusRoom::getRoomStatus( $value );
		}

		return $ret;
	}
	
	// MCU使用フラグ取得
	private function get_is_use_mcu(){
		
		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_is_use_mcu( array( $this->reserve_id ) );
	}

	// 他システムリスト取得
	private function get_other_system_data(){
		
		$ref_pdo = new Ref_Pdo_Confirm();
		return $ref_pdo->ref_other_system_list( array( $this->reserve_id ) );
	}
}
?>
