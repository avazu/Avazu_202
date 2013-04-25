<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_landing_pages
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SortLandingPages_DAO {
	const _coll = 'sort_landing_pages';

	/**
	 * func #136
	 * -used in /tracking202/ajax/sort_landing_pages.php(6825)
	 * -INSERT INTO 202_sort_landing_pages 
			SET user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', sort_landing_page_clicks = 'vv.sort_landing_page_clicks', sort_landing_page_click_throughs = 'vv.sort_landing_page_click_throughs', sort_landing_page_ctr = 'vv.sort_landing_page_ctr', sort_landing_page_leads = 'vv.sort_landing_page_leads', sort_landing_page_su_ratio = 'vv.sort_landing_page_su_ratio', sort_landing_page_payout = 'vv.sort_landing_page_payout', sort_landing_page_epc = 'vv.sort_landing_page_epc', sort_landing_page_avg_cpc = 'vv.sort_landing_page_avg_cpc', sort_landing_page_income = 'vv.sort_landing_page_income', sort_landing_page_cost = 'vv.sort_landing_page_cost', sort_landing_page_net = 'vv.sort_landing_page_net', sort_landing_page_roi = 'vv.sort_landing_page_roi'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		$landing_page_id = $_values['landing_page_id'];
		$sort_landing_page_clicks = $_values['sort_landing_page_clicks'];
		$sort_landing_page_click_throughs = $_values['sort_landing_page_click_throughs'];
		$sort_landing_page_ctr = $_values['sort_landing_page_ctr'];
		$sort_landing_page_leads = $_values['sort_landing_page_leads'];
		$sort_landing_page_su_ratio = $_values['sort_landing_page_su_ratio'];
		$sort_landing_page_payout = $_values['sort_landing_page_payout'];
		$sort_landing_page_epc = $_values['sort_landing_page_epc'];
		$sort_landing_page_avg_cpc = $_values['sort_landing_page_avg_cpc'];
		$sort_landing_page_income = $_values['sort_landing_page_income'];
		$sort_landing_page_cost = $_values['sort_landing_page_cost'];
		$sort_landing_page_net = $_values['sort_landing_page_net'];
		$sort_landing_page_roi = $_values['sort_landing_page_roi'];

		// object to be created
		$data = array('landing_page_id' => $landing_page_id,
					'sort_landing_page_avg_cpc' => $sort_landing_page_avg_cpc,
					'sort_landing_page_click_throughs' => $sort_landing_page_click_throughs,
					'sort_landing_page_clicks' => $sort_landing_page_clicks,
					'sort_landing_page_cost' => $sort_landing_page_cost,
					'sort_landing_page_ctr' => $sort_landing_page_ctr,
					'sort_landing_page_epc' => $sort_landing_page_epc,
					'sort_landing_page_income' => $sort_landing_page_income,
					'sort_landing_page_leads' => $sort_landing_page_leads,
					'sort_landing_page_net' => $sort_landing_page_net,
					'sort_landing_page_payout' => $sort_landing_page_payout,
					'sort_landing_page_roi' => $sort_landing_page_roi,
					'sort_landing_page_su_ratio' => $sort_landing_page_su_ratio,
					'user_id' => $user_id);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #133
	 * -used in /tracking202/ajax/sort_landing_pages.php(1565)
	 * -DELETE 
			FROM 202_sort_landing_pages 
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