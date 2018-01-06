<?php

class RoomReservationRegist extends Regist {

	/**
	 * ビル情報マスタ新規登録
	 */
	public function insertBuildMaster( $id, $name ){

		$sql = 'insert into m_build ( id, name ) values ( :id, :name );';

		$params = array();
		$params[] = new SqlParam( ':id'		, $id	, PDO::PARAM_INT );
		$params[] = new SqlParam( ':name'	, $name );

		return $this->run( $sql, $params );
	}

	/**
	 * ビル情報マスタ更新
	 */
	public function updateBuildMaster( $old_id, $id, $name ){
	
		$sql = 'update m_build set id = :id, name = :name where id = :old_id;';
		
		$params = array();
		$params[] = new SqlParam( ':id'			, $id	, PDO::PARAM_INT );
		$params[] = new SqlParam( ':name'		, $name );
		$params[] = new SqlParam( ':old_id'		, $old_id	, PDO::PARAM_INT );

		return $this->run( $sql, $params ); 
	}

	/**
	 * ビル情報マスタ削除
	 */
	public function deleteBuildMaster( $id ){

		$sql = 'delete from m_build where id = :id;';
		
		$params = array();
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );

		return $this->run( $sql, $params ); 
	}

	
	/**
	 * 部屋情報マスタ新規登録
	 */
	public function insertRoomMaster( $build_id, $name ){
	
		$sql = 'insert into m_room ( build_id, name ) values ( :build_id, :name );';
		
		$params = array();
		$params[] = new SqlParam( ':build_id'	, $build_id );
		$params[] = new SqlParam( ':name'	, $name );
		

		return $this->run( $sql, $params ); 
	}

	/**
	 * 部屋情報マスタ更新
	 */
	public function updateRoomMaster( $id, $name ){

		$sql = 'update m_room set name=:name where id = :id;';
		
		$params = array();
		$params[] = new SqlParam( ':id'		, $id	, PDO::PARAM_INT );
		$params[] = new SqlParam( ':name'	, $name );



		return $this->run( $sql, $params ); 
	}
	
	/**
	 * 部屋情報マスタ削除
	 */
	public function deleteRoomMaster( $id ){

		$sql = 'delete from m_room where id = :id;';
		
		$params = array();
		$params[] = new SqlParam( ':id', $id, PDO::PARAM_INT );

		return $this->run( $sql, $params ); 
	}
	

	/**
	 * 予約情報新規登録
	 */

	public function insertReserveInfo( $id, $username, $start_time, $length ){
		$sql = 'insert into reserve ( id, username, start_time, length ) values ( :id, :username, :start_time, :length );';
		
		$params = array();
		$params[] = new SqlParam( ':id'			, $id, PDO::PARAM_INT);
		$params[] = new SqlParam( ':username'	, $username );
		$params[] = new SqlParam( ':start_time'	, $start_time );
		$params[] = new SqlParam( ':length'		, $length );
		

		return $this->run( $sql, $params ); 
	}

	/**
	 * 予約情報削除
	 */

	public function deleteReserveInfo( $id, $start_time ){
		$sql = 'delete from reserve where id = :id and start_time = :start_time;';
		
		$params = array();
		$params[] = new SqlParam( ':id'			, $id, PDO::PARAM_INT );
		$params[] = new SqlParam( ':start_time'	, $start_time );
		

		return $this->run( $sql, $params ); 
	}

	/**
	 * 予約情報更新
	 */

	public function updateReserveInfo( $id, $username, $start_time, $length, $old_id, $old_start_time ){

		$sql = 'update reserve set id = :id, username = :username, start_time = :start_time, length = :length where id = :old_id and start_time = :old_start_time;';
		$params = array();
		$params[] = new SqlParam( ':id'				, $id, PDO::PARAM_INT);
		$params[] = new SqlParam( ':username'		, $username );
		$params[] = new SqlParam( ':start_time'		, $start_time );
		$params[] = new SqlParam( ':length'			, $length );
		$params[] = new SqlParam( ':old_id'			, $old_id, PDO::PARAM_INT);
		$params[] = new SqlParam( ':old_start_time'	, $old_start_time );

		return $this->run( $sql, $params ); 

	}

}
?>
