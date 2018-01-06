<?php

class BuildMaster extends RoomReservationPage{

	protected function setParams(){
		
		$this->params[ 'table' ] = DTOBuildMaster::getInstance();
	}
}

?>
