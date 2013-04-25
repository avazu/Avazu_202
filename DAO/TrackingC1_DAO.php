<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: tracking_c1
 *  - Time: March 18, 2011, 3:39 am
 */


class TrackingC1_DAO {
	const _coll = 'tracking_c1';

	/**
	 * func #41
	 * -used in /202-config/functions-tracking202.php(88066)
	 * -INSERT INTO 202_tracking_c1
	SET c1 = 'vv.c1'
	 *
	 * create by c1
	 */
	public static function create_by_c1($c1) {


		// object to be created
		$data = array('c1' => $c1);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #40
	 * -used in /202-config/functions-tracking202.php(87692)
	 * -SELECT c1_id
	FROM 202_tracking_c1
	WHERE c1 = 'vv.c1'
	 *
	 * find one by c1
	 */
	public static function find_one_by_c1($c1) {


		// query criteria
		$query = array('c1' => $c1);

		// options for query
		// fields needed
		$fields = array("_id");

		return Db::findOne(self::_coll, $query, $fields);
	}

	public static function get_name($c_id) {
		$c = Db::findOne(self::_coll, $c_id);
		return $c['c1'];
	}


}