<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: clicks_counter
 *  - Time: March 18, 2011, 3:39 am
 */


class ClicksCounter_DAO {
	const _coll = 'clicks_counter';

	/**
	 * func #153
	 * -used in /tracking202/redirect/dl.php(6848)
	/tracking202/static/record_adv.php(5996)
	/tracking202/static/record_simple.php(6568)
	 * -INSERT INTO 202_clicks_counter
	SET click_id = DEFAULT
	 *
	 * create
	 */
	public static function getNextId() {

		return Db::seq(self::_coll);
	}

}