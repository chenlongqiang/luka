<?php

class init{
	
	const ROOMS                 = 'rooms';
	const LEC_PRESETS           = 'lec_presets';
	private $params;
	private $lec_id;
	private $lec_style_preset_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) && isset( $this->params[ POST ][ LEC_ID ] ) ){
			$this->lec_id = $this->params[ POST ][ LEC_ID ];
		}

		// モード（ insert | update | copy | delete ）
		$this->mode = $this->params[ POST ][ MODE ];

		if( isset( $this->params[ POST ][ 'lec_style_preset_id' ] ) ) $this->lec_style_preset_id = $this->params[ POST ][ 'lec_style_preset_id' ];

	}

	public function set_params(){
		
		// 講義ID
		$this->params[ 'lec_id' ] = $this->lec_id;
		// 講義形態プリセットID
		if( $this->mode == 'update' || $this->mode == 'copy' ) $this->params[ 'lec_style_preset_id' ] = $this->lec_style_preset_id;

		// モード（ insert | update | copy | delete ）
		$this->params[ MODE ] = $this->mode;
		// ボタン用キャプション
		$arr_caption = array( 'insert' => '追加',
							  'copy'   => '複製',
							  'update' => '変更',
							  'delete' => '削除'  );

		$this->params[ 'btn_caption' ] = $arr_caption[ $this->mode ];
		
		if( $this->mode == 'copy' || $this->mode == 'update' ){

			// 複製モード、変更モードの場合
			// 講義形態プリセットデータ
			$ret = $this->get_lec_preset_data();
			$this->params[ 'lec_style_preset_id' ]   = $ret[ 'lec_style_preset_id' ];
			$this->params[ 'lec_style_preset_name' ] = $ret[ 'lec_style_preset_name' ];
			$this->params[ 'remarks' ]               = $ret[ 'remarks' ];
			$this->params[ 'lec_id' ]                = $ret[ 'lec_id' ];
			$this->params[ 'sort_key' ]              = $ret[ 'sort_key' ];
			$this->params[ 'is_default' ]            = $ret[ 'is_default' ];

			// 教室リスト（複製・更新用）取得関数
			$this->params[ init::ROOMS ] = $this->get_lec_preset_rooms_data_2();
		
		} else {

			// 教室リスト（プリセットリスト含む）取得
			$this->params[ init::ROOMS ] = $this->get_lec_preset_rooms_data();
		
		}

		$this->params[ 'count' ]     = count( $this->params[ init::ROOMS ] );

		return $this->params;

	}

	// 教室リスト（プリセットリスト含む）取得関数
	private function get_lec_preset_rooms_data(){
		
		$ref_pdo = new Ref_Pdo_LecStylePreset();

		$ret = $ref_pdo->ref_lec_room_list( array( $this->lec_id ) );

		foreach( $ret as &$value ){
			$value[ 'room_presets' ] = $ref_pdo->ref_room_preset_list( array( $value[ 'room_id' ] ) );
			$room_mode = Common::get_room_mode( $value[ 'room_id' ] );
			$value[ 'room_mode'    ] = $room_mode[ 'room_mode' ];
		}

		return $ret;
	}

	// 教室リスト（複製・更新用）取得関数
	private function get_lec_preset_rooms_data_2(){
		
		$ref_pdo = new Ref_Pdo_LecStylePreset();
		$ret = $ref_pdo->ref_lec_preset_sub_data( array( $this->lec_style_preset_id ) );

		foreach( $ret as $key => &$value ){
			$value[ 'room_presets' ] = $ref_pdo->ref_room_preset_list( array( $value[ 'room_id' ] ) );
			$room_mode = Common::get_room_mode( $value[ 'room_id' ] );
			$value[ 'room_mode'    ] = $room_mode[ 'room_mode' ];
		}

		return $ret;
	}

	// 講義形態プリセットデータ取得関数
	private function get_lec_preset_data(){

		$ref_pdo = new Ref_Pdo_LecStylePreset();
		return $ref_pdo->ref_lec_preset_data( array( $this->lec_style_preset_id ) );
	}
}
?>
