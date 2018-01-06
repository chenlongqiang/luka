<?php
class Refer extends ExPDO{

	public function run( $sql, $parameters = null, $class = null ){

		try {

			$statement	= $this->prepare( $sql );

			if( isset( $parameters ) ){
				foreach( $parameters as $param ){
					if( isset( $param->type ) ){
						$statement->bindValue( $param->name, $param->value, $param->type );
					} else {
						$statement->bindValue( $param->name, $param->value );
					}
				}
			}

			$status = $statement->execute();

			if( $status ){
					
				if( isset( $class ) ){
					$ret = $statement->fetchAll( PDO::FETCH_CLASS, $class );
				} else {
					$ret = $statement->fetchAll();
				}

				if( count( $ret ) ){
					return array( Env::CODE => true		, Env::DATA => $ret );
				} else {
					return array( Env::CODE => false	, Env::DATA => null );
				}

			} else {
				return array( Env::CODE => 0, Env::ERROR => $statement->errorInfo() );
			}

		} catch( PDOException $e ) {

			return array( Env::CODE  => 0, Env::ERROR => $e->getCode . ' : ' . $e->getMessage() );

		}
	}
}

/*
 * データ格納用クラス
 */
class IdAndName{
	public $id;
	public $name;
}

class SqlParam{
	public $name;
	public $value;
	public $type;

	public function __construct( $name, $value, $type = null ){
		$this->name		= $name;
		$this->value	= $value;
		$this->type		= $type;
	}
}


?>
