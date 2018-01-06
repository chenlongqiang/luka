<?php

class init{

	const IDX         = 'idx';
	const NEWLEC      = 'newlec';
	const LEC_PRESETS = 'lec_presets';
	private $params;
	private $mode;

	public function __construct( $params ){

		$this->params = $params;
		if( isset( $this->params[ POST ][ MODE ] ) ) $this->mode = $this->params[ POST ][ MODE ];

	}

	public function set_params(){

		$this->set_session();
		$this->params[ 'status' ] = 'プリセットを登録しました';

		return $this->params;

	}

	private function set_session(){

		$room_count = $this->params[ POST ][ 'room_count' ];
		$hush       = array( 'room_id', 'room_preset_id', 'is_lecturer_9', 'is_lecturer_7' );

		switch( $this->mode ){
			case 'insert':
				$idx = $this->params[ POST ][ 'lec_style_preset_id' ];
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'lec_style_preset_id'   ] = $idx;
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'lec_style_preset_name' ] = $this->params[ POST ][ 'lec_style_preset_name' ];
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'remarks' ]               = $this->params[ POST ][ 'remarks' ];
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'sort_key' ]              = $idx;
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'is_default' ]            = $this->params[ POST ][ 'is_default' ];

				for( $i = 0; $i < $room_count; $i++ ){

					$data = explode( '|', $this->params[ POST ][ 'data_' . $i ] );

					$j = 0;	//カウンタ
					foreach( $hush as $value ){
						$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'rooms' ][ $i ][ $value ] = $data[ $j++ ];
					}

				}

				break;

			case 'update':
				$idx = '';
				foreach( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] as $key => $val ){
					if( $val[ 'lec_style_preset_id' ] == $this->params[ POST ][ 'lec_style_preset_id' ] ) $idx = $key;
				}

				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'lec_style_preset_name' ] = $this->params[ POST ][ 'lec_style_preset_name' ];
				$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'remarks' ]               = $this->params[ POST ][ 'remarks' ];

				for( $i = 0; $i < $room_count; $i++ ){

					$data = explode( '|', $this->params[ POST ][ 'data_' . $i ] );

					$j = 0;	//カウンタ
					foreach( $hush as $value ){
						$_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $idx ][ 'rooms' ][ $i ][ $value ] = $data[ $j++ ];
					}
				}
				break;
		}
	}
}
?>
