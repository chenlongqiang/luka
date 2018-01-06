<?php

class ScheduleListRefer extends Refer {

	/**
	 * 各ビルの会議室情報一覧参照
	 */
	public function getScheduleList( $id, $start_time, $dateStart, $dateStop ){
		
		$sql = 'select * from reserve where id = :id';
		$params = array();
		if( $dateStart != "undefined" ) {
			$sql .= ' and start_time > :dateStart';
			$params[] = new SqlParam( ':dateStart', $dateStart, PDO::PARAM_STR );

		}
		if( $dateStop != "undefined" ) {
			$sql .= ' and start_time < :dateStop';
			$params[] = new SqlParam( ':dateStop', $dateStop, PDO::PARAM_STR );
		}
		if( $dateStart == "undefined" && $dateStop == "undefined" ) {
			$sql .= ' and start_time > :start_time';
			$params[] = new SqlParam( ':start_time', $start_time, PDO::PARAM_STR );
		}
		$sql .= ' order by start_time';
		
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );


		
		$ret = $this->run( $sql, $params, 'GetScheduleList' );
		if( $ret[ Env::CODE ] ){
			return $ret[ Env::DATA ];
		} else {
			return null;
		}
	}


	public static function flush(){
		$ret = null;
	}
}

class GetScheduleList{
	public $id;
	public $start_time;
	public $length;
	public $username;
	public $remarks;
}

?>
