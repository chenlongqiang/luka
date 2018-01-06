<?php

class AjaxRegist extends RoomReservationAjax{

	private $regist;

	private $cmd;
	private $id;
	private $name;
	private $old_id;
	private $build_id;
	private $username;
	private $start_time;
	private $length;
	private $old_start_time;

	protected function setParams(){
		$this->regist = new RoomReservationRegist();

		$v = new Validation( $this->getSystemName() );

		$ret = $v->check( $this->post, $this->post[ Env::CMD ] );
		if( !$ret[ 'code' ] ) {
			$this->params[ 'return' ] = json_encode( $ret );
		} else {
			foreach( $this->post as $key => $value ){
				$this->$key = $value;
			}
			$cmd = $this->cmd;

			$this->params[ 'return' ] = json_encode( $this->$cmd() );
		}
	}

	/**
	 * テンプレートファイル設定
	 */
	protected function getTemplateFile(){
		
		return 'ajax/json';
	}

	// ビル情報新規登録
	private function insertBuildMaster(){ return $this->regist->insertBuildMaster( $this->id, $this->name ); }

	// ビル情報更新
	private function updateBuildMaster(){ return $this->regist->updateBuildMaster( $this->old_id, $this->id, $this->name ); }

	// ビル情報削除
	private function deleteBuildMaster(){ return $this->regist->deleteBuildMaster( $this->id ); }

	// 部屋情報新規登録
	private function insertRoomMaster(){ return $this->regist->insertRoomMaster( $this->build_id, $this->name ); }

	// 部屋情報更新
	private function updateRoomMaster(){ return $this->regist->updateRoomMaster( $this->id, $this->name ); }

	// 部屋情報削除
	private function deleteRoomMaster(){ return $this->regist->deleteRoomMaster( $this->id ); }

	// 予約情報登録
	private function insertReserveInfo(){ return $this->regist->insertReserveInfo( $this->id, $this->username, $this->start_time, $this->length ); }

	// 予約情報削除
	private function deleteReserveInfo(){ return $this->regist->deleteReserveInfo( $this->id, $this->start_time ); }

	// 予約情報更新
	private function updateReserveInfo(){ return $this->regist->updateReserveInfo( $this->id, $this->username, $this->start_time, $this->length, $this->old_id, $this->old_start_time ); }

}
?>
