<?php

class ScheduleList extends RoomReservationPage{

	protected function setParams(){
		$this->params[ 'buildTable' ] = DTOBuildMaster::getInstance();
	}
}

?>