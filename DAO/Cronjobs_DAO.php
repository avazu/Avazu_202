<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: cronjobs
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Cronjobs_DAO {
	const _coll = 'cronjobs';

	/**
	 * func #75
	 * -used in /202-cronjobs/index.php(728)
				/202-cronjobs/index.php(3060)
				/202-cronjobs/index.php(4897)
	 * -SELECT COUNT(*) 
			FROM 202_cronjobs 
			WHERE cronjob_type = 'vv.cronjob_type'
				AND cronjob_time = 'vv.cronjob_time'
	 *
	 * count by cronjob time, cronjob type 
	 */
	public static function count_by_time_and_type($cronjob_time, $cronjob_type) {

		// query criteria
		$query = array('cronjob_time' => $cronjob_time,
					'cronjob_type' => $cronjob_type);
    
		return Db::count(self::_coll, $query);
	}


	/**
	 * func #76
	 * -used in /202-cronjobs/index.php(1063)
				/202-cronjobs/index.php(3399)
				/202-cronjobs/index.php(5265)
	 * -INSERT INTO 202_cronjobs 
			SET cronjob_type = 'vv.cronjob_type', cronjob_time = 'vv.cronjob_time'
	 *
	 * create by cronjob time, cronjob type 
	 */
	public static function create_by_time_and_type($cronjob_time, $cronjob_type) {
		

		// object to be created
		$data = array('cronjob_time' => $cronjob_time,
					'cronjob_type' => $cronjob_type);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #80
	 * -used in /202-cronjobs/index.php(2276)
	 * -DELETE 
			FROM 202_cronjobs 
			WHERE cronjob_time < 'vv.cronjob_time'
	 *
	 * remove by cronjob time 
	 */
	public static function remove_by_time($cronjob_time) {
		

		// query criteria
		$query = array('cronjob_time' => array('$lt' => $cronjob_time));
    
		return Db::remove(self::_coll, $query);
	}



}