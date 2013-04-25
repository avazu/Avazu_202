<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: aff_networks
 *  - Time: March 18, 2011, 3:39 am
 */


class AffNetworks_DAO {
	const _coll = 'aff_networks';

	/**
	 * func #177
	 * -used in /tracking202/setup/aff_campaigns.php(1501)
	 * -SELECT *
	FROM 202_aff_networks
	WHERE user_id = 'vv.user_id'
	AND aff_network_id = 'vv.aff_network_id'
	 *
	 * find by aff network id, user id
	 */
	public static function find_by_id_and_user_id($aff_network_id, $user_id) {


		// query criteria
		$query = array('_id' => $aff_network_id,
		               'user_id' => $user_id);

		return Db::find(self::_coll, $query);
	}

	/**
	 * func #183
	 * -used in /tracking202/setup/aff_campaigns.php(12138)
	/tracking202/setup/aff_campaigns.php(18464)
	/tracking202/setup/landing_pages.php(16087)
	/tracking202/setup/text_ads.php(20056)
	 * -SELECT *
	FROM 202_aff_networks
	WHERE user_id = 'vv.user_id'
	AND aff_network_deleted = '0'
	ORDER BY aff_network_name ASC
	 *
	 * find by user id
	 */
	public static function find_not_deleted_by_user_id($user_id) {


		// query criteria
		$query = array('aff_network_deleted' => array('$ne' => 1),
		               'user_id' => $user_id);

		// options for query
		$sort = array('aff_network_name' => 1);
		//DU::dump($query);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #144
	 * -used in /tracking202/ajax/update_cpc.php(1068)
	/tracking202/ajax/update_cpc2.php(1237)
	 * -SELECT *
	FROM 202_aff_networks
	WHERE aff_network_id = 'vv.aff_network_id'
	AND user_id = 'vv.user_id'
	 *
	 * find one by aff network id, user id
	 */
	public static function find_one_by_id_and_user_id($aff_network_id, $user_id) {


		// query criteria
		$query = array('_id' => $aff_network_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #190
	 * -used in /tracking202/setup/landing_pages.php(9035)
	/tracking202/setup/text_ads.php(10166)
	 * -SELECT *
	FROM 202_aff_networks
	WHERE aff_network_id = 'vv.aff_network_id'
	 *
	 * get by idaff network id
	 */
	public static function get($aff_network_id) {


		// query criteria
		$query = array('_id' => $aff_network_id);

		return Db::findOne(self::_coll, $query);
	}


	public static function delete_by_id_and_user_id($aff_network_id, $user_id) {

		// query criteria
		$query = array('_id' => $aff_network_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('aff_network_deleted' => 1,
		                              'aff_network_time' => time()));

		if (Db::updateOne(self::_coll, $query, $data)) {
			return AffCampaigns_DAO::mark_aff_campaign_deleted($aff_network_id);
		} else {
			return false;
		}
	}


	public static function create_by_name_and_user_id($aff_network_name, $user_id) {

		// object to be created
		$data = array('aff_network_name' => $aff_network_name,
		              'aff_network_time' => time(),
		              'user_id' => $user_id,
		              'aff_network_deleted' => 0);

		return Db::insert(self::_coll, $data);
	}

}