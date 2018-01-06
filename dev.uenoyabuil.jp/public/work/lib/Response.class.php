<?php

/**
 *
 */
abstract class Response{
	
	/**
	 * .php ファイル名 = クラス名
	 */
	protected $filename;
	/**
	 * サブシステム名 = ディレクトリ名
	 */
	protected $systemName;
	/**
	 * テンプレートに渡すパラメータ
	 */
	protected $params;
	/**
	 * POST パラメータの格納用
	 */
	protected $post;
	/**
	 * GET パラメータの格納用
	 */
	protected $get;
	/**
	 * 設定
	 */
	protected $conf;

	/**
	 * コンストラクタ
	 */
	public function __construct(){

		$this->filename = lcfirst( get_class( $this ) );

		// 共通データ登録
		if( isset( $_POST ) ) {
			foreach( $_POST as $key => $value ) {
				$key = htmlspecialchars($key);
				$value = htmlspecialchars($value);
			}
			$this->post = $_POST;
		}
		
		if( isset( $_GET  ) ) {
			foreach( $_GET as $key => $value ) {
				$key = htmlspecialchars($key);
				$value = htmlspecialchars($value);
			}
			$this->get  = $_GET;
		}
		// 設定読込
		$this->setEnv();

		// セッション開始
		$this->sessionStart();
	}

	/**
	 * 設定読込
	 */
	private function setEnv(){

		$this->conf = array_merge( Env::loadEnv(), Env::loadEnv( $this->getSystemName() ) );
		$this->conf[ Env::FILENAME ] = $this->filename;
	}

	/**
	 * セッション開始
	 */
	private function sessionStart(){

		session_name( $this->conf[ Env::SESSION_NAME ] );
		session_set_cookie_params( 0, $this->conf[ Env::SESSION_URL ] );
		session_start();
	}

	/**
	 * サブシステム名の取得
	 */
	abstract protected function getSystemName();

	/**
	 * html/json 出力
	 */
	abstract public function response();

	/**
	 * Smarty の設定用メソッド ( デフォルトでは何もしなくてよい )
	 */
	abstract protected function setSmarty( &$smarty );

	/**
	 * パラメータセッタ
	 */
	abstract protected function setParams();

	/**
	 * Smarty テンプレートの実行
	 */
	protected function display( $templateFile = null ){
		
		$this->smarty = new ExSmarty();

		// Smarty 設定
		$this->setSmarty( $this->smarty );

		// パラメータのセット
		$this->setParams();

		if( isset( $this->params ) ) $this->smarty->assign( Env::PARAMS, $this->params	);
		if( isset( $this->conf	 ) ) $this->smarty->assign( Env::CONFIG, $this->conf	);

		if( isset( $templateFile ) ) {

			$this->smarty->display( $templateFile . $this->conf[ Env::TEMPLATE_FILE_EXT ] );
			
		} elseif( isset( $this->conf[ $this->filename ][ Env::TEMPLATE_FILE ] ) ) {

			$this->smarty->display( $this->conf[ $this->filename ][ Env::TEMPLATE_FILE ] );

		} else {

			$this->smarty->display( $this->filename . $this->conf[ Env::TEMPLATE_FILE_EXT ] );
		}
	}
}
?>
