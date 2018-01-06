<?php

class init{
	
	private $params;
	private $room_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) ){
			$this->room_id = $this->params[ POST ][ ROOM_ID ];
			$this->code    = $this->params[ POST ][ CODE ];
		}

	}

	public function set_params(){
		
		// 映像機器名 YAML読み込み
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );

		// 機器名
		$this->params[ 'm_name' ] = $arr_machine_name[ $this->code ];

		// 教室機器ID
		$this->params[ 'room_machine_id' ]   = $this->get_room_machine_id();
		// スクリーンの教室機器ID
		$this->params[ 'screen_machine_id' ] = $this->get_screen_machine_id();
		// スクリーンのステータス
		$this->params[ 'screen_status' ]     = $this->get_screen_status();

		return $this->params;

	}

	// 教室機器ID取得関数
	private function get_room_machine_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_room_machine_id( array( $this->room_id, $this->code ) );
	}

	// スクリーンの教室機器ID取得関数
	private function get_screen_machine_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_room_machine_id( array( $this->room_id, $this->code ) );
	}

	// スクリーンのステータス取得関数
	private function get_screen_status(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
	
		$code     = strtolower( str_replace( 'Screen', '', $this->code ) );
		$funcname = 'ref_screen_status_' . $code;
		
		return $ref_pdo->$funcname( array( $this->room_id ) );
	}

}
?>
