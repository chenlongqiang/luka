<?php
class DTOBuildMaster implements DTO {
	
	private function __construct(){}

	public static function getInstance(){
		
		if( isset( $ret ) ){
			
			return $ret[ Env::DATA ];

		} else {

			$refer	= new Refer();
			$ret	= $refer->run( 'select * from m_build order by id;', null, 'BuildMasterData' );
			if( $ret[ Env::CODE ] ){
				return $ret[ Env::DATA ];
			} else {
				return null;
			}
		}
	}

	public static function flush(){
		$ret = null;
	}

	public static function getRow( $id ){
		
		$refer	= new Refer();
		$params = array();
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );
		$ret    = $refer->run( 'select * from m_build where id = :id', $params, 'BuildMasterData' );
		
		if( $ret[ Env::CODE ] ){
			return $ret[ Env::DATA ];
		} else {
			return null;
		}
	}
}

class BuildMasterData{
	public $id;
	public $name;
}
?>
