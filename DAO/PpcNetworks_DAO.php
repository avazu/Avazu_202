<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: ppc_networks
 *  - Time: March 18, 2011, 3:39 am
 */


class PpcNetworks_DAO {
	const _coll = 'ppc_networks';

	/**
	 * func #193
	 * -used in /tracking202/setup/ppc_accounts.php(1738)
	 * -SELECT COUNT(*)
	FROM 202_ppc_networks
	WHERE user_id = 'vv.user_id'
	AND ppc_network_id = 'vv.ppc_network_id'
	 *
	 * count by ppc network id, user id
	 */
	public static function count_by_id_and_user_id($ppc_network_id, $user_id) {

		// query criteria
		$query = array('_id' => (int)$ppc_network_id,
		               'user_id' => (int)$user_id);
		//				$query = array('_id' => 1,
		//		               'user_id' => 1);
		return Db::count(self::_coll, $query);
	}


	/**
	 * func #192
	 * -used in /tracking202/setup/ppc_accounts.php(672)
	 * -INSERT INTO 202_ppc_networks
	SET user_id = 'vv.user_id', ppc_network_name = 'vv.ppc_network_name', ppc_network_time = 'vv.ppc_network_time'
	 *
	 * create by ppc network name, ppc network time, user id
	 */
	public static function create_by_name_and_time_and_user_id($ppc_network_name, $ppc_network_time, $user_id) {

		// object to be created
		$data = array('ppc_network_name' => $ppc_network_name,
		              'ppc_network_time' => $ppc_network_time,
		              'user_id' => $user_id,
		              'ppc_network_deleted' => 0
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #198
	 * -used in /tracking202/setup/ppc_accounts.php(9161)
	/tracking202/setup/ppc_accounts.php(11130)
	 * -SELECT *
	FROM 202_ppc_networks
	WHERE user_id = 'vv.user_id'
	AND ppc_network_deleted = '0'
	ORDER BY ppc_network_name ASC
	 *
	 * find by user id
	 */
	public static function find_not_deleted_by_user_id($user_id) {


		// query criteria
		$query = array('ppc_network_deleted' => array('$ne' => 1), //0,
		               'user_id' => $user_id);

		// options for query
		$sort = array('ppc_network_name' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #147
	 * -used in /tracking202/ajax/update_cpc.php(4853)
	/tracking202/ajax/update_cpc2.php(5137)
	 * -SELECT *
	FROM 202_ppc_networks
	WHERE ppc_network_id = 'vv.ppc_network_id'
	AND user_id = 'vv.user_id'
	 *
	 * find one by ppc network id, user id
	 */
	public static function find_one_by_id_and_user_id($ppc_network_id, $user_id) {


		// query criteria
		$query = array('_id' => $ppc_network_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}


	public static function get($ppc_network_id) {

		// query criteria
		$query = array('_id' => $ppc_network_id);

		return Db::findOne(self::_coll, $query);
	}

	/**
	 * func #196
	 * -used in /tracking202/setup/ppc_accounts.php(4786)
	 * -UPDATE 202_ppc_networks
	SET ppc_network_deleted = '1', ppc_network_time = 'vv.ppc_network_time'
	WHERE user_id = 'vv.user_id'
	AND ppc_network_id = 'vv.ppc_network_id'
	 *
	 * update by ppc network id, ppc network time, user id
	 */
	public static function delete_by_id_and_user_id($ppc_network_id, $user_id) {

		// query criteria
		$query = array('_id' => $ppc_network_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('ppc_network_deleted' => 1,
		                              'ppc_network_time' => time()));
		if (Db::update(self::_coll, $query, $data)) {
			return PpcAccounts_DAO::mark_ppc_network_deleted($ppc_network_id);
		} else {
			return false;
		}
	}


}