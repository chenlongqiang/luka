<?php

class UserSchedule extends RoomReservationPage{

	protected function setParams(){
		$this->params[ 'buildTable' ] = DTOBuildMaster::getInstance();
	}
}

?>
