<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_ips
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SortIps_DAO {
	const _coll = 'sort_ips';

	/**
	 * func #129
	 * -used in /tracking202/ajax/sort_ips.php(5886)
	 * -INSERT INTO 202_sort_ips 
			SET user_id = 'vv.user_id', ip_id = 'vv.ip_id', sort_ip_clicks = 'vv.sort_ip_clicks', sort_ip_leads = 'vv.sort_ip_leads', sort_ip_su_ratio = 'vv.sort_ip_su_ratio', sort_ip_payout = 'vv.sort_ip_payout', sort_ip_epc = 'vv.sort_ip_epc', sort_ip_avg_cpc = 'vv.sort_ip_avg_cpc', sort_ip_income = 'vv.sort_ip_income', sort_ip_cost = 'vv.sort_ip_cost', sort_ip_net = 'vv.sort_ip_net', sort_ip_roi = 'vv.sort_ip_roi'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		$ip_id = $_values['ip_id'];
		$sort_ip_clicks = $_values['sort_ip_clicks'];
		$sort_ip_leads = $_values['sort_ip_leads'];
		$sort_ip_su_ratio = $_values['sort_ip_su_ratio'];
		$sort_ip_payout = $_values['sort_ip_payout'];
		$sort_ip_epc = $_values['sort_ip_epc'];
		$sort_ip_avg_cpc = $_values['sort_ip_avg_cpc'];
		$sort_ip_income = $_values['sort_ip_income'];
		$sort_ip_cost = $_values['sort_ip_cost'];
		$sort_ip_net = $_values['sort_ip_net'];
		$sort_ip_roi = $_values['sort_ip_roi'];

		// object to be created
		$data = array('ip_id' => $ip_id,
					'sort_ip_avg_cpc' => $sort_ip_avg_cpc,
					'sort_ip_clicks' => $sort_ip_clicks,
					'sort_ip_cost' => $sort_ip_cost,
					'sort_ip_epc' => $sort_ip_epc,
					'sort_ip_income' => $sort_ip_income,
					'sort_ip_leads' => $sort_ip_leads,
					'sort_ip_net' => $sort_ip_net,
					'sort_ip_payout' => $sort_ip_payout,
					'sort_ip_roi' => $sort_ip_roi,
					'sort_ip_su_ratio' => $sort_ip_su_ratio,
					'user_id' => $user_id);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);
		
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #126
	 * -used in /tracking202/ajax/sort_ips.php(1513)
	 * -DELETE 
			FROM 202_sort_ips 
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