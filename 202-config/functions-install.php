<?php

class INSTALL {

	function install_databases() {

		$php_version = PROSPER202::php_version();

		Db::insert('202_version',
		           array(
		                array('version' => $php_version)
		           ));

		//Db::drop('browsers');
		Db::batchInsert('browsers',
		                array(
		                     array('_id' => 1, 'browser_name' => 'Internet Explorer'),
		                     array('_id' => 2, 'browser_name' => 'Firefox'),
		                     array('_id' => 3, 'browser_name' => 'Konqueror'),
		                     array('_id' => 4, 'browser_name' => 'Netscape'),
		                     array('_id' => 5, 'browser_name' => 'OmniWeb'),
		                     array('_id' => 6, 'browser_name' => 'Opera'),
		                     array('_id' => 7, 'browser_name' => 'Safari'),
		                     array('_id' => 8, 'browser_name' => 'AOL'),
		                     array('_id' => 9, 'browser_name' => 'Chrome'),
		                     array('_id' => 10, 'browser_name' => 'Mobile'),
		                     array('_id' => 11, 'browser_name' => 'Console')
		                ));


		//Db::drop('platforms');
		Db::batchInsert('platforms',
		                array(
		                     array('_id' => 1, 'platform_name' => 'Windows'),
		                     array('_id' => 2, 'platform_name' => 'Macintosh'),
		                     array('_id' => 3, 'platform_name' => 'Linux'),
		                     array('_id' => 4, 'platform_name' => 'OS/2'),
		                     array('_id' => 5, 'platform_name' => 'BeOS')
		                ));

		//index

	}
} 