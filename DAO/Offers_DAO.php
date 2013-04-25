<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: offers
 *  - Time: March 18, 2011, 3:39 am
 */


class Offers_DAO {
	const _coll = 'offers';

	/**
	 * func #17
	 * -used in /202-account/ajax/mark-all-offers.php(792)
	 * -INSERT INTO 202_offers
	SET user_id = 'vv.user_id', offer_id = 'vv.offer_id'
	 *
	 * create by offer id, user id
	 */
	public static function create_by_id_and_user_id($offer_id, $user_id) {


		// object to be created
		$data = array('_id' => $offer_id,
		              'offer_seen' => 1,
		              'user_id' => $user_id);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #16
	 * -used in /202-account/ajax/check-for-offers.php(625)
	/202-account/ajax/mark-all-offers.php(554)
	 * -SELECT *
	FROM 202_offers
	WHERE user_id = 'vv.user_id'
	AND offer_id = 'vv.offer_id'
	 *
	 * find one by offer id, user id
	 */
	public static function find_one_by_id_and_user_id($offer_id, $user_id) {


		// query criteria
		$query = array('_id' => $offer_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}


}