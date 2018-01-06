<?php

class init implements IF_Init{

	const   MESSAGE = 'message';
	const   INFO    = 'info';
	private $params;
	private $error_code;
	
	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ GET ][ CODE ] ) ){
		
			 $this->error_code = $this->params[ GET ][ CODE ];

		} else {
			// デフォルト
			$this->error_code = '000000';

		}
		
		
	}

	public function set_params(){

		// HISTORY をクリア
		unset( $_SESSION[ HISTORY ] );
	
		$error_type = $this->params[ 'error_code' ][ $this->error_code ][ init::MESSAGE ];
		$this->params[ 'error_msg' ]  = $this->params[ 'error_msg' ][ $error_type ];
		$this->params[ 'error_info' ] = $this->params[ 'error_code' ][ $this->error_code ][ init::INFO ];
		$this->params[ 'error_code' ] = $this->error_code;

		return $this->params;
	}

}

?>
