<?php

class init implements IF_Init{
	
	const LEVEL            = 'level';
	const TIMESTAMP        = 'timestamp';
	const PAGE             = 'page';
	const IP               = 'ip';
	const IP_AND_ROOM_NAME = 'ip_and_room_name';
	const USER             = 'user';
	const CMD              = 'cmd';
	const CMD_NAME         = 'cmd_name';
	const DETAIL           = 'detail';
	private $params;
	private $filter;

	public function __construct( $params ){

		$this->params = $params;
		
		// アクセスレベルチェック
		$ret = Common::chk_auth();

		if( !$ret[ CODE ] || $ret[ LEVEL ] !== 'STAFF' ){

			// アクセス権限がない場合エラー画面に遷移する。
			Common::goto_error( '000010' );
		}

	}

	public function set_params(){

		// 日付選択
		if( count( $this->params[ POST ] ) ){
		
			$log_date = $this->params[ POST ][ 'date' ];
			if( isset( $this->params[ POST ][ self::IP  ] ) ) $this->filter[ self::IP  ] = $this->params[ POST ][ self::IP ];
			if( isset( $this->params[ POST ][ self::CMD ] ) ) $this->filter[ self::CMD ] = $this->params[ POST ][ self::CMD ];

			$this->params[ 'filter' ] = $this->filter;

		} else {
	
			$log_date = date( 'Ymd' );
		}

		$this->params[ 'log_date' ] = $log_date;

		// ログファイルデータ取得
		$file = file( AC_LOG_DIR . "/access_{$log_date}.log", FILE_IGNORE_NEW_LINES );

		// IPアドレスリスト
		$arr_ip  = array();
		// コマンドリスト
		$arr_cmd = array();

		foreach( $file as $line ){
		
			list( $level, $timestamp, $page, $ip, $user, $cmd, $detail ) = explode( AC_LOG_DELIMITER, $line );
			$row = array( self::LEVEL            => str_replace( 'LEVEL_', '', $level ),
						  self::TIMESTAMP        => $timestamp,
						  self::PAGE             => $this->params[ 'ac_log_page' ][ $page ],
						  self::IP               => $ip,
						  self::IP_AND_ROOM_NAME => ( $room_name = $this->get_room_name( $ip ) ) ? $ip . '（' . $room_name . '）' : $ip,
						  self::USER             => $user,
						  self::CMD              => $cmd,
						  self::CMD_NAME         => isset( $this->params[ 'ac_log_cmd' ][ $cmd ] ) ? $this->params[ 'ac_log_cmd' ][ $cmd ] : $cmd,
						  self::DETAIL           => $this->format_detail( $detail ) );

			$data[] = $row;

			if( !in_array( $ip,  $arr_ip  ) ) $arr_ip[]  = $ip;
			if( !in_array( $cmd, $arr_cmd ) ) $arr_cmd[] = $cmd;
		}

		// ログデータ
		if( $this->filter ) $data = $this->apply_filter( $data );
		$this->params[ 'log_data' ] = $data;
		// 日付リスト
		$this->params[ 'date' ]     = $this->get_date_list();

		foreach( $arr_ip as &$ip ){
			$ip_and_room_name = ( $room_name = $this->get_room_name( $ip ) ) ? $ip . '（' . $room_name . '）' : $ip ;
			$ip = array( self::IP => $ip, self::IP_AND_ROOM_NAME => $ip_and_room_name ); 
		}
		// IPリスト
		$this->params[ self::IP ]   = $arr_ip;

		// コマンドリスト
		foreach( $arr_cmd as &$cmd ){
			$cmd_name = isset( $this->params[ 'ac_log_cmd' ][ $cmd ] ) ? $this->params[ 'ac_log_cmd' ][ $cmd ] : $cmd;
			$cmd      = array( self::CMD => $cmd, self::CMD_NAME => $cmd_name ); 
		}
		$this->params[ self::CMD ]  = $arr_cmd;

		return $this->params;

	}
	
	// 日付リスト取得関数
	private function get_date_list(){

		// ファイルリスト取得
		if ( $dir = opendir( AC_LOG_DIR ) ) {

			while ( ($file = readdir( $dir ) ) !== false ) {
		
				if ( $file != "." && $file != ".." && $file != ".svn" ) {
					$arr_logfile[] = $file;
				}
			}

			closedir($dir);
		}
		
		foreach( $arr_logfile as $value ){

			$date_str   = str_replace( array( 'access_', '.log' ), array( '', '' ), $value );
			$date_obj   = DateTime::createFromFormat( 'Ymd', $date_str );
			$arr_date[] = $date_obj->format( 'Y-m-d' );
		}

		// 日付順に並べ替え
		sort( $arr_date );

		return $arr_date;
	}

	// 教室名取得関数
	private function get_room_name( $ip ){

		$room_id = Common::get_my_room_id( $ip );

		$ref_pdo = new Ref_Pdo_Management();
		return $ref_pdo->ref_room_name( array( $room_id ) );
	}

	// Detail整形
	private function format_detail( $data ){

		$data = str_replace( array( '---|', ': |', '|', ':' ), array( '', " >\n", "\n", ' =' ), $data );

		return $data;
	}

	// フィルタ適用
	private function apply_filter( $data ){

		$ret = $data;
		foreach( $this->filter as $key => $value ){
			if( $value !== '-1' ){
				foreach( $ret as $idx => $row ){
					if( $row[ $key ] !== $value ) unset( $ret[ $idx ] );
				}
			}
		}

		return $ret;
	}
}
?>
