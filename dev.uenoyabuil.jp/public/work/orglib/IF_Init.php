<?php
// +---------------------------------------------
// | initクラス用インターフェース
// +---------------------------------------------
interface IF_Init{

	const MAP_PATH = 'map_path';

	public function __construct( $params );

	public function set_params();

}
?>
