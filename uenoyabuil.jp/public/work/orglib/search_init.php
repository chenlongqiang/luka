<?php

class init implements IF_Init{
	
	private $params;

	public function __construct( $params ){

		$this->params = $params;
		
	}

	public function set_params(){
		
		// POSTされた値を格納（ keyword と inc_past ）
		foreach( $this->params[ POST ] as $key=>$value ){

			$this->params[ $key ] = $value;
		}

		// JS で利用するので、各時限の開始時刻、終了時刻を hidden に追加
		if( isset( $this->params[ PERIOD_START_TIME ] ) ){
			$this->params[ HIDDEN ][ PERIOD_START_TIME ] = implode( '|', $this->params[ PERIOD_START_TIME ] );
			$this->params[ HIDDEN ][ PERIOD_END_TIME ]   = implode( '|', $this->params[ PERIOD_END_TIME ] );
		}

		return $this->params;

	}

}
?>
