<?php

require_once ROOM_DIR . "/statusRoom.php";
require_once DB_DIR . "/DAO/DaoRoomMaster.php";

class init{
	
	const VIDEO_LIST   = 'video_list';
	const VIDEO_COUNT  = 'video_count';
	const MACHINE_NAME = 'machine_name';
	private $params;
	private $room_id;

	public function __construct( $params ){
		$this->params = $params;

		if( $_SESSION[ ROOM_ID ] ){

			$this->room_id = $_SESSION[ ROOM_ID ];

		} else {

			// パラメータがない場合エラー画面に遷移する。
			Common::goto_error( '000004' ); 

		}
	}

	public function set_params(){
		
		// 教室ID
		$this->params[ HIDDEN ][ ROOM_ID ] = $this->room_id;

		// 教室名
		$this->get_room_name();

		// ローカル用教室プリセットID
		$this->params[ HIDDEN ][ 'room_preset_id' ] = $this->get_local_room_preset_id();

		// 教室起動状態取得
		$this->params[ HIDDEN ][ 'room_status' ] = statusRoom::getRoomStatusLocal( $this->room_id );

		// ビデオマーカーの有無
		if( $this->params[ 'is_videomarker_exist' ] = $this->is_videomarker_exist() ){

			// ビデオマーカー room_machine_id 取得
			$this->params[ HIDDEN ][ 'videomarker_id' ] = $this->get_videomarker_id();
		}

		// +-------------------------------------
		// | 映像機器リスト取得
		// +-------------------------------------

		// YAML読み込み
        $machine_name_list = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
        $replace_name_list = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

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
			if( array_key_exists( $a_value[ 'comment' ], $replace_name_list ) && array_key_exists( $this->room_id, $replace_name_list[ $a_value[ 'comment' ] ]) ){
				$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $replace_name_list[ $a_value[ 'comment' ] ][ $this->room_id ];
			} else {
				$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $machine_name_list[ $a_value[ 'comment' ] ];
			}
			// Send PC、Local PC を PC1、PC2 に読み替え
			if( strpos( $a_value[ 'comment' ], 'Send PC' ) !== false  ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( "Send PC-",  "PC1_", $a_value[ 'comment' ]);
			if( strpos( $a_value[ 'comment' ], 'Local PC' ) !== false ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( 'Local PC-', 'PC2_', $a_value[ 'comment' ]);
			// comment から フィールド名を取得
			$arr_sound_device[ $key ][ 'field_name' ] = $this->convert_name_code( $a_value[ 'comment' ] );
		}

		$this->params[ 'sound_devices' ]     = $arr_sound_device;
		$this->params[ 'cnt_sound_devices' ] = count( $arr_sound_device );
		// グループ数
		$this->params[ 'cnt_sound_group' ]   = $disp_group;

		// 拡声音用
		$this->params[ 'master_device_id' ]  = $this->get_sound_id( 'device', 'master' );
		$this->params[ 'master_group_id' ]   = $this->get_sound_id( 'group',  'master' );

		// ステータス格納用項目
		$this->params[ HIDDEN ][ 'local_status' ] = $this->getLocalStatus();
		$this->params[ HIDDEN ][ 'flg_status' ]    = 'timer';

		return $this->params;

	}

	// 映像機器リスト取得関数
	private function get_video_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_video_list( array( $this->room_id ) );
	}
	
	// 機器ソースデバイスリスト取得関数
	private function get_video_source_list( $room_machine_id ){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		
		switch( $this->params[ CTRL_MODE ]){
			case 'NORMAL' :
				return  $ref_pdo->ref_video_source_list_local( array( $room_machine_id ) );
				break;
			case 'CLOCKTOWER'   :
			case 'TOKYO_OFFICE' :
				return  $ref_pdo->ref_video_source_list_local_clock( array( $room_machine_id ) );
				break;
		}
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

	// ローカル用プリセットID取得
	private function get_local_room_preset_id(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_local_room_preset_id( array( $this->room_id ) );
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
	
	private function get_room_name() {
		$roomDao = new DaoRoomMaster();
		$this->params[ 'room_name' ] = $roomDao->getRoomNameByRoomId($this->room_id);
	}

	// 教室制御（ローカル）画面用ステータスデータ
	private function getLocalStatus(){

		$ret[ 'room_status' ] = statusRoom::getRoomStatusLocal( $this->room_id );
		return isset( $ret ) ? trim( json_encode( $ret ) ) : ''; 
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
