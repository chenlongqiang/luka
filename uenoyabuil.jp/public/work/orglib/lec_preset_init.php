<?php

class init{

	const LEC_NAME    = 'lec_name';
	const ROOMS       = 'rooms';
	const NEWLEC      = 'newlec';
	const INSERT      = 'insert';
	const COPY        = 'copy';
	const UPDATE      = 'update';
	const DELETE      = 'delete';
	const NORMAL      = 'normal';
	const LEC_PRESETS = 'lec_presets';
	const EDIT_MODE   = 'edit_mode';
	const RESERVATION = 'reservation';
	const RET_CAPTION = 'ret_caption';
	private $params;
	private $room_id;
	private $lec_id;

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
		if( isset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] ) ){
			$this->params[ HIDDEN ][ init::EDIT_MODE ] = init::NORMAL;
		} else {
			$this->params[ HIDDEN ][ init::EDIT_MODE ] = init::INSERT;
		}

		if( isset( $this->params[ POST ][ MODE ] ) ){

			$this->params[ MODE ] = $this->params[ POST ][ MODE ];
			
			// 講義予約新規からの場合
			if( $this->params[ MODE ] === init::NEWLEC ){

            	// セッションに格納
				$_SESSION[ init::NEWLEC ][ init::RESERVATION ] = $this->params[ POST ];

				// 講義名
				$this->params[ init::LEC_NAME ] = $this->params[ POST ][ init::LEC_NAME ];
				// 教室リスト
				$this->params[ init::ROOMS ]    = $this->params[ POST ][ init::ROOMS ];
				// JS で利用するため HIDDEN に追加
				$this->params[ HIDDEN ][ MODE ]    = $this->params[ MODE ];
				$this->params[ HIDDEN ][ init::ROOMS ] = $this->params[ init::ROOMS ];

				// 戻るボタンのキャプション
				$this->params[ init::RET_CAPTION ] = '講義予約に戻る';

			} else {

				// 別のモード時
				if( isset( $this->params[ POST ][ LEC_ID ] ) ){
			
					$this->params[ LEC_ID ] = $this->params[ POST ][ LEC_ID ];
					$this->lec_id = $this->params[ LEC_ID ];
					if( isset( $_SESSION[ init::UPDATE ][ init::RESERVATION ] ) ){
						$this->params[ init::LEC_NAME ] = $_SESSION[ init::UPDATE ][ init::RESERVATION ][ init::LEC_NAME ];
					} else {
						$this->params[ init::LEC_NAME ] = $this->get_lec_name();
					}
				}

				// 講義リスト取得
				$this->params[ 'lectures' ]  = $this->get_lectures_data();

				// JS で利用するため HIDDEN に追加
				$this->params[ HIDDEN ][ MODE ] = $this->params[ MODE ];
				$this->params[ HIDDEN ][ init::EDIT_MODE ] = init::NORMAL;

				// 戻るボタンのキャプション
				$this->params[ init::RET_CAPTION ] = '戻る';
			
			}

		} else {
			
			// 戻るボタンのキャプション
			$this->params[ init::RET_CAPTION ] = '戻る';
		
		}

		return $this->params;

	}

	// 講義リスト取得関数
	private function get_lectures_data(){
		
		$ref_pdo = new Ref_Pdo_LecStylePreset();
		return $ref_pdo->ref_lecture_list();
	}
	
	// 講義名取得
	private function get_lec_name(){
	
		$ref_pdo = new Ref_Pdo_LecStylePreset();
		return $ref_pdo->ref_lec_name( array( $this->lec_id ) );
	}
}
?>
