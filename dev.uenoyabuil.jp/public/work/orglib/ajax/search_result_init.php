<?php

class init{
	
	const ROWS_PER_PAGE    = 20;
	const DEF_KEYWORD      = '%';
	const DEF_START_TIME   = '00:00:00';
	const DEF_END_TIME	   = '23:59:59';
	const DEF_START_PERIOD = 0;
	const DEF_END_PERIOD   = 6;
	const DEF_START_DATE   = '2010-03-01';	
	const DEF_END_DATE     = '2099-12-31';

	private $params;
	private $cond;

	public function __construct( $params ){

		$this->params = $params;

	}

	public function set_params(){
		
		// 検索条件
		foreach( $this->params[ POST ] as $key => $value ){

			$this->cond[ $key ] = $value; 
		}

		//ログイン状態
		$ret = Common::chk_auth();
		$this->params[ LEVEL ] = $ret[ LEVEL ];

		// 検索結果リスト取得
        $this->params[ 'result' ]  = $this->get_search_result_data();

		return $this->params;

	}

	// 検索結果リスト取得関数
    private function get_search_result_data(){

		// POSTされた値を変換
		// キーワード
		$keyword = $this->cond[ 'keyword' ] ? '%' . $this->cond[ 'keyword' ] . '%' : init::DEF_KEYWORD;

		// 開始時刻
		if( (int)$this->cond[ 'hour_start' ] === -1 || (int)$this->cond[ 'min_start' ] === -1 ){
			$start_time	= init::DEF_START_TIME;
		} else {
			$start_time	= $this->cond[ 'hour_start' ] . ':' . $this->cond[ 'min_start' ] . ':00';
		}

		// 終了時刻
		if( (int)$this->cond[ 'hour_end' ] === -1 || (int)$this->cond[ 'min_end' ] === -1 ){
			$end_time = init::DEF_END_TIME;
		} else {
			$end_time = $this->cond[ 'hour_end' ] . ':' . $this->cond[ 'min_end' ] . ':00';
		}

		// 開始日
		if( !$this->cond[ 'year_start' ] || !$this->cond[ 'month_start' ] || !$this->cond[ 'day_start' ] || ( isset( $this->cond[ 'inc_past' ] ) && $this->cond[ 'inc_past' ] === 'on' ) ){
			$start_date = init::DEF_START_DATE;
		} else {
			$start_date = $this->cond[ 'year_start' ] . '-' . $this->cond[ 'month_start' ] . '-' . $this->cond[ 'day_start' ];
		}

		// 終了日
		if( !$this->cond[ 'year_end' ] || !$this->cond[ 'month_end' ] || !$this->cond[ 'day_end' ] ){
			$end_date = init::DEF_END_DATE;
		} else {
			$end_date = $this->cond[ 'year_end' ] . '-' . $this->cond[ 'month_end' ] . '-' . $this->cond[ 'day_end' ];
		}

		// ページング用の制御
		$start_index = ( $this->cond[ 'page' ] - 1 ) * init::ROWS_PER_PAGE;
		$row_num     = init::ROWS_PER_PAGE;
	
		$this->params[ 'rows_per_page' ] = init::ROWS_PER_PAGE;
		$this->params[ 'cur_page' ]      = (int)$this->cond[ 'page' ];

        $ref_pdo     = new Ref_Pdo_Search();
		$params      = array( $keyword, $end_time, $start_time, $start_date, $end_date, $start_index, $row_num );
		$params_cnt  = array( $keyword, $end_time, $start_time, $start_date, $end_date );

		// 連続した講義を表示
		if( isset( $this->cond[ 'view_serial' ] ) && $this->cond[ 'view_serial' ] === 'on' ){
			
			$ret = $ref_pdo->ref_search( $params );
			$cnt = $ref_pdo->ref_search_count( $params_cnt );

		} else {

			$ret = $ref_pdo->ref_search_lec( $params );
			$cnt = $ref_pdo->ref_search_count_lec( $params_cnt );
		}

		$this->params[ 'ret_data' ] = $ret;

        // ページング用に最大ページを取得
		
		$this->params[ 'ret_count' ] = $cnt;       
        $this->params[ 'max_page' ]  =  (int) ceil( $cnt / init::ROWS_PER_PAGE );

        return $ret;
    }
    
}
?>
