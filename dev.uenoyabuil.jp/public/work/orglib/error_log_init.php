<?php

class init implements IF_Init{
	
	const TIMESTAMP        = 'timestamp';
	const ROOM_MACHINE_ID  = 'room_machine_id';
	const ROOM_NAME        = 'room_name';
	const MACHINE_TYPE     = 'machine_type';
	const ERR_MSG          = 'err_msg';
	const ROOM             = 'room';
	const MACHINE_MODEL    = 'machine_model';
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
			if( isset( $this->params[ POST ][ self::ROOM_NAME ] )    ) $this->filter[ self::ROOM_NAME    ] = $this->params[ POST ][ self::ROOM_NAME ];
			if( isset( $this->params[ POST ][ self::MACHINE_TYPE ] ) ) $this->filter[ self::MACHINE_TYPE ] = $this->params[ POST ][ self::MACHINE_TYPE ];

			$this->params[ 'filter' ] = $this->filter;

		} else {
	
			$log_date = date( 'Ymd' );
		}

		$this->params[ 'log_date' ] = $log_date;

		// ログファイルデータ取得
		$filename = ERR_LOG_DIR . "/error_{$log_date}.log";
		if( is_readable( $filename ) ){

			$file = file( $filename, FILE_IGNORE_NEW_LINES );

			$arr_room         = array();	// 教室リスト
			$arr_machine_type = array();	// 機器タイプリスト

			foreach( $file as $line ){
			
				list( $timestamp, $room_machine_id, $machine_type, $err_msg ) = explode( ERR_LOG_DELIMITER, $line );
				$room_name = $this->get_room_name( substr( $room_machine_id, 0, 2 ) );
				$row = array(
							  self::TIMESTAMP        => $timestamp,
							  self::ROOM_MACHINE_ID  => $room_machine_id,
							  self::ROOM_NAME        => $room_name,
							  self::MACHINE_TYPE     => $machine_type,
							  self::MACHINE_MODEL    => $this->get_machine_model_name( $machine_type ),
							  self::ERR_MSG          => $err_msg );

				$data[] = $row;

				if( !in_array( $room_name,    $arr_room  )        ) $arr_room[]         = $room_name;
				if( !in_array( $machine_type, $arr_machine_type ) ) $arr_machine_type[] = $machine_type;
			}

			// ログデータ
			if( $this->filter ) $data = $this->apply_filter( $data );
			$this->params[ 'log_data' ] = $data;

			$this->params[ self::ROOM         ] = $arr_room;
			$this->params[ self::MACHINE_TYPE ] = $arr_machine_type;

		}

		// 日付リスト
		$this->params[ 'date' ]     = $this->get_date_list();

		return $this->params;

	}
	
	// 日付リスト取得関数
	private function get_date_list(){

		// ファイルリスト取得
		if ( $dir = opendir( ERR_LOG_DIR ) ) {

			while ( ($file = readdir( $dir ) ) !== false ) {
		
				if ( $file != "." && $file != ".." && $file != ".svn" ) {
					$arr_logfile[] = $file;
				}
			}

			closedir($dir);
		}

		foreach( $arr_logfile as $value ){

			$date_str   = str_replace( array( 'error_', '.log' ), array( '', '' ), $value );
			$date_obj   = DateTime::createFromFormat( 'Ymd', $date_str );
			$arr_date[] = $date_obj->format( 'Y-m-d' );
		}

		// 日付順に並べ替え
		sort( $arr_date );

		return $arr_date;
	}

	// 教室名取得関数
	private function get_room_name( $room_id ){

		$ref_pdo = new Ref_Pdo_Management();
		return $ref_pdo->ref_room_name( array( $room_id ) );
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

	// 機器モデル名取得
	private function get_machine_model_name( $machine_type ){
	
		$ref_pdo = new Ref_Pdo_Management();
		return $ref_pdo->ref_machine_type( array( $machine_type ) );
	}
}
?>
