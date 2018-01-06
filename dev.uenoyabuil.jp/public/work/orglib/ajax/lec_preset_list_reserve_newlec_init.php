<?php

class init{
	
	const NEWLEC      = 'newlec';
	const LEC_PRESETS = 'lec_presets';
	const IDX         = 'idx';
	private $params;
	private $room_id;

	public function __construct( $params ){

		$this->params = $params;

	}

	public function set_params(){
		
		// SESSION変数からプリセットリストを表示
		if( isset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] ) ){
			$this->params[ init::LEC_PRESETS ] = $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ];
		}

		return $this->params;

	}

}
?>
