<?php

require_once( 'Env.php' );

class Page extends Html{

	private $filename;
	private $params;

	public function __construct( $filename, $params = null ){
		
		parent::__construct( $filename, $params );

		if( !parent::get_code() ) return parent::get_error();

		// IPアドレスチェック
		$ip_chk = Common::chk_ip_restriction( $_SERVER[ 'REMOTE_ADDR'] );
		if( !$ip_chk[ CODE ] ){

			$obj_smarty = new ExSmarty(); 
			$url = ( basename( $_SERVER[ 'PHP_SELF' ] ) === 'index.php' ) ? str_replace( 'index.php', '', $_SERVER[ 'PHP_SELF' ] ) : $_SERVER['PHP_SELF'] ;
			$obj_smarty->assign( 'url', $url );
			$obj_smarty->display( '404.tpl' );
			exit;
		}

		$this->filename = $filename;
		$this->params   = parent::get_params();

		// YAML読み込み
        $config = Spyc::YAMLLoad( CONFIG_DIR . '/config.yaml' );

        foreach( $config[ strtoupper( $this->filename ) ] as $key => $value ){
            $this->params[ strtolower( $key ) ] = $value;
        }
		
		// JS読み込み設定
		// SESSION または POST に ROOM_ID があればそこから room_mode を取得（両方ある場合は POST）
		if( isset( $_SESSION[ ROOM_ID ] )             ) $room_mode = Common::get_room_mode( $_SESSION[ ROOM_ID ] );
		if( isset( $this->params[ POST ][ ROOM_ID ] ) ) $room_mode = Common::get_room_mode( $this->params[ POST ][ ROOM_ID ] );
		$env_file = ( isset( $room_mode ) && preg_match( '/^clocktower|^tokyo_office/', $room_mode[ ROOM_MODE ] ) ) ? 'env_clocktower' : 'env';

		$js_files = array( 'jquery', $env_file, 'ch_page', 'ajax_func', $this->filename, 'validation', 'ajax_ctrl_command' );
		$flags    = str_split( $this->params[ JS_LOAD ] );
		$i        = 0;
		foreach( $js_files as $value ){
			$this->params[ 'flg_js_load' ][ $value ] = (bool) $flags[ $i++ ];
		}

		$this->params[ TITLE_TEXT ]  = $this->params[ PRE_TITLE_TEXT ] . $this->params[ TITLE_TEXT ][ $filename ];
		$this->params[ HEADER_TEXT ] = $this->params[ HEADER_TEXT ][ $filename ];

		// 履歴を記録
		$this->set_history();

	}

	// HTML出力実行
	public function create_html(){

		$this->params = $this->init( $this->params );

		$this->display();

	}
	
	// フックメソッドのオーバーライド
	protected function get_init_path(){
		return LIB_DIR . '/' . $this->filename . '_init.php';
	}
	// フックメソッドのオーバーライド（中身は空）
	protected function set_smarty_dir( &$smarty_obj ){ }

	// +--------------------------------------------------------------
	// | 履歴の記録
	// +--------------------------------------------------------------
	private function set_history(){

		if( isset( $_SESSION[ HISTORY ] ) ){

			// 2ページ目以降の場合
			$history = explode( '|', $_SESSION[ HISTORY ] );
			
			// 次ページが履歴に含まれない場合、自分を追加
			if( array_search( $this->filename, $history ) === false ){

				array_push( $history, $this->filename );
			
			}

			// 自ページが既に履歴に含まれていて、かつ最終履歴でない場合、最終履歴を消す
			if( array_search( $this->filename, $history ) !== false && $history[ count( $history ) - 1 ] !== $this->filename){

				unset( $history[ count( $history ) - 1 ] );

			}

		} else {

			// 初めて表示するページの場合
			$history = array( $this->filename );

		}

		// トップページでは HISTORY をリセット
		if( $this->filename === 'index' ) $history = array( 'index' );

		$_SESSION[ HISTORY ] = implode( '|', $history );

		// 最終履歴の一つ前を frompage に設定
		if( count( $history ) > 1 ){

			$_SESSION[ FROMPAGE ] = $history[ count( $history ) - 2 ];

		} else {

			// 履歴が2個以上ない場合（Bookmarkなどで表示した場合）
			$_SESSION[ FROMPAGE ] = 'index';

		}

		// frompage は全ページの JS で使うので hidden に追加
		$this->params[ HIDDEN ][ FROMPAGE ] = $_SESSION[ FROMPAGE ];
	}
	
}

?>
