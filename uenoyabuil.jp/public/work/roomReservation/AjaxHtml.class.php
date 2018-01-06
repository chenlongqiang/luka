<?php

class AjaxHtml extends RoomReservationAjax{

	private $templateFile;
	private $cmd;
	private $id;//ここはビルID
	private $start_time;
	private $dateStart;
	private $dateStop;

	protected function setParams(){
		foreach( $this->post as $key => $value ){
			$this->$key = $value;
		}

		$cmd = $this->cmd;
		$this->$cmd();
	}

	protected function getTemplateFile(){

		switch( $this->post[ Env::CMD ] ){
			case 'updateBuildMasterDataTable'	: return 'dataTable/buildMasterDataTable';

			case 'updateRoomMasterDataTable'	: return 'dataTable/roomMasterDataTable';

			case 'selectReserveDataTable'		: return 'dataTable/reserveDataTable';

			case 'selectScheduleDataTable'		: return 'dataTable/scheduleDataTable';

			case 'selectScheduleListRoomTable'	: return 'dataTable/scheduleListRoomTable';

			case 'selectScheduleListDataTable'	: return 'dataTable/scheduleListDataTable';
		}
	}
	
	
	private function updateBuildMasterDataTable(){
	
		$this->params[ 'table' ] = DTOBuildMaster::getInstance();
	}
	

	private function updateRoomMasterDataTable(){
	

		$refer	= new RoomReservationRefer();
		$this->params[ 'table' ] = $refer->getRoomMasterData( $this->id );
	}

	private function selectReserveDataTable(){
	

		$refer	= new RoomReservationRefer();
		$this->params[ 'roomTable' ] = $refer->getRoomMasterData( $this->id );
	}

	private function selectScheduleDataTable(){
	
		$refer	= new RoomReservationRefer();
		$this->params[ 'roomTable' ] = $refer->getRoomMasterData( $this->id );
	}

	private function selectScheduleListRoomTable(){
	
		$refer	= new RoomReservationRefer();
		$this->params[ 'roomTable' ] = $refer->getRoomMasterData( $this->id );
	}

	private function selectScheduleListDataTable(){
		$refer	= new ScheduleListRefer();
		$this->params[ 'scheduleListTable' ] = $refer->getScheduleList( $this->id, $this->start_time, $this->dateStart, $this->dateStop );
	}

}

?>
