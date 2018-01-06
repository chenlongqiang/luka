<?php

class init implements IF_Init{
	
	private $params;

	public function __construct( $params ){

		$this->params = $params;

	}

	public function set_params(){

		switch( $this->params[ POST ][ MODE ] ){
			case 'login':

				$chk_ret = Common::chk_auth();

				if( !$chk_ret[ CODE ] ){

					$ret = $this->set_session( 'auth' );

					// 認証失敗の場合エラーメッセージを表示
					if( !$ret[ CODE ] ){
						// 不要
						$this->params[ ERROR ] = $this->params[ ERROR_MSG ][ $ret[ ERROR ] ];
					} else {
						// アクセスログ
						Common::access_log( 'login', 3 );
					}
				}
				break;

			case 'logout':

				// アクセスログ
				Common::access_log( 'logout', 3 );
				
				$this->destroy_session();
				break;

			case 'check':
				
				$ret = $this->check();

				// チェック結果を返す
				$code  = $ret[ CODE ] ? 'OK' : 'NG';
				$level = $ret[ LEVEL ];
				$error = $ret[ ERROR ] ? $this->params[ ERROR_MSG ][ $ret[ ERROR ] ] : null;
				
				$this->params[ STATUS ] = json_encode( array( 'code' => $code, 'level' => $level, 'error' => $error ) );
				break;
		}
		
		// モードにより切り分け
		$this->params[ MODE ] = $this->params[ POST ][ MODE ];

		// セッションの状態をチェックし smarty に渡す
		$this->params[ SESSION_STATUS ] = Common::chk_auth();

		return $this->params;

	}

	// 認証OKの場合、セッション変数セット
	// 戻り値（HUSH値）
	// +-------+---------------------------------
	// | code  | true  : 認証OK
	// |       | false : 認証NG
	// +-------+---------------------------------
	// | error | array : エラーコード
	// +-----------------------------------------
	private function set_session( $mode = 'check' ){

		$account = Spyc::YAMLLoad( CONFIG_DIR . '/account.yaml' );

		$user_id = $this->params[ POST ][ USER_ID ];
		$passwd  = $this->params[ POST ][ PASSWD ];
					
		// user_id が登録されているかチェック
		if( array_key_exists( $user_id, $account ) ){

			// パスワードチェック
			if( $passwd === $account[ $user_id ][ PASSWD ] ){
				
				if( $mode = 'auth' ){
					$_SESSION[ 'sid' ]     = session_id();
					$_SESSION[ 'user_id' ] = $user_id;
					$_SESSION[ 'level' ]   = $account[ $user_id ][ LEVEL ];
				}
	
				$code = true;				
				$err  = null;

			} else {
				// パスワード不正
				$code = false;
				$err  = 'ERR_PASSWD';

			}

		} else {
			// ユーザID不正	
			$code = false;
			$err  = 'ERR_USER_ID';

		}

		return array( CODE => $code, ERROR => $err );
	}

	// セッション破棄（ログアウトした場合）
	private function destroy_session(){
		
		session_unset();
		session_destroy();

	}
	
	// ID、パスワードチェック
	private function check(){

		$account = Spyc::YAMLLoad( CONFIG_DIR . '/account.yaml' );

		$user_id = $this->params[ POST ][ USER_ID ];
		$passwd  = $this->params[ POST ][ PASSWD ];
					
		// user_id が登録されているかチェック
		if( array_key_exists( $user_id, $account ) ){

			// パスワードチェック
			if( $passwd === $account[ $user_id ][ PASSWD ] ){
					
				$code  = true;
				$level = $account[ $user_id ][ LEVEL ];
				$err   = null;

			} else {
				// パスワード不正
				$code  = false;
				$level = null;
				$err   = 'ERR_PASSWD';

			}

		} else {
			// ユーザID不正	
			$code  = false;
			$level = null;
			$err   = 'ERR_USER_ID';

		}

		return array( CODE => $code, LEVEL => $level, ERROR => $err );
	}
}
?>
