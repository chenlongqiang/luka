<?php
class db_api extends PDO{
	
	public $obj_pdo;
	public $status;

	public function __construct(){
		try {

			parent::__construct('sqlite:' . UI_DIR . '/sqlite/schedule.db');

		} catch( PDOException $e) {

			var_dump( $e->getMessage() );
			$this->status = 'ERROR';
		}

	}

	public function exec( $sql_path, $parameters = null ){
		
		$sql = file_get_contents( $sql_path );
	
		try {
		
			$obj_st = $this->prepare( $sql );
			$obj_st->execute( $parameters );
		
			$ret = $obj_st->fetchAll( PDO::FETCH_ASSOC );

			return $ret;

		} catch( PDOException $e ) {

			var_dump( $e->getMessage() );
			$this->status = 'ERROR';
			
			return array(
						'code' => $obj_st->errorCode(),
						'info' => $obj_st->errorInfo()
					);
		}
	}
}
?>
