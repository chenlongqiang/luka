<?php

require_once( '../../lib/Env.php' );

class Ajax extends Html{
	
	private $filename;
	private $params;

	public function __construct( $filename, $params = null ){
		
		parent::__construct( $filename, $params );

        if( !parent::get_code() ) return parent::get_error();

		$this->filename = $filename;
		$this->params = parent::get_params();
		
	}

	public function create_html(){
		
		$this->params = $this->init( $this->params );

		$this->display();

	}
	
	// フックメソッド LIB_DIR を上書き
	protected function get_init_path(){
		return AJAX_LIB_DIR . '/' . $this->filename . '_init.php';
	}
	// フックメソッド TEMPLATE_DIR、COMPILE_DIR を上書き
	protected function set_smarty_dir( &$smarty_obj ){

		$smarty_obj->template_dir = AJAX_TEMPLATE_DIR;
		$smarty_obj->compile_dir  = AJAX_COMPILE_DIR;
		
	}
}

?>
