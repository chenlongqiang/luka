<?php
class ExPdo extends PDO{

	private $status;

	public function __construct(){

		try {

			$conf = Spyc::YAMLLoad( ROOT_DIR . 'config/env.yaml' );
			parent::__construct( "mysql:dbname={$conf[ 'dbname' ]};host={$conf[ 'host' ]}", $conf[ 'user' ], $conf[ 'passwd' ] );

			$this->status[ CODE ] = true;
			$this->status[ MSG ]  = 'PDO_INIT';

		} catch( PDOException $e) {

			$this->status[ CODE ] = 'PDO_ERROR';
			$this->status[ MSG ]  = $e->getMessage();

		}

	}

	// ステータス返却
	// +-----+-----------------------------------
	// | out | ERROR     : エラーコード
	// |     | ERROR_MSG : エラーメッセージ
	// +-----+-----------------------------------
	public function get_status(){ return $this->status; }

	// 指定したSQLファイルのSQLを実行するメソッド
	// +-----+-----------------------------------
	// | in  | $sql_path   : SQLファイルのパス
	// |     | $parameters : プリペアドステートメントに渡すバインドパラメータ
	// | out | CODE  : 1 - 正常、0 - エラー -1 - レコード0件
	// |     | DATA  : [ 正常時 ] 結果リスト配列
	// |     | ERROR : [ 異常時 ] errorInfo
	// +-----+-----------------------------------
	protected function _exec( $sql_path, $parameters = null, $mode = 'ref' ){

		$sql = file_get_contents( $sql_path );

		return $this->_exec_sql( $sql, $parameters, $mode);

	}
	// _exec のコア
	protected function _exec_sql( $sql, $parameters = null, $mode = 'ref' ){

		try {

			$obj_st = $this->prepare( $sql );
			$status = $obj_st->execute($parameters);

			if( $status ){
				if( $mode == 'ref' ){
					
					$ret = $obj_st->fetchAll( PDO::FETCH_ASSOC );

					if( count( $ret ) ){
						return array( CODE => 1,  DATA => $ret );
					} else {
						return array( CODE => -1, DATA => 'NO_RECORD');
					}

				} else if( $mode == 'upd' ){

					$ret = $obj_st->rowCount() . ' rows affected.';

					return array( CODE => 1,  DATA => $ret );
				}
			} else {
				return array( CODE => 0, ERROR => $obj_st->errorInfo() );
			}

		} catch( PDOException $e ) {

			return array( CODE  => 0, ERROR => $e->getCode . ' : ' . $e->getMessage() );

		}
	}
	
	// LIMIT対応版
	protected function _exec_sql_limit( $sql, $parameters = null, $mode = 'ref' ){

		try {

			$obj_st = $this->prepare( $sql );
			
			if ($parameters) {
				// LIMITの挙動がおかしいので、bindValueを使用する
				foreach($parameters as $index => $param) {
					if (is_string( $param )) {
						$obj_st->bindValue($index + 1, $param, PDO::PARAM_STR);
					} else if (is_int( $param )) {
						$obj_st->bindValue($index + 1, $param, PDO::PARAM_INT);
					} else if (is_bool( $param )) {
						$obj_st->bindValue($index + 1, $param, PDO::PARAM_BOOL);
					} else if (is_null( $param )) {
						$obj_st->bindValue($index + 1, $param, PDO::PARAM_NULL);
					} else {
						$obj_st->bindValue($index + 1, $param, PDO::PARAM_STR);
					}
				}
				$status = $obj_st->execute();
			}

			if( $status ){
				if( $mode == 'ref' ){
					
					$ret = $obj_st->fetchAll( PDO::FETCH_ASSOC );

					if( count( $ret ) ){
						return array( CODE => 1,  DATA => $ret );
					} else {
						return array( CODE => -1, DATA => 'NO_RECORD');
					}

				} else if( $mode == 'upd' ){

					$ret = $obj_st->rowCount() . ' rows affected.';

					return array( CODE => 1,  DATA => $ret );
				}
			} else {
				return array( CODE => 0, ERROR => $obj_st->errorInfo() );
			}

		} catch( PDOException $e ) {

			return array( CODE  => 0, ERROR => $e->getCode . ' : ' . $e->getMessage() );

		}
	}
}
?>
