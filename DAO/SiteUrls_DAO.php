<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: site_urls
 *  - Time: March 18, 2011, 3:39 am
 */


class SiteUrls_DAO {
	const _coll = 'site_urls';

	/**
	 * func #35
	 * -used in /202-config/functions-tracking202.php(85133)
	 * -INSERT INTO 202_site_urls
	SET site_domain_id = 'vv.site_domain_id', site_url_address = 'vv.site_url_address'
	 *
	 * create by site url address, site domain id
	 */
	//todo deprecated by upsert
	public static function create_by_address_and_site_domain_id($site_url_address, $site_domain_id) {

		DU::dump($site_domain_id);
		//反规范化
		$site_domain = SiteDomains_DAO::get($site_domain_id);
		DU::dump($site_domain);
		$site_domain_host = $site_domain['site_domain_host'];

		// object to be created
		$data = array('site_url_address' => $site_url_address,
		              'site_domain_id' => $site_domain_id,
		              'site_domain_host' => $site_domain_host
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * @static 代替 func #35
	 * @param  $site_url_address
	 * @param  $site_domain_id
	 * @return bool
	 */
	public static function upsert_by_address_and_site_domain_id($site_url_address, $site_domain_id) {

		DU::dump($site_domain_id);
		//反规范化
		$site_domain = SiteDomains_DAO::get($site_domain_id);
		DU::dump($site_domain);
		$site_domain_host = $site_domain['site_domain_host'];

		// object to be updated
		$data = array('site_url_address' => $site_url_address,
		              'site_domain_id' => $site_domain_id,
		              'site_domain_host' => $site_domain_host
		);

		// query criteria
		$query = array('site_url_address' => $site_url_address);

		$doc = Db::findOne(self::_coll, $query);
		if($doc) return $doc;

		return Db::insert(self::_coll, $data);
		//return Db::upsert(self::_coll, $query, $data);
	}

	/**
	 * func #176 111 112 113 114
	 * -SELECT site_url_address
	FROM 202_site_urls
	WHERE site_url_id = 'vv.site_url_id'
	 *
	 * get by idsite url id
	 */
	public static function get($site_url_id) {

		// query criteria
		$query = array('_id' => NameUtil::empty_to_0($site_url_id));

		return Db::findOne(self::_coll, $query);
	}


}