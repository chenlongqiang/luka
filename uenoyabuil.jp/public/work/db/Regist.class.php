<?php
class Regist extends ExPDO{

	protected function run( $sql, $parameters = null ){

		try {

			$statement = $this->prepare( $sql );

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
					
				return array( Env::CODE => true	);

			} else {

				return array( Env::CODE => 0, Env::ERROR => $statement->errorInfo() );
			
			}

		} catch( PDOException $e ) {

			return array( Env::CODE  => 0, Env::ERROR => $e->getCode . ' : ' . $e->getMessage() );

		}
	}
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
