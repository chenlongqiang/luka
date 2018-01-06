<?php

class init implements IF_Init{

	const AUTH  = 'auth';
	const LEVEL = 'level';
	private $params;
	
	public function __construct( $params ){
		
		$this->params = $params;
		
	}

	public function set_params(){
	
		// セッションチェック
		$ret = Common::chk_auth();
		$this->params[ HIDDEN ][ init::AUTH  ] = $ret[ CODE ];
		$this->params[ HIDDEN ][ init::LEVEL ] = $ret[ LEVEL ];

		// room_ip.yamlに登録されている場合　教室IDをセット
		$this->params[ HIDDEN ][ ROOM_ID ] = Common::get_my_room_id( $_SERVER[ 'REMOTE_ADDR' ] );

		// CTRL_MODE を HIDDEN にセット
		$this->params[ HIDDEN ][ CTRL_MODE ] = $this->params[ CTRL_MODE ];

		return $this->params;
	}
	
}

?>
