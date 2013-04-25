<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: tracking_c2
 *  - Time: March 18, 2011, 3:39 am
 */

 
class TrackingC2_DAO {
	const _coll = 'tracking_c2';

	/**
	 * func #43
	 * -used in /202-config/functions-tracking202.php(88813)
	 * -INSERT INTO 202_tracking_c2 
			SET c2 = 'vv.c2'
	 *
	 * create by c2 
	 */
	public static function create_by_c2($c2) {
		

		// object to be created
		$data = array('c2' => $c2);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #42
	 * -used in /202-config/functions-tracking202.php(88439)
	 * -SELECT c2_id 
			FROM 202_tracking_c2 
			WHERE c2 = 'vv.c2'
	 *
	 * find one by c2 
	 */
	public static function find_one_by_c2($c2) {
		

		// query criteria
		$query = array('c2' => $c2);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	public static function get_name($c_id) {
		$c = Db::findOne(self::_coll, $c_id);
		return $c['c2'];
	}

}