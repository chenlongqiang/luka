<?php

class init implements IF_Init{
	
	private $params;

	public function __construct( $params ){

		$this->params = $params;

	}

	public function set_params(){
		
		// 教室リストを取得
		$this->params[ "room_list" ] = $this->get_room_list();

		$auth = Common::chk_auth();
		if( !$auth[ CODE ] ){
			switch( $this->params[ CTRL_MODE ] ){
				case 'TOKYO_OFFICE' :
					
					foreach( $this->params[ 'room_list' ] as $key => $room ){
						
						$ret = Common::get_room_mode( $room[ 'room_id' ] );
						if( strpos( $ret[ ROOM_MODE ], 'tokyo_office' ) === false ){
			
							unset( $this->params[ 'room_list' ][ $key ] );

						}
					}
					break;
			}
		}
			
		// グループの先頭を示すフラグを追加
		$group_name = null;
		foreach( $this->params[ 'room_list' ] as &$value ){

			if( $value[ 'group_name' ] && $value[ 'group_name' ] != $group_name ) $value[ 'group_first' ] = true; 
			$group_name = $value[ 'group_name' ];
			$value[ 'group_name_jp' ] = $this->params[ 'group_name' ][ $group_name ]; 
		}

		// 教室リストグループ名表示フラグ
		$this->params[ 'disp_group_name' ] = DISP_GROUP_NAME;

		return $this->params;

	}

	private function get_room_list(){

		$ref_pdo = new Ref_Pdo_Init();
		return $ref_pdo->ref_room_list();
	}
	
}
?>
