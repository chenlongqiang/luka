<?php

abstract class RoomReservationPage extends Page{

	protected function getSystemName(){

		return str_replace( ROOT_DIR, '', dirname( __FILE__ ) );

	}

	protected function setSmarty( &$smarty_obj ){
		
		$smarty_obj->compile_dir = ROOT_DIR . $this->getSystemName() . '/templates_c';

	}
}

?>
