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
		
			$allow   = array();
			$remarks = array();
			$deny    = array();
			foreach( $this->params[ POST ] as $key => $value ){
		
				preg_match( '/allow|remarks|deny/', $key, $matches );
				switch( $matches[ 0 ] ){
					case 'allow'   : $allow[]   = $value; break;
					case 'remarks' : $remarks[] = $value; break;
					case 'deny'    : $deny[]    = $value; break;
				}
			}
			
			$allow_data = array();
			for( $i = 0 ; $i < count( $allow ) ; $i++ ){
				$allow_data[] = array( 'ip' => $allow[ $i ], 'remarks' => $remarks[ $i ] );
			}
		
			if( count( $allow_data ) == 0 ) $allow_data[ 0 ] = 'dummy';
			if( count( $deny )       == 0 ) $deny[ 0 ]       = 'dummy';

			$yaml_data = array( 'allow' => $allow_data, 'deny' => $deny );
			
			$ret = Spyc::YAMLDump( $yaml_data );

			$filename = CONFIG_DIR . '/ip_restriction.yaml';
			$fp = fopen( $filename, 'w' );
			fwrite( $fp, $ret );
			fclose( $fp );

		}
		
		$conf  = Spyc::YAMLLoad( CONFIG_DIR . '/ip_restriction.yaml' );
		$this->params[ 'allow' ] = $conf[ 'allow' ];
		$this->params[ 'deny'  ] = $conf[ 'deny'  ];

		return $this->params;

	}

}
?>
