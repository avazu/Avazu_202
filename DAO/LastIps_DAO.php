<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: last_ips
 *  - Time: March 18, 2011, 3:39 am
 */

 
class LastIps_DAO {
	const _coll = 'last_ips';

	/**
	 * func #31
	 * -used in /202-config/functions-tracking202.php(71350)
	 * -SELECT COUNT(*) AS count 
			FROM 202_last_ips 
			WHERE user_id = 'vv.user_id'
				AND ip_id = 'vv.ip_id'
	 *
	 * count by ip id, user id 
	 */
	public static function count_by_ip_id_and_user_id($ip_id, $user_id) {
		

		// query criteria
		$query = array('ip_id' => $ip_id,
					'user_id' => $user_id);
    
		return Db::count(self::_coll, $query);
	}


	/**
	 * func #32
	 * -used in /202-config/functions-tracking202.php(71850)
	 * -INSERT INTO 202_last_ips 
			SET user_id = 'vv.user_id', ip_id = 'vv.ip_id', time = 'vv.time'
	 *
	 * create by ip id, time, user id 
	 */
	public static function create_by_ip_id_and_time_and_user_id($ip_id, $time, $user_id) {
		

		// object to be created
		$data = array('ip_id' => $ip_id,
					'time' => $time,
					'user_id' => $user_id);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #78
	 * -used in /202-cronjobs/index.php(1779)
	 * -DELETE 
			FROM 202_last_ips 
			WHERE time < '$from'
	 *
	 * remove by from 
	 */
	public static function remove_by_from($from) {
		

		// query criteria
		$query = array('TIME' => array('$lt' => $from));
    
		return Db::remove(self::_coll, $query);
	}



}