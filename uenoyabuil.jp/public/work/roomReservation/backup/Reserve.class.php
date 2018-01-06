<?php

class Reserve extends RoomReservationPage{

	

	protected function setParams(){

		//編集画面の場合
		if( !empty($this->post) ) {
		
			$this->params[ 'flg' ] = "edit";
			
			$this->params[ 'buildTable' ] = DTOBuildMaster::getRow( $this->post['buildId'] );
			$this->params[ 'roomTable' ] = DTORoomMaster::getRow( $this->post['buildId'] );
			$refer = new RoomReservationRefer();
			$tmp = $refer->getReserveListData( $this->post['roomId'], $this->post['startTime'] );
			$this->params[ 'row' ] = $tmp[0];

			//古い情報　部屋IDと希望予約時間
/*

			$date = explode(" ", $tmp[ 'start_time' ]);
			$date[2] = explode("-", $date[0]);
			$date[3] = explode(":", $date[1]);
			$y = $date[2][0];
			$m = $date[2][1];
			$d = $date[2][2];


			$this->params[ 'year' ] 	= $y;
			$this->params[ 'month' ]	= $m;
			$this->params[ 'day' ] 		= $d;

			$this->params[ 'startTime' ] = $date[1];
			$this->params[ 'timeLength' ] = $tmp[ 'length' ];
			$this->params[ 'username' ] = $tmp[ 'username' ];
*/
		
		
		} else {
			$this->params[ 'flg' ] = "newRegist";
			$this->params[ 'buildTable' ] = DTOBuildMaster::getInstance();
//			$this->params[ 'roomTable' ] = DTOBuildMaster::getInstance();

		}






		for( $i = 2011; $i <= 2015; $i++ ) {
			$year[] = array( 'year' => $i );
		}

		for( $i = 1; $i <= 12; $i++ ) {
			$month[] = array( 'month' => $i );
		}
		
		for( $i = 1; $i <= 31; $i++ ) {
			$day[] = array( 'day' => $i );
		}
		
		$this->params[ 'year' ] 	= $year;
		$this->params[ 'month' ]	= $month;
		$this->params[ 'day' ] 		= $day;


		$startTime = array(
			"9:00",
			"9:30",	
			"10:00",
			"10:30",
			"11:00",
			"11:30",
			"12:00",
			"12:30",
			"13:00",
			"13:30",
			"14:00",
			"14:30",
			"15:00",
			"15:30",
			"16:00",
			"16:30",
			"17:00",
			"17:30",
			"18:00"
		);

		$timeLength = array(
			"00:30:00" => "30分",
			"01:00:00" => "1時間",
			"01:30:00" => "1時間30分",
			"02:00:00" => "2時間",
			"02:30:00" => "2時間30分",
			"03:00:00" => "3時間",
			"03:30:00" => "3時間30分",
			"04:00:00" => "4時間",
			"04:30:00" => "4時間30分",
			"05:00:00" => "5時間",
			"06:00:00" => "6時間",
			"07:00:00" => "7時間",
			"08:00:00" => "8時間",
			"09:00:00" => "9時間",
			"10:00:00" => "9時間以上"

		);

		$this->params[ 'startTime' ] = $startTime;
		$this->params[ 'timeLength' ] = $timeLength;



	}
}

?>
