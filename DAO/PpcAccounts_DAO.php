<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: ppc_accounts
 *  - Time: March 18, 2011, 3:39 am
 */


class PpcAccounts_DAO {
	const _coll = 'ppc_accounts';

	/**
	 * func #194
	 * -used in /tracking202/setup/ppc_accounts.php(2463)
	 * -SELECT COUNT(*)
	FROM 202_ppc_accounts
	WHERE user_id = 'vv.user_id'
	AND ppc_account_id = 'vv.ppc_account_id'
	 *
	 * count by ppc account id, user id
	 */
	public static function count_by_id_and_user_id($ppc_account_id, $user_id) {


		// query criteria
		$query = array('_id' => $ppc_account_id,
		               'user_id' => $user_id);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #199
	 * -used in /tracking202/setup/ppc_accounts.php(12279)
	 * -SELECT *
	FROM 202_ppc_accounts
	WHERE ppc_network_id = 'vv.ppc_network_id'
	AND ppc_account_deleted = '0'
	ORDER BY ppc_account_name ASC
	 *
	 * find by ppc network id
	 */
	public static function find_by_ppc_network_id($ppc_network_id) {


		// query criteria
		$query = array('ppc_account_deleted' => array('$ne' => 1),
		               'ppc_network_id' => $ppc_network_id);

		// options for query
		$sort = array('ppc_account_name' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #122
	 * -used in /tracking202/ajax/ppc_accounts.php(251)
	 * -SELECT *
	FROM 202_ppc_accounts
	WHERE user_id = 'vv.user_id'
	AND ppc_network_id = 'vv.ppc_network_id'
	AND ppc_account_deleted = '0'
	ORDER BY ppc_account_name ASC
	 *
	 * find by ppc network id, user id
	 */
	public static function find_by_ppc_network_id_and_user_id($ppc_network_id, $user_id) {

		// query criteria
		$query = array('ppc_account_deleted' => 0,
		               'ppc_network_id' => $ppc_network_id,
		               'user_id' => $user_id);

		// options for query
		$sort = array('ppc_account_name' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #148
	 * -used in /tracking202/ajax/update_cpc.php(5624)
	/tracking202/ajax/update_cpc2.php(5908)
	/tracking202/setup/ppc_accounts.php(5966)
	 * -SELECT *
	FROM 202_ppc_accounts
	WHERE ppc_account_id = 'vv.ppc_account_id'
	AND user_id = 'vv.user_id'
	 *
	 * find one by ppc account id, user id
	 */
	public static function find_one_by_id_and_user_id($ppc_account_id, $user_id) {

		// query criteria
		$query = array('_id' => $ppc_account_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}

	public static function get($ppc_account_id) {

		// query criteria
		$query = array('_id' => $ppc_account_id);

		return Db::findOne(self::_coll, $query);
	}

	/**
	 * func #195
	 * -used in /tracking202/setup/ppc_accounts.php(3274)
	 * -UPDATE 202_ppc_accounts
	SET ppc_account_name = 'vv.ppc_account_name', ppc_network_id = 'vv.ppc_network_id', user_id = 'vv.user_id', ppc_account_time = 'vv.ppc_account_time'
	WHERE ppc_account_id = 'vv.ppc_account_id'
	 *
	 * update or created by values
	 */
	public static function upsert_by($_values) {
		//variables passed
		$ppc_account_name = $_values['ppc_account_name'];
		$user_id = $_values['user_id'];
		$ppc_account_time = $_values['ppc_account_time'];
		//$ppc_account_id = $_values['ppc_account_id'];
		$ppc_network_id = $_values['ppc_network_id'];

		//反规范化
		$ppc_network = PpcNetworks_DAO::get($ppc_network_id);
		$ppc_network_name = $ppc_network['ppc_network_name'];
		$ppc_network_deleted = $ppc_network['ppc_network_deleted'];

		// object to be updated
		$data = array(
			'user_id' => $user_id,
			'ppc_account_name' => $ppc_account_name,
			'ppc_account_time' => $ppc_account_time,
			'ppc_network_id' => $ppc_network_id,
			'ppc_network_name' => $ppc_network_name,
			'ppc_network_deleted' => $ppc_network_deleted
		);

		$_id = -1;
		if (isset($_values['ppc_account_id'])) {
			$_id = $_values['ppc_account_id'];
			unset($_values['ppc_account_id']);
		}
		if ($_id < 0) {
			$_id = Db::seq(self::_coll);
			$data['ppc_account_deleted'] = 0;
		}
		$data['_id'] = $_id;

		// query criteria
		//$query = array('_id', $_id);

		return Db::upsertById(self::_coll, $data);
	}


	// if ppc net is deleted, then here
	public static function mark_ppc_network_deleted($ppc_network_id) {

		// query criteria
		$query = array('ppc_network_id' => $ppc_network_id);

		// object to be updated
		$data = array('$set' => array('ppc_network_deleted' => 1)); //,'ppc_network_time' => time()));

		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #197
	 * -used in /tracking202/setup/ppc_accounts.php(5412)
	 * -UPDATE 202_ppc_accounts
	SET ppc_account_deleted = '1', ppc_account_time = 'vv.ppc_account_time'
	WHERE user_id = 'vv.user_id'
	AND ppc_account_id = 'vv.ppc_account_id'
	 *
	 * update by ppc account id, ppc account time, user id
	 */
	public static function update_by_id_and_time_and_user_id($ppc_account_id, $ppc_account_time, $user_id) {


		// query criteria
		$query = array('_id' => $ppc_account_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('ppc_account_deleted' => 1,
		                              'ppc_account_time' => $ppc_account_time));

		return Db::updateOne(self::_coll, $query, $data);
	}




}