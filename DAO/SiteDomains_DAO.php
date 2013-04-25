<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: site_domains
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SiteDomains_DAO {
	const _coll = 'site_domains';

	/**
	 * func #37
	 * -used in /202-config/functions-tracking202.php(86348)
	 * -INSERT INTO 202_site_domains 
			SET site_domain_host = 'vv.site_domain_host'
	 *
	 * create by site domain host 
	 */
	public static function create_by_host($site_domain_host) {
		

		// object to be created
		$data = array('site_domain_host' => $site_domain_host);
    
		return Db::insert(self::_coll, $data);
	}

	public static function get($site_domain_id) {

		// query criteria
		$query = array('_id' => $site_domain_id);

		return Db::findOne(self::_coll, $query);
	}

	/**
	 * func #36
	 * -used in /202-config/functions-tracking202.php(85790)
	 * -SELECT site_domain_id 
			FROM 202_site_domains 
			WHERE site_domain_host = 'vv.site_domain_host'
	 *
	 * find one by site domain host 
	 */
	public static function find_one_by_host($site_domain_host) {
		

		// query criteria
		$query = array('site_domain_host' => $site_domain_host);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}



}