<?php

class PROSPER202 {

	function mongodb_version() {
		return 1.8;
	}

	function php_version() {
		global $version;
		$php_version = $version;
		return $php_version;
	}
}


class UPGRADE {


	function upgrade_databases() {

		ini_set('max_execution_time', 60 * 10);
		ini_set('max_input_time', 60 * 10);

		//get the old version
		$mongodb_version = PROSPER202::mongodb_version();
		$php_version = PROSPER202::php_version();

		return true;
	}
}