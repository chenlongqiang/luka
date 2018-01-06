<?php

abstract class Ajax extends Response{

	// HTML出力実行
	public function response(){

		$this->display( $this->getTemplateFile() );

	}

	abstract protected function getTemplateFile();
}

?>
