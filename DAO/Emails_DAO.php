<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: emails
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Emails_DAO {
	const _coll = 'emails';

	/**
	 * func #21
	 * -used in /202-config/functions-tracking202.php(2377)
	 * -INSERT INTO emails 
			SET email_to_user_id = 'vv.email_to_user_id', email_from_user_id = 'vv.email_from_user_id', email_to = 'vv.email_to', email_from = 'vv.email_from', ip_id = 'vv.ip_id', email_time = 'vv.email_time', email_subject = 'vv.email_subject', email_message = 'vv.email_message', email_type_id = 'vv.email_type_id', site_url_id = 'vv.site_url_id'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$email_to_user_id = $_values['email_to_user_id'];
		$email_from_user_id = $_values['email_from_user_id'];
		$email_to = $_values['email_to'];
		$email_from = $_values['email_from'];
		$ip_id = $_values['ip_id'];
		$email_time = $_values['email_time'];
		$email_subject = $_values['email_subject'];
		$email_message = $_values['email_message'];
		$email_type_id = $_values['email_type_id'];
		$site_url_id = $_values['site_url_id'];

		// object to be created
		$data = array('email_from' => $email_from,
					'email_from_user_id' => $email_from_user_id,
					'email_message' => $email_message,
					'email_subject' => $email_subject,
					'email_time' => $email_time,
					'email_to' => $email_to,
					'email_to_user_id' => $email_to_user_id,
					'email_type_id' => $email_type_id,
					'ip_id' => $ip_id,
					'site_url_id' => $site_url_id);
    
		return Db::insert(self::_coll, $data);
	}



}