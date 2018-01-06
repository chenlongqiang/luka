<?php
require_once LEC_DIR     . "/statusLecture.php";
require_once MACHINE_DIR . "/statusMcu.php";

class init{

	const CMD = 'cmd';

	private $params;
	private $cmd;
	private $reserve_id;
	private $lec_style_preset_id;

	public function __construct( $params ){

		$this->params = $params;

		// POST データを変数に格納
		foreach( $this->params[ POST ] as $key => $value ){
			if( isset( $this->params[ POST ][ $key ] ) ) $this->$key = $value;
		}

	}

	public function set_params(){

		$funcname = $this->cmd;
		$ret = $this->$funcname();

		$arr_msg = Spyc::YAMLLoad( CONFIG_DIR . '/ctrl_msg.yaml' );
		
		$this->params[ 'json' ] = json_encode( array( 'code' => $ret, 'msg' => $arr_msg[ $this->cmd ] ) );

		// アクセスログ
		$arr_vars = get_object_vars( $this );
		foreach( $arr_vars as $key => $var ){
			if( $var ) $detail[ $key ] = $var;
		}
		unset( $detail[ PARAMS ] );
		Common::access_log( $funcname, 2, $detail );

		return $this->params;

	}

	// 講義起動
	private function lecStart()        { return statusLecture::lecStart( $this->reserve_id ); }

	// 講義停止
	private function lecStop()         { return statusLecture::lecStop( $this->reserve_id ); }

	// 講義形態プリセット変更
	private function lecChangePreset() { return statusLecture::lecChangePreset( $this->reserve_id, $this->lec_style_preset_id ); }

	// 講義予約テーブルの講義形態プリセットIDを更新
	private function updLecStylePresetId(){
		
		$upd_pdo = new Upd_Pdo_Ctrl();
		
		$data = array( 'reserve_id' => $this->reserve_id, 'lec_style_preset_id' => $this->lec_style_preset_id );
		return $upd_pdo->update_lec_style_preset_id( $data );
	}

	// 録画開始
	private function startRecording() {
		
		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );
		return statusMcu::mcuStartRecording( $mcu_id, $this->reserve_id );
	}
	
	// 録画停止
	private function stopRecording() {

		$mcu_id = Common::get_mcu_machine_id( $this->reserve_id );
		return statusMcu::mcuStopRecording($mcu_id, $this->reserve_id);
	}
	
}
?>
