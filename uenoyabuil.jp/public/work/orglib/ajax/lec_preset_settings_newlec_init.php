<?php

class init{

	const ROOMS                 = 'rooms';
	const PRESET_ID             = 'preset_id';
	const NEWLEC                = 'newlec';
	const LEC_PRESETS           = 'lec_presets';
	private $params;
	private $rooms;
	private $mode;
	private $preset_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) && isset( $this->params[ POST ][ init::ROOMS ] ) ){
			// 教室IDのリスト
			$this->rooms = explode( '|', $this->params[ POST ][ init::ROOMS ] );
		}

		$this->mode = $this->params[ POST ][ MODE ];

		if( isset( $this->params[ POST ][ init::PRESET_ID ] ) ) $this->preset_id = $this->params[ POST ][ init::PRESET_ID ];

	}

	public function set_params(){

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
			$ret = $this->get_session_data();
			$this->params[ 'lec_style_preset_id' ]   = $this->preset_id;
			$this->params[ 'lec_style_preset_name' ] = $ret[ 'lec_style_preset_name' ];
			$this->params[ 'remarks' ]               = $ret[ 'remarks' ];
			$this->params[ 'is_default' ]            = $ret[ 'is_default' ];
			$this->params[ init::ROOMS ]             = $ret[ init::ROOMS ];

		} else {

			// 追加モードの場合
			// 教室リスト
			$this->params[ init::ROOMS ] = $this->get_lec_preset_rooms_data();

		}

		if( $this->mode == 'copy' || $this->mode == 'insert' ){

			// 仮プリセットID
			if( isset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] ) ){
				// 次の仮プリセットID
				$id = 0;
				foreach( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] as $key => $value ){
					if( $value[ 'lec_style_preset_id'] > $id ) $id = $value[ 'lec_style_preset_id' ];
				}
				$this->params[ 'lec_style_preset_id' ] = $id + 1;
			} else {
				$this->params[ 'lec_style_preset_id' ] = 1;
			}
		}

		$this->params[ 'count' ] = count( $this->params[ init::ROOMS ] );

		return $this->params;

	}

	// 教室リスト取得
	private function get_lec_preset_rooms_data(){

		$ref_pdo = new Ref_Pdo_LecStylePreset();

		foreach( $this->rooms as $value ){
			$ref[] = $ref_pdo->ref_room_id_name( array( $value ) );
		}

		return $ref;
	}

	// 講義形態プリセット設定データ取得
	private function get_session_data(){

		$ref_pdo = new Ref_Pdo_LecStylePreset();

		foreach( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] as $value ){

			if( $value[ 'lec_style_preset_id' ] === $this->preset_id ){

				// SESSION変数には教室名がないので補う
				foreach( $value[ init::ROOMS ] as $key => $val ){

					$room_data = $ref_pdo->ref_room_id_name( array( $val[ 'room_id' ] ) );
					$value[ init::ROOMS ][ $key ][ 'room_name' ] = $room_data[ 'room_name' ];

				}

				$ret[ 'lec_style_preset_name' ] = $value[ 'lec_style_preset_name' ];
				$ret[ 'remarks' ]               = $value[ 'remarks' ];
				$ret[ 'is_default' ]            = $value[ 'is_default' ];
				$ret[ init::ROOMS ]             = $value[ init::ROOMS ];
				break;
			}
		}

		return $ret;
	}
}
?>
