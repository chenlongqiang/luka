<?php

class Schedule extends RoomReservationPage{

	protected function setParams(){
		$this->params[ 'buildTable' ] = DTOBuildMaster::getInstance();
	}
}

?>
