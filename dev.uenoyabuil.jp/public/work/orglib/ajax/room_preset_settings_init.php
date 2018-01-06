<?php

class init implements IF_Init{
	
	const MACHINE_NAME_CODE = 'machine_name_code';
	const MACHINE_NAME      = 'machine_name';
	const FIELD_NAME        = 'field_name';
	const PRE_NAME          = 'pre_name';
	const ROOM_PRESET_ID    = 'room_preset_id';
	private $params;
	private $room_id;
	private $mode;
	private $room_preset_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ ROOM_ID ] ) ) $this->room_id = $this->params[ POST ][ ROOM_ID ];
		if( isset( $this->params[ POST ][ MODE ] )    ) $this->mode    = $this->params[ POST ][ MODE ];
		if( isset( $this->params[ POST ][ init::ROOM_PRESET_ID ] ) ) $this->room_preset_id = $this->params[ POST ][ init::ROOM_PRESET_ID ];

	}

	public function set_params(){

		// モード（ insert | update | copy ）
		$this->params[ MODE ] = $this->mode;
		// ボタン用キャプション
		$arr_caption = array( 'insert' => '追加',
							  'copy'   => '複製',
							  'update' => '変更' );

		$this->params[ 'btn_caption' ] = $arr_caption[ $this->mode ]; 

		if( $this->mode == 'insert' ){

			// ROOM_TYPE_ID取得
			$this->params[ 'room_type_id' ] = $this->get_room_type_id();
			
		} else {

			$this->params[ 'room_preset_id' ] = $this->room_preset_id;

			// 教室設定プリセットデータ取得
			$this->params[ 'data' ] = $this->get_room_preset_data();

			// デバイスIDの先頭2桁を教室IDと入れ替える
			foreach( $this->params[ 'data' ] as $key => &$value ){
				if( $value != -1 && preg_match( '/select$/', $key ) ){
					$value = $this->room_id . substr( $value, -4 );
				}
			}
		}

		// 映像機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' ); 
        $arr_replace_name = Spyc::YAMLLoad( CONFIG_DIR . '/replace_machine_name.yaml' );

		// +-------------------------------------
		// | 映像機器リスト取得
		// +-------------------------------------
			$arr_imaging_device = $this->get_imaging_device_data();

			$disp_group = null;

			// 機器名を追加
			foreach( $arr_imaging_device as $key => $value ){
				if( $value[ 'disp_group'] && $value[ 'disp_group'] != $disp_group ) $arr_imaging_device[ $key ][ 'group_first' ] = true;
				$disp_group = $value[ 'disp_group' ];
				// 機器表示名を追加
				if( array_key_exists( $value[ init::MACHINE_NAME_CODE ], $arr_replace_name ) ){
					$arr_imaging_device[ $key ][ init::MACHINE_NAME ] = $arr_replace_name[ $value[ init::MACHINE_NAME_CODE ] ][ $this->room_id ];
				} else {
					$arr_imaging_device[ $key ][ init::MACHINE_NAME ] = $arr_machine_name[ $value[ init::MACHINE_NAME_CODE ] ];
				}
				$arr_imaging_device[ $key ][ init::FIELD_NAME ]   = $this->convert_name_code( $value[ init::MACHINE_NAME_CODE ] );
				$arr_imaging_device[ $key ][ init::PRE_NAME ]     = $this->convert_name_code( $value[ init::MACHINE_NAME_CODE ], 'pre' );
				// ついでに映像ソース選択肢用機器リストも取得
				$arr_imaging_device[ $key ][ 'source_devices' ] = $this->get_source_device_data( $value[ 'room_machine_id' ] ); 
				// 機器名を追加
				foreach( $arr_imaging_device[ $key ][ 'source_devices' ] as $key => &$source_devices ){
					if( array_key_exists( $source_devices[ init::MACHINE_NAME_CODE ], $arr_replace_name ) ){
						$source_devices[ init::MACHINE_NAME ] = $arr_replace_name[ $source_devices[ init::MACHINE_NAME_CODE ] ][ $this->room_id ];
					} else {
						$source_devices[ init::MACHINE_NAME ] = $arr_machine_name[ $source_devices[ init::MACHINE_NAME_CODE ] ];
					}
				}
			}

			$this->params[ 'video_devices' ]     = $arr_imaging_device;
			$this->params[ 'cnt_video_devices' ] = count( $arr_imaging_device );

			// グループ数
			$this->params[ 'cnt_video_group' ] = $disp_group;


		// +-------------------------------------
		// | 送信出力リスト
		// +-------------------------------------
			$arr_send_list = $this->get_send_list(); 

			// 機器名を追加
			foreach( $arr_send_list as $key => $value ){
				$ret_send_list[ $key ][ init::MACHINE_NAME_CODE ] = $value;
				$ret_send_list[ $key ][ init::MACHINE_NAME ]      = $arr_machine_name[ $value[ init::MACHINE_NAME_CODE ] ];
				$ret_send_list[ $key ][ init::FIELD_NAME ]        = $this->convert_name_code( $value[ init::MACHINE_NAME_CODE ] );

				// ついでに送信出力 選択肢用機器リスト取得
				$ret_send_list[ $key ][ 'source_devices' ] = $this->get_send_device_data( $value[ 'room_machine_id' ] );
				// 機器名を追加
				foreach( $ret_send_list[ $key ][ 'source_devices' ] as $key => &$send_device ){
					if( array_key_exists( $send_device[ init::MACHINE_NAME_CODE ], $arr_replace_name ) ){
						$send_device[ init::MACHINE_NAME ] = $arr_replace_name[ $send_device[ init::MACHINE_NAME_CODE ] ][ $this->room_id ];
					} else {
						$send_device[ init::MACHINE_NAME ] = $arr_machine_name[ $send_device[ init::MACHINE_NAME_CODE ] ];
					}
				}
			}

			$this->params[ 'send_list' ]     = $ret_send_list; 
			$this->params[ 'cnt_send_list' ] = count( $arr_send_list );

		// +-------------------------------------
		// | 受信リスト
		// +-------------------------------------
			$arr_receive_list = $this->get_receive_list(); 
			// 機器名を追加
			foreach( $arr_receive_list as $key => $value ){
				$ret_receive_list[ $key ][ init::MACHINE_NAME_CODE ] = $value;
				$ret_receive_list[ $key ][ init::MACHINE_NAME ]      = $arr_machine_name[ $value ];
				$ret_receive_list[ $key ][ init::FIELD_NAME ]        = $this->convert_name_code( $value );
			}
			$this->params[ 'receive_list' ] = $ret_receive_list; 
			$this->params[ 'cnt_receive_list' ] = count( $arr_receive_list );

		// +-------------------------------------
		// | 音響制御機器リスト
		// +-------------------------------------
			$arr_sound_device = $this->get_sound_device_data();

			$disp_group = null;

			// 機器名を追加
			foreach( $arr_sound_device as $key => $value ){
				if( $value[ 'disp_group'] && $value[ 'disp_group'] != $disp_group ) $arr_sound_device[ $key ][ 'group_first' ] = true;
				$disp_group = $value[ 'disp_group' ];
				// 機器表示名を追加
				if( array_key_exists( $value[ 'comment' ], $arr_replace_name ) && array_key_exists( $this->room_id, $arr_replace_name[ $value[ 'comment' ] ] ) ){
					$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $arr_replace_name[ $value[ 'comment' ] ][ $this->room_id ];
				} else {
					$arr_sound_device[ $key ][ init::MACHINE_NAME ] = $arr_machine_name[ $value[ 'comment' ] ];
				}
				// Send PC、Local PC を PC1、PC2 に読み替え
				if( strpos( $value[ 'comment' ], 'Send PC' ) !== false  ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( "Send PC-",  "PC1_", $value[ 'comment' ]);
				if( strpos( $value[ 'comment' ], 'Local PC' ) !== false ) $arr_sound_device[ $key ][ 'comment' ] = str_replace( 'Local PC-', 'PC2_', $value[ 'comment' ]);
				// comment を小文字に変換
				//$arr_sound_device[ $key ][ 'comment' ] = strtolower( $arr_sound_device[ $key ][ 'comment' ] );
				$arr_sound_device[ $key ][ 'field_name' ] = $this->convert_name_code( $arr_sound_device[ $key ][ 'comment' ] );
			}
			$this->params[ 'sound_devices' ]     = $arr_sound_device;
			$this->params[ 'cnt_sound_devices' ] = count( $arr_sound_device );

			// グループ数
			$this->params[ 'cnt_sound_group' ] = $disp_group;
		
		$room_mode = Common::get_room_mode( $this->room_id );
		$this->params[ 'room_mode' ] = $room_mode[ 'room_mode' ];

		return $this->params;

	}

	// 映像機器リスト取得関数
	private function get_imaging_device_data(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_imaging_device( array( $this->room_id ) );
	}

	// 映像ソース選択肢用リスト取得関数
	private function get_source_device_data( $room_machine_id ){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_source_device( array( $room_machine_id ) );
	}
	
	// 送信映像リスト取得関数
	private function get_send_list(){

		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_send_list( array( $this->room_id ) );
	}

	// 受信映像リスト取得関数
	private function get_receive_list(){

		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_receive_list( array( $this->room_id ) );
	}

	// 送信映像ソース選択肢用リスト取得関数
	private function get_send_device_data( $room_machine_id ){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_send_device( array( $room_machine_id ) );
	}

	// 音響制御機器リスト取得関数
	private function get_sound_device_data(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_sound_device( array( $this->room_id ) );
	}

	// 教室設定プリセットデータ取得関数
	private function get_room_preset_data(){

		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_room_preset_data( array( $this->room_preset_id ) );
	}

	// ROOM_TYPE_ID取得関数
	private function get_room_type_id(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_room_type_id( array( $this->room_id ) );

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
}
?>
