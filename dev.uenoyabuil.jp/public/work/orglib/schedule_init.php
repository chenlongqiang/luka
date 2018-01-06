<?php
require_once LEC_DIR  . "/statusLecture.php";

class init implements IF_Init{

	const   DEFAULT_WEEKS = 4;
	const   MAX_LEC_VIEW  = 6;	// カレンダー1日分に表示する最大数
	private $params;
	private $room_id;

	public function __construct( $params ){

		$this->params = $params;

		// アクセスレベルチェック
		$ret = Common::chk_auth();

		if( !$ret[ CODE ] || $ret[ LEVEL ] !== 'STAFF' ){

			$this->room_id = Common::get_my_room_id( $_SERVER[ 'REMOTE_ADDR' ] );

			if( $this->room_id ){

				// ROOM_ID を HIDDEN に追加
				$this->params[ HIDDEN ][ 'room_id' ] = $this->room_id;

			} else {

				// アクセス権限がない場合エラー画面に遷移する。
				Common::goto_error( '000010' );
			}
		}
	}

	public function set_params(){

		// 本日のリスト取得
		$this->params[ 'todays_list' ] = $this->get_today_data( $this->room_id );
		
		// 現在の日付
		$today = new DateTime();
		$this->params[ HIDDEN ][ 'today' ] = $today->format( 'Y/m/d' );

		if( isset( $this->params[ POST ][ 'current_date' ] ) ){
			
			$c_date = new DateTime( $this->params[ POST ][ 'current_date' ] );

		} else {
			
			$c_date = $today;

		}
		
		// カレント日付を HIDDEN に設定
		$this->params[ HIDDEN ][ 'current_date' ] = $c_date->format( 'Y/m/d' );

		// カレンダー表示段数（週）
		$this->params[ 'weeks' ] = init::DEFAULT_WEEKS;
		if( isset( $this->params[ POST ][ 'week_view' ] ) ) $this->params[ 'weeks' ] = 1;

		// カレンダー1日分表示件数
		$this->params[ 'max_lec_view' ] = init::MAX_LEC_VIEW;

		// カレンダー用開始日の設定
		$weekday = (int)$c_date->format( 'w' ) == 0 ? 7 : (int)$c_date->format( 'w' );

		$tmp_date   = clone $c_date;
		$start_date = $tmp_date->sub( new DateInterval( 'P' . ( $weekday -1 ) . 'D' ) );

		// カレンダー用リスト取得
		$this->params[ 'calendar_list' ] = $this->get_calendar_data( $start_date, $this->room_id ); 

		$tmp_date = clone $c_date;
		$sunday   = $tmp_date->add( new DateInterval( 'P' . ( 7 - $weekday ) . 'D' ) );

		$this->params[ 'year' ]  = $sunday->format( 'Y' );
		$this->params[ 'month' ] = $sunday->format( 'n' );

		// 確認画面のURL
		$this->params[ 'confirm_url' ] = 'confirm.php';

		// ステータスリフレッシュ用項目
		$this->params[ HIDDEN ][ 'lamp_state' ] = $this->getLectureStatus();
		$this->params[ HIDDEN ][ 'flg_status' ] = 'timer';

		return $this->params;

	}

	// 本日の講義データ取得関数
	private function get_today_data( $room_id = null ){

		$ref_pdo = new Ref_Pdo_Schedule();
		
		if( $room_id ){
			return $ref_pdo->ref_today_data_by_room( array( $room_id, 0, 8 ) );
		} else {
			return $ref_pdo->ref_today_data( array( 0, 8 ) );
		}
	}

	// カレンダー用データ取得関数
	private function get_calendar_data( $start_date, $room_id = null ){

		// 開始日以降を抽出
		$ref_pdo = new Ref_Pdo_Schedule();
		if( $this->params[ CTRL_MODE ] !== 'TOKYO_OFFICE' && $room_id ){
	
			$ret = $ref_pdo->ref_calendar_data_by_room( array( $start_date->format( 'Y-m-d' ), $room_id ) );

		} else {

			$ret = $ref_pdo->ref_calendar_data( array( $start_date->format( 'Y-m-d' ) ) );
		}

		// 日数分の要素を持つ配列にデータを格納
		$ret_data = null;
		$tmp_date = $start_date;

		// 各週の最大講義数
		$cnt_week_max = array( 0, 0, 0, 0 );
		for( $week = 0; $week < $this->params[ 'weeks' ]; $week++ ){

			for( $i = 0; $i<7; $i++ ){

				$j = 0;
				$f_tmp_date = $tmp_date->format( 'Y-m-d' );
				foreach( $ret as $key => $val ){

					// 講義予約情報
					if( $val['lec_date'] === $f_tmp_date ){
						$ret_data[ $week ][ $i ][ 'reserve' ][ $j++ ] = $val;
					} else {
						$ret_data[ $week ][ $i ][ 'reserve' ][ $j ]   = '';
					}
				}

				// 各週の講義データの最大数
				if( isset( $ret_data[ $week ][ $i ] ) ) $cnt_week_max[ $week ] = max( $cnt_week_max[ $week ], min( count( $ret_data[ $week ][ $i ][ 'reserve' ] )-1, init::MAX_LEC_VIEW ) );

				// 日付用データ
				if( $tmp_date->format( 'j' ) === '1' ){
					$ret_data[ $week ][ $i ][ 'h_date' ] = $tmp_date->format( 'n月 j日' );
				} else {
					$ret_data[ $week ][ $i ][ 'h_date' ] = $tmp_date->format( 'j' );
				}
				//一日進める
				$tmp_date->add( new DateInterval( 'P1D' ) );
			}
			
		}
		// 各週の最大講義数の合計が1日あたりの最大表示数の2倍を超える場合は3週分のみ
		if( array_sum( $cnt_week_max ) >= ( init::MAX_LEC_VIEW * 2 ) ) unset( $ret_data[ 3 ] );
		// 1週目、2週目の最大講義数がともに MAX_LEC_VIEW なら2週分のみ
		if( $cnt_week_max[ 0 ] >= init::MAX_LEC_VIEW && $cnt_week_max[ 1 ] >= init::MAX_LEC_VIEW ) unset( $ret_data[ 2 ] );

		return $ret_data;
	}
	
	// 講義状態取得（ランプ用） 
	private function getLectureStatus(){
		
		// 講義ステータスランプ
		$ref_pdo = new Ref_Pdo_Schedule();
		$lecture_list = $ref_pdo->ref_today_reserve_id();

		foreach( $lecture_list as &$value ){
			$lec_status[ $value ] = statusLecture::getLectureStatus( $value );
		}
		
		if( isset( $lec_status ) ) $ret[ 'lec_status' ] = $lec_status;

		// 他の日付の講義リスト
		$auth = Common::chk_auth();
		if( $auth[ CODE ] ){
			$other_day_lec_list = $ref_pdo->ref_other_day_lec();
		} else {
			$other_day_lec_list = $ref_pdo->ref_other_day_lec_by_room( array( Common::get_my_room_id_2() ) );
		}

		if( count( $other_day_lec_list ) ) $ret[ 'other_day_lec_list' ] = $other_day_lec_list;

		return isset( $ret ) ? json_encode( $ret ) : ''; 
	}

}
?>
