<?php

class RoomReservationRefer extends Refer {

	/**
	 * 各ビルの会議室情報参照
	 */
	public function getRoomMasterData( $id ){

		$sql = 'select m_build.name AS build_name, m_room.build_id AS build_id, m_room.id AS id, m_room.name AS name from m_build INNER JOIN m_room on m_build.id = m_room.build_id where m_room.build_id = :id;';

		$params = array();
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );

		$ret = $this->run( $sql, $params, 'GetRoomList' );
		if( $ret[ Env::CODE ] ){
			return $ret[ Env::DATA ];
		} else {
			return null;
		}
	}


	public function getReserveListData( $roomId, $startTime ){

		$sql = "select * from reserve where id = :roomId and start_time = :startTime;";

		$params = array();
		$params[] = new SqlParam( ':roomId', $roomId, PDO::PARAM_INT );
		$params[] = new SqlParam( ':startTime', $startTime, PDO::PARAM_STR, 19 );

		$ret = $this->run( $sql, $params, 'GetReseveList' );

		if( $ret[ Env::CODE ] ){
			return $ret[ Env::DATA ];
		} else {
			return null;
		}


	}

}

class GetRoomList{
	public $build_name;
	public $build_id;
	public $id;
	public $name;
}

class GetReseveList{
	public $id;
	public $start_time;
	public $length;
	public $username;
	public $remarks;
}













?>
