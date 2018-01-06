<?php

class init{

	const STATUS    = 'status';
	private $params;
	private $mode;

	public function __construct( $params ){

		$this->params = $params;

		if( isset( $this->params[ POST ][ MODE ] ) ) $this->mode = $this->params[ POST ][ MODE ];

	}

	public function set_params(){

		$data = $this->params[ POST ];
		// MODE は不要
		unset( $data[ MODE ] );

		// 講義形態プリセット更新実行
		$ret = $this->upd_lec_preset( $data );
		
		$lec_style_preset_id = $ret[ 'last_id' ];

		// 講義形態プリセットSUB更新実行
		for( $i = 0 ; $i < 20 ; $i++ ){

			if( isset( $data[ 'data_' . $i ] ) ){
				$ret_sub = $this->upd_lec_preset_sub( explode( '|', $data[ 'data_' . $i ] ) , $lec_style_preset_id );
			} else {
				break;
			}
		}

		// 結果をアサイン
		if( is_array( $ret ) ){
			$ret_str = implode( "\n", $ret ) . "\n";
		} else {
			$ret_str = $ret . "\n";
		}

		if( is_array( $ret_sub ) ){
			$ret_str .= implode( "\n", $ret_sub ) . "\n";
		} else {
			$ret_str .= $ret_sub . "\n";
		}

		$this->params[ init::STATUS ] = $ret_str;

		// アクセスログ
		$detail = array( 'lec_style_preset_id' => $lec_style_preset_id );
		Common::access_log( "lec_style_preset_{$this->mode}", 3, $detail );

		return $this->params;

	}

	// 更新実行関数（講義形態プリセットテーブル）
	private function upd_lec_preset( $data ){

		$upd_pdo  = new Upd_Pdo_LecStylePreset();

		$funcname = $this->mode . '_lec_style_preset';

        $insert_data[ 'lec_style_preset_name' ] = $data[ 'lec_style_preset_name' ];
        $insert_data[ 'lec_id' ]                = $data[ 'lec_id' ];
        $insert_data[ 'is_default' ]            = $data[ 'is_default' ] == 'true' ? true : false ;
        $insert_data[ 'remarks' ]               = $data[ 'remarks' ];

		if( $this->mode === 'insert' || $this->mode === 'copy' ){
			// 新規追加、複製時　-> ソートキー設定
			$insert_data[ 'sort_key' ] = (int)$this->get_last_sort_key( $data[ 'lec_id' ] ) + 1;
		} else {
			// 更新時　-> lec_style_preset_id, sort_key をPOSTデータからセット
			$insert_data[ 'lec_style_preset_id' ] = $data[ 'lec_style_preset_id' ];
			$insert_data[ 'sort_key'            ] = $data[ 'sort_key' ];
		}

		$ret = $upd_pdo->$funcname( $insert_data );
		
		if( $this->mode === 'insert' || $this->mode === 'copy' ){
			$id = $upd_pdo->lastInsertId();
		} else {
			$id = $data[ 'lec_style_preset_id' ];
		}

		return array( DATA => $ret, 'last_id' => $id );
	}

	// 更新SQL実行(sub用)
	private function upd_lec_preset_sub( $data, $lec_style_preset_id ){

		$upd_pdo  = new Upd_Pdo_LecStylePreset();

		$funcname = $this->mode . '_lec_style_preset_sub';

        $insert_data_sub[ 'lec_style_preset_id' ] = $lec_style_preset_id;
        $insert_data_sub[ 'room_id' ]             = $data[ 0 ];
        $insert_data_sub[ 'room_preset_id' ]      = $data[ 1 ];
        $insert_data_sub[ 'is_lecturer_9' ]       = $data[ 2 ];
        $insert_data_sub[ 'is_lecturer_7' ]       = $data[ 3 ];

		return $upd_pdo->$funcname( $insert_data_sub );
	}

	private function get_last_sort_key( $lec_id ){
		
		$ref_pdo = new Ref_Pdo_LecStylePreset();
		return $ref_pdo->ref_last_sort_key( array( $lec_id ) );
	}
}
?>
