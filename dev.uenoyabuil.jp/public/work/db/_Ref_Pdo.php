<?php

class Ref_Pdo extends ExPdo{

	// +-----+-----------------------------------
	// | in  | $sql_name   : SQLファイル名
	// |     | $parameters : プリペアドステートメントに渡すバインドパラメータ
	// | out | CODE  : 1 - 正常、0 - エラー、-1 - レコード0件
	// |     | DATA  : [ 正常時 ] 結果セット
	// |     | ERROR : [ 異常時 ] errorInfo
	// +-----+-----------------------------------
	// SQL名（ファイル名）を指定してSQLを実行
	protected function ref_exec( $sql_name, $parameters = null ){

		$sql_path = SQL_DIR . '/ref/' . $sql_name . '.sql';

		return $this->_exec( $sql_path, $parameters );

	}

	// SQL（SQL名）を指定してSQLを実行
	protected function ref_exec_sql( $sql_name, $parameters = null ){

		$class = get_class( $this );
		$sql   = 'SQL_' . strtoupper( $sql_name );
		$sql   = constant( "$class::$sql" );

		return $this->_exec_sql( $sql, $parameters );

	}

	// SQL（SQL名）を指定してSQLを実行（改）
	// +-----+-----------------------------------------------------------------
	// | out | [正常時] データ配列 / [レコード0件] 空配列 / [エラー時] false
	// +-----+-----------------------------------------------------------------
	protected function ref_exec_sql1( $sql_name, $parameters = null ){

		$class = get_class( $this );
		$sql   = 'SQL_' . strtoupper( $sql_name );
		$sql   = constant( "$class::$sql" );

		$ret   = $this->_exec_sql( $sql, $parameters );

		if( $ret[ CODE ] > 0 ){
			return $ret[ DATA ];
		} else if( $ret[ CODE ] < 0 ){
			return array();
		} else {
			return false;
		}
	}

	// SQL（SQL名）を指定してSQLを実行し、結果を一行取得
	// +-----+-----------------------------------------------------------------
	// | out | [正常時] データ配列 / [レコード0件] 空配列 / [エラー時] false
	// +-----+-----------------------------------------------------------------
	protected function ref_exec_row( $sql_name, $parameters = null ){

		$ret = $this->ref_exec_sql( $sql_name, $parameters );
		if( $ret[ CODE ] > 0 ){
			return $ret[ DATA ][ 0 ];
		} else if ( $ret[ CODE ] < 0 ){
			return array();
		} else {
			return false;
		}
	}

	// SQL（SQL名）を指定してSQLを実行し、結果セットの単一カラムを取得（最初のカラム）
	// +-----+-----------------------------------------------------------------
	// | out | [正常時] データ配列 / [レコード0件] 空配列 / [エラー時] false
	// +-----+-----------------------------------------------------------------
	protected function ref_exec_column( $sql_name, $parameters = null, $column ){

		$ret = $this->ref_exec_sql( $sql_name, $parameters, 'ref' );
		if( $ret[ CODE ] > 0 ){
			foreach( $ret[ DATA ] as $value ){
				$arr_ret[] = $value[ $column ];
			}
			return $arr_ret;
		} else if ( $ret[ CODE ] < 0 ){
			return array();
		} else {
			return false;
		}

	}

	// SQL（SQL名）を指定してSQLを実行し、結果の一行目から指定カラムのデータを取得（単一データを取得）
	// +-----+-----------------------------------------------------------------
	// | out | [正常時] データ /  [レコード0件 又は エラー時] false
	// +-----+-----------------------------------------------------------------

	protected function ref_exec_data( $sql_name, $parameters = null, $column ){

		$ret = $this->ref_exec_sql( $sql_name, $parameters );
		if( $ret[ CODE ] > 0 ){
			return $ret[ DATA ][ 0 ][ $column ];
		} else {
			return false;
		}

	}

	// SQL（SQL名）を指定してSQLを実行（改） LIMIT 不具合対応版
	// +-----+-----------------------------------------------------------------
	// | out | [正常時] データ配列 / [レコード0件] 空配列 / [エラー時] false
	// +-----+-----------------------------------------------------------------
	protected function ref_exec_sql1_limit( $sql_name, $parameters = null ){

		$class = get_class( $this );
		$sql   = 'SQL_' . strtoupper( $sql_name );
		$sql   = constant( "$class::$sql" );

		$ret   = $this->_exec_sql_limit( $sql, $parameters );

		if( $ret[ CODE ] > 0 ){
			return $ret[ DATA ];
		} else if( $ret[ CODE ] < 0 ){
			return array();
		} else {
			return false;
		}
	}

}

?>
