<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: ips
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Ips_DAO {
	const _coll = 'ips';

	/**
	 * func #34
	 * -used in /202-config/functions-tracking202.php(84553)
	 * -INSERT INTO 202_ips 
			SET ip_address = 'vv.ip_address', location_id = 'vv.location_id'
	 *
	 * create by ip address, location id 
	 */
	public static function create_by_address_and_location_id($ip_address, $location_id) {
		

		// object to be created
		$data = array('ip_address' => $ip_address,
					'location_id' => $location_id);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #33
	 * -used in /202-config/functions-tracking202.php(83949)
	 * -SELECT ip_id 
			FROM 202_ips 
			WHERE ip_address = 'vv.ip_address'
	 *
	 * find one by ip address 
	 */
	public static function find_one_by_address($ip_address) {
		

		// query criteria
		$query = array('ip_address' => $ip_address);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	public static function get($ip_id) {
		return Db::findOne(self::_coll, $ip_id);
	}

}