<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: locations_block
 *  - Time: March 18, 2011, 3:39 am
 */

 
class LocationsBlock_DAO {
	const _coll = 'locations_block';

	/**
	 * func #66
	 * -used in /202-config/functions.php(5230)
	 * -SELECT COUNT(*) 
			FROM 202_locations_block
	 *
	 * count 
	 */
	public static function count() {
		

		// query criteria
		$query = array();
    
		return Db::count(self::_coll, $query);
	}


	/**
	 * func #48
	 * -used in /202-config/functions-tracking202.php(90786)
	 * -SELECT location_id 
			FROM 202_locations_block 
			WHERE location_block_ip_from >= 'vv.ip_address'
				AND location_block_ip_to <= 'vv.ip_address'
	 *
	 * find one by ip address, ip address 
	 */
	public static function find_one_by_ip_address_and_ip_address($ip_address, $ip_address) {
		

		// query criteria
		$query = array('location_block_ip_from' => array('$gte' => $ip_address),
					'location_block_ip_to' => array('$lte' => $ip_address));

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}



}