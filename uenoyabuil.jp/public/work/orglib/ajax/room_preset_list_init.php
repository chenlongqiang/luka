<?php

class init{
	
	private $params;
	private $room_id;
	private $room_type_id;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ] ) ){
			$this->room_id = $this->params[ POST ][ ROOM_ID ];
		}

	}

	public function set_params(){
		
		// ROOM_TYPE_ID取得
		$this->room_type_id = $this->get_room_type_id();

		// 教室設定プリセットリスト取得
		$this->params[ 'room_presets' ] = $this->get_room_presets_data();

		return $this->params;

	}

	// 教室設定プリセットリスト取得関数
	private function get_room_presets_data(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_room_preset( array( $this->room_type_id ) );

	}

	// ROOM_TYPE_ID取得関数
	private function get_room_type_id(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_room_type_id( array( $this->room_id ) );

	}
}
?>
