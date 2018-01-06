<?php

class init implements IF_Init{
 
	const RESERVATION = 'reservation';
	private $params;
	private $mode;

	public function __construct( $params ){

		$this->params = $params;
		if( isset( $this->params[ POST ][ MODE ] ) ) $this->mode = $this->params[ POST ][ MODE ];

	}

	public function set_params(){

		$this->set_session();

		return $this->params;

	}

	// POSTされたデータを SESSION に格納
	private function set_session(){

		// MODE は不要
		unset( $this->params[ POST ][ MODE ] );

		$_SESSION[ $this->mode ][ init::RESERVATION ] = $this->params[ POST ];

	}
}
?>
