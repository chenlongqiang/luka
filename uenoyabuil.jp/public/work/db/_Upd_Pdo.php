<?php

class Upd_Pdo extends ExPdo{

	const TEST     = 'test';
	const SQL_TEST = 'UPDATE test SET id = :id, str = :str WHERE id = :id';

	// +-----+-----------------------------------
	// | in  | $sql_name   : SQLファイル名
	// |     | $parameters : プリペアドステートメントに渡すバインドパラメータ
	// | out | CODE  : true - 正常、false - エラー
	// |     | DATA  : [ 正常時 ] 結果セット 又は 作用した行数
	// |     | ERROR : [ 異常時 ] errorInfo
	// +-----+-----------------------------------
	// SQL名（ファイル名）を指定してSQLを実行
	protected function upd_exec( $sql_name, $parameters = null ){

		$sql_path = SQL_DIR . '/upd/' . $sql_name . '.sql';

		return $this->_exec( $sql_path, $parameters, 'upd' );

	}

	// SQL（SQL名）を指定してSQLを実行
	protected function upd_exec_sql( $sql_name, $parameters = null ){

		$class = get_class( $this );
		$sql = 'SQL_' . strtoupper( $sql_name );
		$sql = constant( "$class::$sql" );

		return $this->_exec_sql( $sql, $parameters, 'upd' );

	}


// テスト用
	public function upd_test( $params ){

		$ret = $this->upd_exec( self::TEST, $params );
		return $ret[ CODE ] ? $ret[ DATA ] : $ret[ ERROR ];
	}
}

?>
