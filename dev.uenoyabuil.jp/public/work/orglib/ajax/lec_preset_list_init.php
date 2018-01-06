<?php

class init{
	
	const NEWLEC      = 'newlec';
	const LEC_PRESETS = 'lec_presets';
	private $params;
	private $lec_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ LEC_ID ] ) ){
			$this->lec_id = $this->params[ POST ][ LEC_ID ];
		}

	}

	public function set_params(){
		
		// 講義形態プリセットリスト取得
		$this->params[ 'lec_presets' ] = $this->get_lec_presets_data();

		return $this->params;

	}

	// 講義形態プリセットリスト取得関数
	private function get_lec_presets_data(){
		
		$ref_pdo = new Ref_Pdo_LecStylePreset();
		return $ref_pdo->ref_lec_preset_list( array( $this->lec_id ) );
	}
}
?>
