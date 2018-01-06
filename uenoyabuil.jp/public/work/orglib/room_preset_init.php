<?php

class init{
	
	const EDIT_MODE = 'edit_mode';
	const NORMAL    = 'normal';
	private $params;
	private $room_id;

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

		// 編集モード格納用 HIDDEN
		$this->params[ HIDDEN ][ init::EDIT_MODE ] = init::NORMAL;
		
		// 教室リスト取得
		$this->params[ 'rooms' ]  = $this->get_room_data();

		return $this->params;

	}

	// 教室リスト取得関数
	private function get_room_data(){
		
		$ref_pdo = new Ref_Pdo_RoomPreset();
		return $ref_pdo->ref_all_rooms();
		
	}

}
?>
