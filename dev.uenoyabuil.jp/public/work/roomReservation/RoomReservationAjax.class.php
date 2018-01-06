<?php

abstract class RoomReservationAjax extends Ajax{

	protected function getSystemName(){
		return str_replace( ROOT_DIR, '', dirname( __FILE__ ) );
	}

	protected function setSmarty( &$smarty_obj ){
		
		$smarty_obj->compile_dir = ROOT_DIR . $this->getSystemName() . '/templates_c';

	}
}

?>
