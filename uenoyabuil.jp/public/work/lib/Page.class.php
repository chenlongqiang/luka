<?php

abstract class Page extends Response{

	// HTML出力実行
	public function response(){

		$this->display();

	}
}

?>
