<?php
class ExPDO extends PDO{

	private $status;

	public function __construct(){

		try {

			$conf = Spyc::YAMLLoad( ROOT_DIR . 'config/env.yaml' );
			parent::__construct(
				"mysql:dbname={$conf[ 'dbname' ]};" .
				"host={$conf[ 'host' ]};",
				$conf[ 'user' ],
				$conf[ 'passwd' ]
			);

			// utf8文字化け対策
			$ps = $this->prepare( 'SET NAMES utf8;' );
			$ps->execute();

			$this->status = array( 	Env::CODE		=> true,
									Env::MESSAGE	=> 'init' );

		} catch( PDOException $e) {

			$this->status = array( 	Env::CODE		=> false,
									Env::MESSAGE	=> $e->getMessage() );
		}
	}

	public function getStatus(){ return $this->status; }
}


?>
