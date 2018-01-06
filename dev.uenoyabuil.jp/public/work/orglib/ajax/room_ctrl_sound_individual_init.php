<?php

class init{
	
	private $params;
	private $room_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) ){
			$this->room_id = $this->params[ POST ][ ROOM_ID ];
		}

	}

	public function set_params(){
		
		$arr_machine_name = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
		
		$this->params[ 'sounds_device' ] = $this->get_sound_device_list();
		
		foreach( $this->params[ 'sounds_device' ] as &$value ){
			$value[ 'machine_name' ] = $arr_machine_name[ $value[ 'comment' ] ];
		}

		return $this->params;

	}
	
	private function get_sound_device_list(){
		
		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return $ref_pdo->ref_sound_device( array( $this->room_id ) );
	}
}
?>
