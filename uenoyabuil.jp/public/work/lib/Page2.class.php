<?php

abstract class Page2 extends Response{

	// HTML出力実行
	public function response(){

		$v = new Validation( $this->getSystemName() );
		$ret = $v->check( $this->get, $this->filename );
var_dump( $ret );

		$this->display();

	}
}

?>
