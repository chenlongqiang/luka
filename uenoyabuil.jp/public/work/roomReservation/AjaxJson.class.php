<?php

class AjaxJson extends RoomReservationAjax{

	private $cmd;
	private $id;
	private $prev_start_time;
	private $start_time;
	private $next_start_time;

	protected function setParams(){

		foreach( $this->post as $key => $value ){
			$this->$key = $value;
		}

		$cmd = $this->cmd;

		$this->$cmd();
	}

	protected function getTemplateFile(){
		
		return 'ajax/json';
	}

	private function selectScheduleData() {

		$refer	= new ScheduleReservationRefer();
		$result = $refer->getScheduleData( $this->id );
//		$result = $refer->getScheduleData( $this->id, $this->prev_start_time, $this->start_time, $this->next_start_time );
		if (is_null($result)) {
			$result = array();
		}
		//日時から年月日を取得する
		$return = array();
		foreach($result as $key => $value){
			$date = explode(" ", $value->start_time);
			if( !isset( $return[ $key ] ) ) {
				$return[ $key ][ 'date' ] = $date[0];
			}
			$date[2] = explode("-", $date[0]);
			$date[3] = explode(":", $date[1]);
			$y = $date[2][0];
			$m = $date[2][1];
			$d = $date[2][2];
			$h = $date[3][0];
			$i = $date[3][1];
			$s = $date[3][2];
			$time = mktime($h, $i, $s, $m, $d, $y);
			
			//午前＆午後判断
			$a = date("a", $time);

			$length = explode(":", $value->length);

			//「午前」及び五時間以上予約希望の場合
			if( $a == "am" && $length[0]*1 >= 5 ){
				$return[ $key ][ 'a' ] = "all";
			} else {
				$return[ $key ][ 'a' ] = $a;
			}
		}
		$this->params[ 'return' ] = json_encode($return);
	}

}

?>
