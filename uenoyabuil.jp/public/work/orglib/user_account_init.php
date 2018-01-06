<?php

class init implements IF_Init{
	
	private $params;

	public function __construct( $params ){

		$this->params = $params;
		
		// アクセスレベルチェック
		$ret = Common::chk_auth();

		if( !$ret[ CODE ] || $ret[ LEVEL ] !== 'STAFF' ){

			// アクセス権限がない場合エラー画面に遷移する。
			Common::goto_error( '000010' );
		}

	}

	public function set_params(){

		if( count( $this->params[ POST ] ) ){
		
			$user   = array();
			$passwd = array();
			$level  = array();
			foreach( $this->params[ POST ] as $key => $value ){
		
				preg_match( '/user|passwd|level/', $key, $matches );
				switch( $matches[ 0 ] ){
					case 'user'   : $user[]   = $value; break;
					case 'passwd' : $passwd[] = $value; break;
					case 'level'  : $level[]  = $value; break;
				}
			}
			
			$account_data = array();
			for( $i = 0 ; $i < count( $user ) ; $i++ ){
				$account_data[ $user[ $i ] ] = array( 'passwd' => $passwd[ $i ], 'level' => $level[ $i ] );
			}
		
			if( count( $account_data ) == 0 ) $account_data[ 0 ] = 'dummy';
			
			$ret = Spyc::YAMLDump( $account_data );

			$filename = CONFIG_DIR . '/account.yaml';
			$fp = fopen( $filename, 'w' );
			fwrite( $fp, $ret );
			fclose( $fp );

		}
		
		$conf = Spyc::YAMLLoad( CONFIG_DIR . '/account.yaml' );
		$this->params[ 'account' ] = $conf;

		return $this->params;

	}

}
?>
