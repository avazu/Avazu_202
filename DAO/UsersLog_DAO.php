<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: users_log
 *  - Time: March 18, 2011, 3:39 am
 */

 
class UsersLog_DAO {
	const _coll = 'users_log';

	/**
	 * func #84
	 * -used in /202-login.php(1376)
	 * -INSERT INTO 202_users_log 
			SET user_name = 'vv.user_name', user_pass = 'vv.user_pass', ip_address = 'vv.ip_address', login_time = 'vv.login_time', login_success = 'vv.login_success', login_error = 'vv.login_error', login_server = 'vv.login_server', login_session = 'vv.login_session'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_name = $_values['user_name'];
		$user_pass = $_values['user_pass'];
		$ip_address = $_values['ip_address'];
		$login_time = $_values['login_time'];
		$login_success = $_values['login_success'];
		$login_error = $_values['login_error'];
		$login_server = $_values['login_server'];
		$login_session = $_values['login_session'];

		// object to be created
		$data = array('ip_address' => $ip_address,
					'login_error' => $login_error,
					'login_server' => $login_server,
					'login_session' => $login_session,
					'login_success' => $login_success,
					'login_time' => $login_time,
					'user_name' => $user_name,
					'user_pass' => $user_pass);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #13
	 * -used in /202-account/administration.php(4453)
	 * -SELECT * 
			FROM 202_users_log 
			ORDER BY login_id DESC 
			LIMIT 50
	 *
	 * find 
	 */
	public static function find() {
		

		// query criteria
		$query = array();

		// options for query
		$sort = array('login_id' => -1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort, 'limit' => 50));
	}



}