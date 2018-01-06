<?php

class init{

	const LETURERER_CAMERA = 1;
	const STUDENT_CAMERA   = 2;

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
		//$this->params[ 'm_name' ] = $this->params[ 'machine_name' ][ $this->code ];
		$this->params[ 'm_name' ]  = $arr_machine_name[ $this->code ];

		//コーデックの教室機器ID
		$this->params[ 'room_machine_id' ]  = $this->get_room_machine_id();

		//カメラNo
		switch( $this->code ) {
			case 'LecturerCamera' :
				$this->params[ 'camera_no' ]  = self::LETURERER_CAMERA;
				break;
			case 'StudentCamera' :
				$this->params[ 'camera_no' ]  = self::STUDENT_CAMERA;
				break;
			default : 
				$this->params[ 'camera_no' ]  = '1';
				break;
		}

		return $this->params;

	}

	// コーデックの教室機器ID取得
	private function get_room_machine_id(){

		$ref_pdo = new Ref_Pdo_CtrlRoom2();
		return  $ref_pdo->ref_codec_1_id( array( $this->room_id ) );
	}

}
?>
