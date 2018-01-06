<?php

class init{
	
	const CHECKNAME = 'checkname';
	const STATUS    = 'status';
	private $params;
	private $checkname;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ init::CHECKNAME ] ) ){

			$this->checkname = $this->params[ POST ][ init::CHECKNAME ];
			unset( $this->params[ POST ][ init::CHECKNAME ] );

		}
	}

	public function set_params(){

		// バリデーションチェック実行
		$vali = new Validation( $this->checkname, $this->params[ POST ] );
		$ret = $vali->check();

		// 結果をアサイン
		$this->params[ init::STATUS ] = $ret[ CODE ];
		if( !$ret[ CODE ] ){

			foreach( $ret[ ERROR_MSG ] as $key => $value ){
				$arr_err[] = $key . '=' . $value;
			}

			$errstr = implode( '|', $arr_err );

			$this->params[ ERROR ] = $errstr;
		}
		return $this->params;

	}
}
?>
