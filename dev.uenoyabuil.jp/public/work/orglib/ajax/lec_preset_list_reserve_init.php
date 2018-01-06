<?php

class init{
	
	const LEC_PRESETS = 'lec_presets';
	private $params;
	private $reserve_id;

	public function __construct( $params ){

		$this->params = $params;
		
		if( isset( $this->params[ POST ][ RESERVE_ID ] ) ) $this->reserve_id = $this->params[ POST ][ RESERVE_ID ];

	}

	public function set_params(){

		// 講義形態プリセットリストを表示
		$this->params[ init::LEC_PRESETS ] = $this->get_lec_preset_list();

		return $this->params;

	}
	
	private function get_lec_preset_list(){
		
		$ref_pdo = new Ref_Pdo_Reserve();
		return $ref_pdo->ref_lec_preset_list( array( $this->reserve_id ) );
	}
}
?>
