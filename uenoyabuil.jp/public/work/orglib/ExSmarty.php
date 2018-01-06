<?php

class ExSmarty extends Smarty{

	public function __construct(){

		$this->Smarty();
		$this->template_dir = TEMPLATE_DIR;
		$this->compile_dir  = COMPILE_DIR;

	}
}
?>
