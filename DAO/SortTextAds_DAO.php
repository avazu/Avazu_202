<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_text_ads
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SortTextAds_DAO {
	const _coll = 'sort_text_ads';

	/**
	 * func #142
	 * -used in /tracking202/ajax/sort_text_ads.php(5563)
	 * -INSERT INTO 202_sort_text_ads 
			SET user_id = 'vv.user_id', text_ad_id = 'vv.text_ad_id', sort_text_ad_clicks = 'vv.sort_text_ad_clicks', sort_text_ad_leads = 'vv.sort_text_ad_leads', sort_text_ad_su_ratio = 'vv.sort_text_ad_su_ratio', sort_text_ad_payout = 'vv.sort_text_ad_payout', sort_text_ad_epc = 'vv.sort_text_ad_epc', sort_text_ad_avg_cpc = 'vv.sort_text_ad_avg_cpc', sort_text_ad_income = 'vv.sort_text_ad_income', sort_text_ad_cost = 'vv.sort_text_ad_cost', sort_text_ad_net = 'vv.sort_text_ad_net', sort_text_ad_roi = 'vv.sort_text_ad_roi'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		$text_ad_id = $_values['text_ad_id'];
		$sort_text_ad_clicks = $_values['sort_text_ad_clicks'];
		$sort_text_ad_leads = $_values['sort_text_ad_leads'];
		$sort_text_ad_su_ratio = $_values['sort_text_ad_su_ratio'];
		$sort_text_ad_payout = $_values['sort_text_ad_payout'];
		$sort_text_ad_epc = $_values['sort_text_ad_epc'];
		$sort_text_ad_avg_cpc = $_values['sort_text_ad_avg_cpc'];
		$sort_text_ad_income = $_values['sort_text_ad_income'];
		$sort_text_ad_cost = $_values['sort_text_ad_cost'];
		$sort_text_ad_net = $_values['sort_text_ad_net'];
		$sort_text_ad_roi = $_values['sort_text_ad_roi'];

		// object to be created
		$data = array('sort_text_ad_avg_cpc' => $sort_text_ad_avg_cpc,
					'sort_text_ad_clicks' => $sort_text_ad_clicks,
					'sort_text_ad_cost' => $sort_text_ad_cost,
					'sort_text_ad_epc' => $sort_text_ad_epc,
					'sort_text_ad_income' => $sort_text_ad_income,
					'sort_text_ad_leads' => $sort_text_ad_leads,
					'sort_text_ad_net' => $sort_text_ad_net,
					'sort_text_ad_payout' => $sort_text_ad_payout,
					'sort_text_ad_roi' => $sort_text_ad_roi,
					'sort_text_ad_su_ratio' => $sort_text_ad_su_ratio,
					'text_ad_id' => $text_ad_id,
					'user_id' => $user_id);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);
		    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #140
	 * -used in /tracking202/ajax/sort_text_ads.php(1518)
	 * -DELETE 
			FROM 202_sort_text_ads 
			WHERE user_id = 'vv.user_id'
	 *
	 * remove by user id 
	 */
	public static function remove_by_user_id($user_id) {
		

		// query criteria
		$query = array('user_id' => $user_id);
    
		return Db::remove(self::_coll, $query);
	}



}