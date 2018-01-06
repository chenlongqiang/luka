<?php

class RoomMaster extends RoomReservationPage{

	protected function setParams(){
		
		$this->params[ 'table' ] = DTOBuildMaster::getInstance();
	}
}

?>
