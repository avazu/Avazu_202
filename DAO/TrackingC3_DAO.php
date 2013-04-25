<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: tracking_c3
 *  - Time: March 18, 2011, 3:39 am
 */

 
class TrackingC3_DAO {
	const _coll = 'tracking_c3';

	/**
	 * func #45
	 * -used in /202-config/functions-tracking202.php(89560)
	 * -INSERT INTO 202_tracking_c3 
			SET c3 = 'vv.c3'
	 *
	 * create by c3 
	 */
	public static function create_by_c3($c3) {
		

		// object to be created
		$data = array('c3' => $c3);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #44
	 * -used in /202-config/functions-tracking202.php(89186)
	 * -SELECT c3_id 
			FROM 202_tracking_c3 
			WHERE c3 = 'vv.c3'
	 *
	 * find one by c3 
	 */
	public static function find_one_by_c3($c3) {
		

		// query criteria
		$query = array('c3' => $c3);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	public static function get_name($c_id) {
		$c = Db::findOne(self::_coll, $c_id);
		return $c['c3'];
	}

}