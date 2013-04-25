<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: users
 *  - Time: March 18, 2011, 3:39 am
 */


class Users_DAO {
	const _coll = 'users';

	/**
	 * func #64
	 * -used in /202-config/functions.php(606)
	 * -SELECT COUNT(*)
	FROM 202_users
	 *
	 * count
	 */
	public static function count() {

		return Db::count(self::_coll);
	}


	/**
	 * func #4
	 * -used in /202-account/account.php(2314)
	 * -SELECT COUNT(*)
	FROM 202_users
	WHERE user_email = 'vv.user_email'
	AND user_id != 'vv.user_id'
	 *
	 * count by user email, user id
	 */
	public static function count_by_email_and_id($user_email, $user_id) {


		// query criteria
		$query = array('user_email' => $user_email,
		               '_id' => array('$ne' => $user_id));

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #30
	 * -used in /202-config/functions-tracking202.php(68680)
	 * -SELECT COUNT(*)
	FROM 202_users
	WHERE user_id = 'vv.user_id'
	AND user_last_login_ip_id = 'vv.ip_id'
	 *
	 * count by user id, ip id
	 */
	public static function count_by_id_and_ip_id($user_id, $ip_id) {


		// query criteria
		$query = array('_id' => $user_id,
		               'user_last_login_ip_id' => $ip_id);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #9
	 * -used in /202-account/account.php(7213)
	 * -SELECT COUNT(*)
	FROM 202_users
	WHERE user_id = 'vv.user_id'
	AND user_pass = 'vv.user_pass'
	 *
	 * count by user id, user pass
	 */
	public static function count_by_id_and_pass($user_id, $user_pass) {


		// query criteria
		$query = array('_id' => $user_id,
		               'user_pass' => $user_pass);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #71
	 * -used in /202-config/install.php(2396)
	 * -INSERT INTO 202_users
	SET user_email = 'vv.user_email', user_name = 'vv.user_name', user_pass = 'vv.user_pass', user_timezone = 'vv.user_timezone', user_time_register = 'vv.user_time_register'
	 *
	 * create by values
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_email = $_values['user_email'];
		$user_name = $_values['user_name'];
		$user_pass = $_values['user_pass'];
		$user_timezone = $_values['user_timezone'];
		$user_time_register = $_values['user_time_register'];

		// object to be created
		$data = array('user_email' => $user_email,
		              'user_name' => $user_name,
		              'user_pass' => $user_pass,
		              'user_time_register' => $user_time_register,
		              'user_timezone' => $user_timezone);
		$data['_id'] = Db::seq(self::_coll);
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #86
	 * -used in /202-lost-pass.php(271)
	 * -SELECT user_id
	FROM 202_users
	WHERE user_name = 'vv.user_name'
	AND user_email = 'vv.user_email'
	 *
	 * find one by user email, user name
	 */
	public static function find_one_by_email_and_name($user_email, $user_name) {


		// query criteria
		$query = array('user_email' => $user_email,
		               'user_name' => $user_name);

		// options for query
		// fields needed
		$fields = array("_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #83
	 * -used in /202-login.php(339)
	 * -SELECT *
	FROM 202_users
	WHERE user_name = 'vv.user_name'
	AND user_pass = 'vv.user_pass'
	 *
	 * find one by user name, user pass
	 */
	public static function find_one_by_name_and_pass($user_name, $user_pass) {

		// query criteria
		$query = array('user_name' => (string)$user_name,
		               'user_pass' => (string)$user_pass);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #88
	 * -used in /202-pass-reset.php(202)
	 * -SELECT *
	FROM 202_users
	WHERE user_pass_key = 'vv.user_pass_key'
	 *
	 * find one by user pass key
	 */
	public static function find_one_by_pass_key($user_pass_key) {


		// query criteria
		$query = array('user_pass_key' => $user_pass_key);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #3
	 * -used in /202-account/account.php(1020)
	 * -SELECT *
	FROM 202_users
	LEFT JOIN 202_users_pref USING (user_id)
	WHERE 202_users.user_id = 'vv.user_id'
	 *
	 * get by id user id
	 */
	public static function get_with_pref($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		$u = Db::findOne(self::_coll, $query);
		$u_pref = UsersPref_DAO::get($user_id);

		return array_merge($u, $u_pref);
	}


	/**
	 * func #23
	 * -used in /202-config/functions-tracking202.php(35084)
	/202-config/functions-tracking202.php(41377)
	 * -SELECT user_time_register
	FROM 202_users
	WHERE user_id = 'vv.user_id'
	 *
	 * get by id1 user id
	 */
	public static function get1($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_time_register");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #27
	 * -used in /202-config/functions-tracking202.php(52229)
	 * -SELECT user_username
	FROM users
	WHERE user_id = 'vv.user_id'
	 *
	 * get by id2 user id
	 */
	public static function get2($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_username");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #50
	 * -used in /202-config/functions-tracking202.php(92171)
	/202-config/functions-tracking202.php(119214)
	 * -SELECT user_time_register, user_pref_breakdown, user_pref_chart, user_pref_show
	FROM 202_users
	LEFT JOIN 202_users_pref USING (user_id)
	WHERE 202_users.user_id = 'vv.user_id'
	 *
	 * get by id3 user id
	 */
	public static function get3($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_pref_breakdown", "user_pref_chart", "user_pref_show", "user_time_register");

		return Db::findOne(UsersPref_DAO::_coll, $query, $fields);
	}


	/**
	 * func #152
	 * -used in /tracking202/redirect/dl.php(2664)
	/tracking202/static/record_adv.php(603)
	/tracking202/static/record_simple.php(1109)
	 * -SELECT user_timezone, user_keyword_searched_or_bidded
	FROM 202_users
	LEFT JOIN 202_users_pref USING (user_id)
	WHERE 202_users.user_id = 'vv.user_id'
	 *
	 * get by id5 user id
	 */
	public static function get5($user_id) {

		// query criteria
		//$query = array('_id' => $user_id);

		// options for query
		// fields needed
		//$fields = array("user_keyword_searched_or_bidded", "user_timezone");
		$u_pref = UsersPref_DAO::get_user_keyword_searched_or_bidded($user_id);
		$u = self::get6($user_id);
		//echo "user_id=$user_id   ";
		//DU::dump($u);
		//DU::dump($u_pref);
		return array_merge($u, $u_pref);
	}


	/**
	 * func #171
	 * -used in /tracking202/redirect/off.php(10366)
	 * -SELECT user_timezone
	FROM 202_users
	WHERE user_id = 'vv.user_id'
	 *
	 * get by id6 user id
	 */
	public static function get6($user_id) {

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_timezone");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #7
	 * -used in /202-account/account.php(4770)
	 * -UPDATE 202_users
	SET user_api_key = 'vv.user_api_key'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user api key, user id
	 */
	public static function update_by_api_key_and_id($user_api_key, $user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_api_key' => $user_api_key));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #5
	 * -used in /202-account/account.php(3449)
	 * -UPDATE 202_users
	SET user_email = 'vv.user_email', user_timezone = 'vv.user_timezone'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user email, user id, user timezone
	 */
	public static function update_by_email_and_id_and_timezone($user_email, $user_id, $user_timezone) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_email' => $user_email,
		                              'user_timezone' => $user_timezone));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #1
	 * -used in /202-account/account.php(291)
	 * -UPDATE 202_users
	SET user_stats202_app_key = ''
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id
	 */
	public static function update_by_id($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_stats202_app_key' => ''));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #2
	 * -used in /202-account/account.php(701)
	 * -UPDATE 202_users
	SET user_api_key = ''
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id
	 */
	public static function update_by_id1($user_id) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_api_key' => ''));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #10
	 * -used in /202-account/account.php(7827)
	 * -UPDATE 202_users
	SET user_pass = 'vv.user_pass'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id, user pass
	 */
	public static function update_by_id_and_pass($user_id, $user_pass) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_pass' => $user_pass));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #89
	 * -used in /202-pass-reset.php(1997)
	 * -UPDATE 202_users
	SET user_pass = 'vv.user_pass', user_pass_time = '0'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id, user pass
	 */
	public static function update_by_id_and_pass1($user_id, $user_pass) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_pass' => $user_pass,
		                              'user_pass_time' => 0));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #87
	 * -used in /202-lost-pass.php(1174)
	 * -UPDATE 202_users
	SET user_pass_key = 'vv.user_pass_key', user_pass_time = 'vv.user_pass_time'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id, user pass key, user pass time
	 */
	public static function update_by_id_and_pass_key_and_pass_time($user_id, $user_pass_key, $user_pass_time) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_pass_key' => $user_pass_key,
		                              'user_pass_time' => $user_pass_time));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #8
	 * -used in /202-account/account.php(5694)
	 * -UPDATE 202_users
	SET user_stats202_app_key = 'vv.user_stats202_app_key'
	WHERE user_id = 'vv.user_id'
	 *
	 * update by user id, user stats202 app key
	 */
	public static function update_by_id_and_stats202_app_key($user_id, $user_stats202_app_key) {


		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('user_stats202_app_key' => $user_stats202_app_key));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #85
	 * -used in /202-login.php(2153)
	 * -UPDATE 202_users
	SET user_last_login_ip_id = 'vv.ip_id'
	WHERE user_name = 'vv.user_name'
	AND user_pass = 'vv.user_pass'
	 *
	 * update by user name, user pass, ip id
	 */
	public static function update_by_name_and_pass_and_ip_id($user_name, $user_pass, $ip_id) {


		// query criteria
		$query = array('user_name' => $user_name,
		               'user_pass' => $user_pass);

		// object to be updated
		$data = array('$set' => array('user_last_login_ip_id' => $ip_id));

		return Db::updateOne(self::_coll, $query, $data);
	}


}