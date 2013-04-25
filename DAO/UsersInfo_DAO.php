<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: users_info
 *  - Time: March 18, 2011, 3:39 am
 */

 
class UsersInfo_DAO {
	const _coll = 'users_info';

	/**
	 * func #19
	 * -used in /202-config/functions-tracking202.php(1123)
	 * -SELECT user_id 
			FROM users_info 
			WHERE user_email = 'vv.email_from'
	 *
	 * find one by email from 
	 */
	public static function find_one_by_email_from($email_from) {
		

		// query criteria
		$query = array('user_email' => $email_from);

		// options for query
		// fields needed
		$fields = array("user_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #20
	 * -used in /202-config/functions-tracking202.php(1482)
	 * -SELECT user_id 
			FROM users_info 
			WHERE user_email = 'vv.email_to'
	 *
	 * find one by email to 
	 */
	public static function find_one_by_email_to($email_to) {
		

		// query criteria
		$query = array('user_email' => $email_to);

		// options for query
		// fields needed
		$fields = array("user_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}



}