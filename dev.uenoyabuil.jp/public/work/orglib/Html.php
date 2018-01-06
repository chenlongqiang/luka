<?php

abstract class Html{
	
	private $filename;
	private $params;
	private $code;
	private $error;

	public function __construct( $filename, $params = null ){

		if( !isset( $filename ) ){
			$this->code = false;
			$this->error = 'FILENAME_ERROR';
			return;
		}

		$this->filename = $filename;
		$this->params   = $params;

		// 共通データ登録
		$this->params[ FILENAME ] = $this->filename;
		if( isset( $_GET ) )  $this->params[ GET ]  = $_GET;
		if( isset( $_POST ) ) $this->params[ POST ] = $_POST;
		
		// YAML読み込み
		$disp = Spyc::YAMLLoad( CONFIG_DIR . '/disp.yaml' );
		foreach( $disp as $key => $value ){
            $this->params[ strtolower( $key ) ] = $value;
		}
		
		// セッション開始
		Common::_session_start();
		// セッションチェック
		$this->params[ SESSION_STATUS ] = Common::chk_auth();

		// 概算、時計台・東京オフィス分岐用
		$this->params[ CTRL_MODE ] = $this->get_ctrl_mode();

		$this->code = true;

	}

	// $code、$params、$error 取得用関数
	public function get_code(){   return $this->code; }
	public function get_params(){ return $this->params; }
	public function get_error(){  return $this->error; }

	// HTML出力実行
	abstract public function create_html();

	// +--------------------------------------------------------------
	// | 各ページ固有の処理の実行
	// |  ページ名_init.php を読み込み、set_paramsメソッドを実行する
	// |  initメソッドには $params にデータをセットする処理を記述する
	// +--------------------------------------------------------------
	protected function init( $params = null ){

		if( isset( $params ) ) $this->params = $params;

		$path = $this->get_init_path();

		if( file_exists( $path ) ){

			require_once( $path );
			$init = new init( $this->params );
			$this->params = $init->set_params();

		} else {
			// init ファイルが存在しない場合
			if( $this->filename !== 'index' ) var_dump( $path . ' is not exist.');

		}

		return $this->params;
	}
	
	// init.php 設定用フックメソッド
	abstract protected function get_init_path();

	protected function display(){
		
		$this->smarty = new ExSmarty();

		$this->set_smarty_dir( $this->smarty );

		if( isset( $this->params ) ){
			$this->smarty->assign( PARAMS, $this->params );
		}

		$this->smarty->display( $this->filename . '.tpl');
	}
	
	// template_dir、compile_dir 設定用フックメソッド（デフォルトでは何もしない）
	abstract protected function set_smarty_dir( &$smarty_obj );

	// +--------------------------------------------------------------
	// | IPアドレスから、時計台・東京オフィス OR 概算を判別
	// +--------------------------------------------------------------
	private function get_ctrl_mode(){

		$ip = $_SERVER[ 'REMOTE_ADDR' ];

		// YAML読み込み
        $arr_clocktower_ip = Spyc::YAMLLoad( CONFIG_DIR . '/clocktower_ip.yaml' );

		if( in_array( $ip, $arr_clocktower_ip[ 'clocktower' ] ) ){

			return 'CLOCKTOWER';

		} else if( in_array( $ip, $arr_clocktower_ip[ 'tokyo_office' ] ) ){
		
			return 'TOKYO_OFFICE';

		} else {

			return 'NORMAL';
		}
	}
}

?>
