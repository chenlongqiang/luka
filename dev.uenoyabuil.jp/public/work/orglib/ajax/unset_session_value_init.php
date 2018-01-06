<?php

class init{
	
	const HUSH        = 'hush';
	const NEWLEC      = 'newlec';
	const UPDATE      = 'update';
	const COPY        = 'copy';
	const LEC_PRESETS = 'lec_presets';
	const PRESET_ID   = 'preset_id';
	const RESERVATION = 'reservation';
	const ROOMS       = 'rooms';
	const VALUE       = 'value';
	private $params;

	public function __construct( $params ){

		$this->params = $params;
		
		if( isset( $this->params[ POST ][ init::HUSH ] ) ){
			$this->params[ init::HUSH ] = $this->params[ POST ][ init::HUSH ];
		}
		if( isset( $this->params[ POST ][ init::VALUE ] ) ){
			$this->params[ init::VALUE ] = $this->params[ POST ][ init::VALUE ];
		}
	}

	public function set_params(){

		if( isset( $this->params[ init::HUSH ] ) ){

			if( $this->params[ init::HUSH ] === init::NEWLEC || $this->params[ init::HUSH ] === init::UPDATE || $this->params[ init::HUSH ] === init::COPY ) {

				unset( $_SESSION[ $this->params[ init::HUSH ] ] );
				$this->params[ 'status' ] = '予約情報を消去しました。';

			} else if( $this->params[ init::HUSH ] === init::LEC_PRESETS ){

				unset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] );
				$this->params[ 'status' ] = '講義形態プリセット情報を消去しました。';

			} else if( $this->params[ init::HUSH ] === init::PRESET_ID ){
				
				if( $this->params[ init::VALUE ] ){

					foreach( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ] as $key => $value ){
						if( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $key ][ 'lec_style_preset_id' ] == $this->params[ init::VALUE ] ){
							unset( $_SESSION[ init::NEWLEC ][ init::LEC_PRESETS ][ $key ] );
							break;
						}
					}
				}
				$this->params[ 'status' ] = '講義形態プリセット情報を一件消去しました。';

			} else if( $this->params[ init::HUSH ] === init::ROOMS ){

				$_SESSION[ init::NEWLEC ][ init::RESERVATION ][ init::ROOMS ] = '';
				$this->params[ 'status' ] = '教室リスト情報を消去しました。';

			}
		}

		return $this->params;

	}
}
?>
