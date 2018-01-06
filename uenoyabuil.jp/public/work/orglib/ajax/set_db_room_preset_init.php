<?php

class init{

	const STATUS    = 'status';
	private $params;
	private $mode;
	private $lastInsertId;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ MODE ] ) ) $this->mode = $this->params[ POST ][ MODE ];

	}

	public function set_params(){

		$data = $this->params[ POST ];
		// MODE は不要
		unset( $data[ MODE ] );

		// デフォルト設定 YAML読み込み
        $insert_data = Spyc::YAMLLoad( CONFIG_DIR . '/default_room_preset.yaml' );

		// チェックボックスの値を調整
		foreach( $data as $key => $value ){
			if( $value == 'on' ){
				$insert_data[ $key ] = 1;
			} else {
				$insert_data[ $key ] = $value;
			}
		}

		// 更新SQL実行
		$ret = $this->excute( $this->mode, $insert_data );

		// 結果をアサイン
		if( is_array( $ret ) ){
			$this->params[ init::STATUS ] = implode( "\n", $ret );
		} else {
			$this->params[ init::STATUS ] = $ret;
		}
		
		// アクセスログ
		$room_preset_id = ( $this->mode === 'insert' || $this->mode === 'copy' ) ? $this->lastInsertId : $insert_data[ 'room_preset_id' ] ;
		$detail = array( 'room_preset_id' => $room_preset_id );
		Common::access_log( "room_preset_{$this->mode}", 3, $detail );

		return $this->params;

	}

	// 更新SQL実行
	private function excute( $mode, $data ){

		$upd_pdo  = new Upd_Pdo_RoomPreset();

		$funcname = $mode . '_room_preset';

		$ret = $upd_pdo->$funcname( $data );
		
		// アクセスログ用にID取得
		if( $mode === 'insert' || $mode === 'copy' ) $this->lastInsertId = $upd_pdo->lastInsertId();
		
		return $ret;
	}

	private function excute1( $mode, $data ){

		$upd_pdo  = new Upd_Pdo_RoomPreset();

		return $upd_pdo->upd_test( $data );
	}
}
?>
