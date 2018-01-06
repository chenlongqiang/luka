<?php

class ScheduleReservationRefer extends Refer {

	/**
	 * 各ビルの会議室情報参照
	 */
//	public function getScheduleData( $id, $prev_start_time, $start_time, $next_start_time ){
	public function getScheduleData( $id ){

//		$sql = 'select * from reserve where id = :id and ( start_time like :prev_start_time or start_time like :start_time or start_time like :next_start_time);';
		$sql = 'select * from reserve where id = :id;';

		$params = array();
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );
//		$params[] = new SqlParam( ':prev_start_time', $prev_start_time . '%', PDO::PARAM_STR );
//		$params[] = new SqlParam( ':start_time', $start_time . '%', PDO::PARAM_STR );
//		$params[] = new SqlParam( ':next_start_time', $next_start_time . '%', PDO::PARAM_STR );

		
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
//	public $reserve_id;
	public $id;
	public $start_time;
	public $length;
	public $username;
	public $remarks;
}

?>
