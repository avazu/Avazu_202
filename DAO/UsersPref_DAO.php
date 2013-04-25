<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: users_pref
 *  - Time: March 18, 2011, 3:39 am
 */

 
class UsersPref_DAO {
	const _coll = 'users_pref';

	/**
	 * func #72
	 * -used in /202-config/install.php(2927)
	 * -INSERT INTO 202_users_pref 
			SET user_id = 'vv.user_id'
	 *
	 * create by user id 
	 */
	public static function create_by_user_id($user_id) {
		

		// object to be created
		$data = array('_id' => $user_id,
		'user_pref_limit' => 50,
		'user_pref_time_predefined' => 'today',
		'user_pref_breakdown' => 'day',
		'user_pref_chart' => 'net',
		'user_cpc_or_cpv' => 'cpc',
		'user_keyword_searched_or_bidded' => 'searched',
		'user_tracking_domain' => ''
		);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #11
	 * -used in /202-account/administration.php(2315)
				/202-config/functions-tracking202.php(6399)
				/202-config/functions-tracking202.php(43198)
				/tracking202/ajax/group_overview.php(479)
				/tracking202/overview/group_overview_download.php(479)
	 * -SELECT * 
			FROM 202_users_pref 
			WHERE user_id = 'vv.user_id'
	 *
	 * find one by user id 
	 */
	public static function get($user_id) {
		

		// query criteria
		$query = array('_id' => $user_id);
    
		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #24
	 * -used in /202-config/functions-tracking202.php(38334)
	 * -SELECT user_pref_time_predefined, user_pref_time_from, user_pref_time_to 
			FROM 202_users_pref 
			WHERE user_id = 'vv.user_id'
	 *
	 * find one by user id 
	 */
	public static function find_one_by_user_id1($user_id) {
		

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_pref_time_from", "user_pref_time_predefined", "user_pref_time_to");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #25
	 * -used in /202-config/functions-tracking202.php(42376)
	 * -SELECT user_tracking_domain 
			FROM 202_users_pref 
			WHERE user_id = 'vv.user_id'
	 *
	 * find one by user id 
	 */
	public static function find_one_by_user_id2($user_id) {
		

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_tracking_domain");
    
		return Db::findOne(self::_coll, $query, $fields);
	}

	public static function get_user_keyword_searched_or_bidded($user_id) {

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_keyword_searched_or_bidded");

		$u_pref = Db::findOne(self::_coll, $query, $fields);
		return $u_pref;
	}


	/**
	 * func #90
	 * -used in /tracking202/ajax/account_overview.php(558)
	 * -SELECT user_pref_show, user_cpc_or_cpv 
			FROM 202_users_pref 
			WHERE user_id = 'vv.user_id'
	 *
	 * find one by user id 
	 */
	public static function find_one_by_user_id3($user_id) {
		

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_cpc_or_cpv", "user_pref_show");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #124
	 * -used in /tracking202/ajax/sort_breakdown.php(357)
				/tracking202/ajax/sort_hourly.php(352)
				/tracking202/ajax/sort_ips.php(539)
				/tracking202/ajax/sort_keywords.php(536)
				/tracking202/ajax/sort_landing_pages.php(586)
				/tracking202/ajax/sort_referers.php(539)
				/tracking202/ajax/sort_text_ads.php(539)
				/tracking202/ajax/sort_weekly.php(352)
				/tracking202/analyze/ips_download.php(203)
				/tracking202/analyze/keywords_download.php(203)
				/tracking202/analyze/landing_pages_download.php(234)
				/tracking202/analyze/referers_download.php(203)
				/tracking202/analyze/text_ads_download.php(203)
	 * -SELECT user_pref_breakdown, user_pref_show, user_cpc_or_cpv 
			FROM 202_users_pref 
			WHERE user_id = 'vv.user_id'
	 *
	 * find one by user id 
	 */
	public static function find_one_by_user_id4($user_id) {
		

		// query criteria
		$query = array('_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("user_cpc_or_cpv", "user_pref_breakdown", "user_pref_show");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #123
	 * -used in /tracking202/ajax/set_user_prefs.php(4562)
	 * -UPDATE 202_users_pref 
			SET user_pref_adv = 'vv.user_pref_adv', user_pref_ppc_network_id = 'vv.user_pref_ppc_network_id', user_pref_ppc_account_id = 'vv.user_pref_ppc_account_id', user_pref_aff_network_id = 'vv.user_pref_aff_network_id', user_pref_aff_campaign_id = 'vv.user_pref_aff_campaign_id', user_pref_text_ad_id = 'vv.user_pref_text_ad_id', user_pref_method_of_promotion = 'vv.user_pref_method_of_promotion', user_pref_landing_page_id = 'vv.user_pref_landing_page_id', user_pref_country_id = 'vv.user_pref_country_id', user_pref_ip = 'vv.user_pref_ip', user_pref_referer = 'vv.user_pref_referer', user_pref_keyword = 'vv.user_pref_keyword', user_pref_limit = 'vv.user_pref_limit', user_pref_show = 'vv.user_pref_show', user_pref_breakdown = 'vv.user_pref_breakdown', user_pref_chart = 'vv.user_pref_chart', user_cpc_or_cpv = 'vv.user_cpc_or_cpv', user_pref_time_predefined = 'vv.user_pref_time_predefined', user_pref_time_from = 'vv.user_pref_time_from', user_pref_time_to = 'vv.user_pref_time_to', user_pref_group_1 = 'vv.user_pref_group_1', user_pref_group_2 = 'vv.user_pref_group_2', user_pref_group_3 = 'vv.user_pref_group_3', user_pref_group_4 = 'vv.user_pref_group_4'
			WHERE user_id = 'vv.user_id'
	 *
	 * update by values 
	 */
	public static function update_by($_values) {
		//variables passed 
		$user_pref_adv = $_values['user_pref_adv'];
		$user_pref_ppc_network_id = $_values['user_pref_ppc_network_id'];
		$user_pref_ppc_account_id = $_values['user_pref_ppc_account_id'];
		$user_pref_aff_network_id = $_values['user_pref_aff_network_id'];
		$user_pref_aff_campaign_id = $_values['user_pref_aff_campaign_id'];
		$user_pref_text_ad_id = $_values['user_pref_text_ad_id'];
		$user_pref_method_of_promotion = $_values['user_pref_method_of_promotion'];
		$user_pref_landing_page_id = $_values['user_pref_landing_page_id'];
		$user_pref_country_id = $_values['user_pref_country_id'];
		$user_pref_ip = $_values['user_pref_ip'];
		$user_pref_referer = $_values['user_pref_referer'];
		$user_pref_keyword = $_values['user_pref_keyword'];
		$user_pref_limit = $_values['user_pref_limit'];
		$user_pref_show = $_values['user_pref_show'];
		$user_pref_breakdown = $_values['user_pref_breakdown'];
		$user_pref_chart = $_values['user_pref_chart'];
		$user_cpc_or_cpv = $_values['user_cpc_or_cpv'];
		$user_pref_time_predefined = $_values['user_pref_time_predefined'];
		$user_pref_time_from = $_values['user_pref_time_from'];
		$user_pref_time_to = $_values['user_pref_time_to'];
		$user_pref_group_1 = $_values['user_pref_group_1'];
		$user_pref_group_2 = $_values['user_pref_group_2'];
		$user_pref_group_3 = $_values['user_pref_group_3'];
		$user_pref_group_4 = $_values['user_pref_group_4'];
		$user_id = $_values['user_id'];

		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' =>array('user_cpc_or_cpv' => $user_cpc_or_cpv,
					'user_pref_adv' => $user_pref_adv,
					'user_pref_aff_campaign_id' => $user_pref_aff_campaign_id,
					'user_pref_aff_network_id' => $user_pref_aff_network_id,
					'user_pref_breakdown' => $user_pref_breakdown,
					'user_pref_chart' => $user_pref_chart,
					'user_pref_country_id' => $user_pref_country_id,
					'user_pref_group_1' => $user_pref_group_1,
					'user_pref_group_2' => $user_pref_group_2,
					'user_pref_group_3' => $user_pref_group_3,
					'user_pref_group_4' => $user_pref_group_4,
					'user_pref_ip' => $user_pref_ip,
					'user_pref_keyword' => $user_pref_keyword,
					'user_pref_landing_page_id' => $user_pref_landing_page_id,
					'user_pref_limit' => $user_pref_limit,
					'user_pref_method_of_promotion' => $user_pref_method_of_promotion,
					'user_pref_ppc_account_id' => $user_pref_ppc_account_id,
					'user_pref_ppc_network_id' => $user_pref_ppc_network_id,
					'user_pref_referer' => $user_pref_referer,
					'user_pref_show' => $user_pref_show,
					'user_pref_text_ad_id' => $user_pref_text_ad_id,
					'user_pref_time_from' => $user_pref_time_from,
					'user_pref_time_predefined' => $user_pref_time_predefined,
					'user_pref_time_to' => $user_pref_time_to) );
    
		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #6
	 * -used in /202-account/account.php(3722)
	 * -UPDATE 202_users_pref 
			SET user_keyword_searched_or_bidded = 'vv.user_keyword_searched_or_bidded', user_tracking_domain = 'vv.user_tracking_domain'
			WHERE user_id = 'vv.user_id'
	 *
	 * update by user id, user keyword searched or bidded, user tracking domain 
	 */
	public static function update_by_user_id_and_user_keyword_searched_or_bidded_and_user_tracking_domain($user_id, $user_keyword_searched_or_bidded, $user_tracking_domain) {
		

		// query criteria
		$query = array('_id' => $user_id);

		// object to be updated
		$data = array('$set' =>array('user_keyword_searched_or_bidded' => $user_keyword_searched_or_bidded,
					'user_tracking_domain' => $user_tracking_domain) );
    
		return Db::updateOne(self::_coll, $query, $data);
	}



}