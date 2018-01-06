<?php

class init implements IF_Init{

	const STATUS    = 'status';
	private $params;
	private $mode;
	private $data;

	public function __construct( $params ){

		$this->params = $params;

	}

	public function set_params(){

		$this->data = $this->params[ POST ];

		$ret = $this->excute( $this->data );

		// 結果をアサイン
		$ret_str = $ret[ CODE ] ? 'ソート順を変更しました' : $ret[ DATA ]; 

		$this->params[ init::STATUS ] = $ret_str;

		// アクセスログ
		Common::access_log( 'change_sort_order', 2, $this->data );

		return $this->params;

	}

	private function excute( $data ){
		
		$upd_pdo = new Upd_Pdo_LecStylePreset();
		
		$ret_flg = true;	
		$ret_str = '';
		foreach( $data as $key => $value ){
			
			$ret = $upd_pdo->update_sort_key( array( 'lec_style_preset_id' => $key, 'sort_key' => $value ) );

			if( !$ret[ CODE ] ){
				$ret_flg  = false;
				$ret_str .= $ret[ ERROR ] . "\n";
			}
		}

		return array( CODE => $ret_flg, DATA =>$ret_str );
	}
}
?>
