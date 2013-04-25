<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_keywords
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SortKeywords_DAO {
	const _coll = 'sort_keywords';

	/**
	 * func #132
	 * -used in /tracking202/ajax/sort_keywords.php(5728)
	 * -INSERT INTO 202_sort_keywords 
			SET user_id = 'vv.user_id', keyword_id = 'vv.keyword_id', sort_keyword_clicks = 'vv.sort_keyword_clicks', sort_keyword_leads = 'vv.sort_keyword_leads', sort_keyword_su_ratio = 'vv.sort_keyword_su_ratio', sort_keyword_payout = 'vv.sort_keyword_payout', sort_keyword_epc = 'vv.sort_keyword_epc', sort_keyword_avg_cpc = 'vv.sort_keyword_avg_cpc', sort_keyword_income = 'vv.sort_keyword_income', sort_keyword_cost = 'vv.sort_keyword_cost', sort_keyword_net = 'vv.sort_keyword_net', sort_keyword_roi = 'vv.sort_keyword_roi'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		$keyword_id = $_values['keyword_id'];
		$sort_keyword_clicks = $_values['sort_keyword_clicks'];
		$sort_keyword_leads = $_values['sort_keyword_leads'];
		$sort_keyword_su_ratio = $_values['sort_keyword_su_ratio'];
		$sort_keyword_payout = $_values['sort_keyword_payout'];
		$sort_keyword_epc = $_values['sort_keyword_epc'];
		$sort_keyword_avg_cpc = $_values['sort_keyword_avg_cpc'];
		$sort_keyword_income = $_values['sort_keyword_income'];
		$sort_keyword_cost = $_values['sort_keyword_cost'];
		$sort_keyword_net = $_values['sort_keyword_net'];
		$sort_keyword_roi = $_values['sort_keyword_roi'];

		// object to be created
		$data = array('keyword_id' => $keyword_id,
					'sort_keyword_avg_cpc' => $sort_keyword_avg_cpc,
					'sort_keyword_clicks' => $sort_keyword_clicks,
					'sort_keyword_cost' => $sort_keyword_cost,
					'sort_keyword_epc' => $sort_keyword_epc,
					'sort_keyword_income' => $sort_keyword_income,
					'sort_keyword_leads' => $sort_keyword_leads,
					'sort_keyword_net' => $sort_keyword_net,
					'sort_keyword_payout' => $sort_keyword_payout,
					'sort_keyword_roi' => $sort_keyword_roi,
					'sort_keyword_su_ratio' => $sort_keyword_su_ratio,
					'user_id' => $user_id);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);
		    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #130
	 * -used in /tracking202/ajax/sort_keywords.php(1515)
	 * -DELETE 
			FROM 202_sort_keywords 
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