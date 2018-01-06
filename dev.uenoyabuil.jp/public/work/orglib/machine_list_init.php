<?php

require_once DB_DIR . "/DAO/DaoRoomMaster.php";

class init{
	
	private $params;
	private $room_id;
	const MACHINE_LIST    = 'machine_list';

	public function __construct( $params ){

		$this->params = $params;
		
		if(isset( $_POST[ ROOM_ID ] ) ) {
			
			$this->room_id = $_POST[ ROOM_ID ];
			$this->params[ HIDDEN ][ ROOM_ID ] = $_POST[ ROOM_ID ];
			
		} else if( isset( $_SESSION[ ROOM_ID ] ) ){

			$this->room_id = $_SESSION[ ROOM_ID ];
		}

		// パラメータがない場合エラー画面に遷移する。
		if( !$this->room_id ) Common::goto_error( '000004' ); 

	}

	public function set_params(){
		
		// 教室名
		$this->get_room_name();

		// 機器リスト取得
		$this->params[ init::MACHINE_LIST ]  = $this->get_machine_list();
		
		// YAML読み込み
        $machine_name_list = Spyc::YAMLLoad( CONFIG_DIR . '/machine.yaml' );
		
		$sort_key_arr = array();
		
		$key = "";
		
		// 映像機器名をYAMLから取得
		foreach( $this->params[ init::MACHINE_LIST ] as &$v_value ){
			// 機器表示名を追加
			if (isset( $machine_name_list[ $v_value[ 'machine_name_code' ] ]) ) {
				$key = $machine_name_list[ $v_value[ 'machine_name_code' ] ];
				$v_value[ 'machine_caption' ]   = $key;
			} else {
				$key = $v_value[ 'machine_name_code' ];
				$v_value[ 'machine_caption' ]   = $key;
			}
			$sort_key_arr[ $key ] = $v_value;
		}
		
		// 名称でソート
		ksort( $sort_key_arr );
		
		$machine_list_arr = new ArrayObject();
		
		foreach ( $sort_key_arr as $v ) {
			$machine_list_arr[] = $v;
		}
		
		$this->params[ init::MACHINE_LIST ] = $machine_list_arr;

		return $this->params;

	}

	// 映像機器リスト取得関数
	private function get_machine_list(){
		
		$ref_pdo = new Ref_Pdo_Machines();
		return $ref_pdo->ref_machines( array( $this->room_id ) );
	}
	
	private function get_room_name() {

		$roomDao = new DaoRoomMaster();
		$this->params[ 'room_name' ] = $roomDao->getRoomNameByRoomId($this->room_id);
	}
}
?>
