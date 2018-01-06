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

		// 拡声音用
		$this->params[ 'master_device_id' ]  = $this->get_sound_id( 'device', 'master' );
		$this->params[ 'master_group_id' ]   = $this->get_sound_id( 'group',  'master' );

		// 送信音用
		$this->params[ 'send_7_device_id' ]  = $this->get_sound_id( 'device', 'send', '7' );
		$this->params[ 'send_7_group_id' ]   = $this->get_sound_id( 'group',  'send', '7' );
		$this->params[ 'send_9_device_id' ]  = $this->get_sound_id( 'device', 'send', '9' );
		$this->params[ 'send_9_group_id' ]   = $this->get_sound_id( 'group',  'send', '9' );

		// 受信音用
		$this->params[ 'receive_7_device_id' ]  = $this->get_sound_id( 'device', 'receive', '7' );
		$this->params[ 'receive_7_group_id' ]   = $this->get_sound_id( 'group',  'receive', '7' );
		$this->params[ 'receive_9_device_id' ]  = $this->get_sound_id( 'device', 'receive', '9' );
		$this->params[ 'receive_9_group_id' ]   = $this->get_sound_id( 'group',  'receive', '9' );

		return $this->params;

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
}
?>
