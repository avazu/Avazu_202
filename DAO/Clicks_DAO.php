<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: clicks_advance
 *  - Time: March 18, 2011, 3:39 am
 *
 * capped collection
 * 快速记录第一时间的点击数据，用于点击数据的临时存储
 * 随后被转移到clicks advance collection中进行以后的长期存储和使用
 */


class Clicks_DAO {
	const _coll = 'clicks';


	/**
	 * func #154
	 * -used in /tracking202/redirect/dl.php(7334)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_alp = 'vv.click_alp', click_filtered = 'vv.click_filtered', click_time = 'vv.click_time'
	 *
	 * create by values
	 */
	public static function create_for_dl_by($_values) {
		//variables passed
		$click_id = $_values['click_id'];
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$ppc_account_id = $_values['ppc_account_id'];
		$click_cpc = $_values['click_cpc'];
		$click_payout = $_values['click_payout'];
		$click_alp = $_values['click_alp'];
		$click_filtered = $_values['click_filtered'];
		$click_time = $_values['click_time'];

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              '_id' => $click_id,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #217
	 * -used in /tracking202/static/record_adv.php(6611)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by1 values
	 */
	public static function create_for_adv_by($_values) {
		//variables passed
		$click_id = $_values['click_id'];
		$user_id = $_values['user_id'];
		$landing_page_id = $_values['landing_page_id'];
		$ppc_account_id = $_values['ppc_account_id'];
		$click_cpc = $_values['click_cpc'];
		$click_payout = $_values['click_payout'];
		$click_filtered = $_values['click_filtered'];
		$click_alp = $_values['click_alp'];
		$click_time = $_values['click_time'];

		// object to be created
		$data = array('click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              '_id' => $click_id,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'landing_page_id' => $landing_page_id,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #223
	 * -used in /tracking202/static/record_simple.php(7183)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by2 values
	 */
	public static function create_for_simple_by($_values) {
		//variables passed
		$click_id = $_values['click_id'];
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$landing_page_id = $_values['landing_page_id'];
		$ppc_account_id = $_values['ppc_account_id'];
		$click_cpc = $_values['click_cpc'];
		$click_payout = $_values['click_payout'];
		$click_filtered = $_values['click_filtered'];
		$click_alp = $_values['click_alp'];
		$click_time = $_values['click_time'];

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              '_id' => $click_id,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'landing_page_id' => $landing_page_id,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		return Db::insert(self::_coll, $data);
	}




}