<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: tracking_c4
 *  - Time: March 18, 2011, 3:39 am
 */

 
class TrackingC4_DAO {
	const _coll = 'tracking_c4';

	/**
	 * func #47
	 * -used in /202-config/functions-tracking202.php(90344)
	 * -INSERT INTO 202_tracking_c4 
			SET c4 = 'vv.c4'
	 *
	 * create by c4 
	 */
	public static function create_by_c4($c4) {
		

		// object to be created
		$data = array('c4' => $c4);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #46
	 * -used in /202-config/functions-tracking202.php(89970)
	 * -SELECT c4_id 
			FROM 202_tracking_c4 
			WHERE c4 = 'vv.c4'
	 *
	 * find one by c4 
	 */
	public static function find_one_by_c4($c4) {
		

		// query criteria
		$query = array('c4' => $c4);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	public static function get_name($c_id) {
		$c = Db::findOne(self::_coll, $c_id);
		return $c['c4'];
	}

}