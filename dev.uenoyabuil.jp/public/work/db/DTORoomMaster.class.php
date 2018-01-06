<?php
class DTORoomMaster implements DTO {
	
	public static $inData = array();

	private function __construct(){}
	
	public static function getInstance(){
		
		if( isset( $ret ) ){
			
			return $ret[ Env::DATA ];

		} else {

			$refer	= new Refer();
//			$buildId = self::$inData['buildId'];
			$ret	= $refer->run( 'select * from m_room order by id;', null, 'RoomMasterData' );
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

	public static function getRow( $buildId ){
		
		$refer	= new Refer();
		$params = array();
		$params[] = new SqlParam( ':buildId', $buildId, PDO::PARAM_INT );
		$ret    = $refer->run( 'select * from m_room where build_id = :buildId', $params, 'RoomMasterData' );
		
		
		if( $ret[ Env::CODE ] ){
			return $ret[ Env::DATA ];
		} else {
			return null;
		}
	}

}

class RoomMasterData{
	public $build_id;
	public $id;
	public $name;
}
?>
