<?php

// インターフェース「IF_Html」
//////////////////////////////
interface IF_Html{

	// コンストラクタ
	public function __construct( $filename, $params = null );

	// HTML出力実行
	public function create_html();

}
?>
