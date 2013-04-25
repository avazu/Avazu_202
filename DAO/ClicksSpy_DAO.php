<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: clicks_spy
 *  - Time: March 18, 2011, 3:39 am
 */


class ClicksSpy_DAO {
	const _coll = 'clicks_spy';

	/**
	 * func #155
	 * -used in /tracking202/redirect/dl.php(8006)
	 * -INSERT INTO 202_clicks_spy
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by values
	 */
	public static function create_by($_values) {
		//variables passed 
		$click_id = $_values['click_id'];
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
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
		              'click_id' => $click_id,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #218
	 * -used in /tracking202/static/record_adv.php(7281)
	 * -INSERT INTO 202_clicks_spy
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by1 values
	 */
	public static function create_by1($_values) {
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
		              'click_id' => $click_id,
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
	 * func #224
	 * -used in /tracking202/static/record_simple.php(7916)
	 * -INSERT INTO 202_clicks_spy
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by2 values
	 */
	public static function create_by2($_values) {
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
		              'click_id' => $click_id,
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
	 * func #77
	 * -used in /202-cronjobs/index.php(1577)
	 * -DELETE
	FROM 202_clicks_spy
	WHERE click_time < '$from'
	 *
	 * remove by from
	 */
	public static function remove_by_from($from) {


		// query criteria
		$query = array('click_time' => array('$lt' => $from));

		return Db::remove(self::_coll, $query);
	}


	/**
	 * func #231
	 * -used in /tracking202/update/upload.php(4779)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '1', click_filtered = '0', click_payout = 'vv.click_payout'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, click payout, user id
	 */
	public static function update_by_click_id_and_click_payout_and_user_id($click_id, $click_payout, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 1,
		                              'click_payout' => $click_payout));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #227
	 * -used in /tracking202/update/delete-subids.php(1128)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '0', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, user id
	 */
	public static function update_by_click_id_and_user_id($click_id, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 0));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #229
	 * -used in /tracking202/update/subids.php(1268)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '1', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, user id
	 */
	public static function update_by_click_id_and_user_id1($click_id, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 1));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #206
	 * -used in /tracking202/static/gpb.php(889)
	/tracking202/static/gpx.php(2102)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '1', click_filtered = '0'
	 *
	 * zinvalid update
	 */
	public static function delay_update_click_filtered($click_id, $use_pixel_payout, $click_payout) {

		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$sets = array('click_filtered' => 0,
		              'click_lead' => 1);
		if ($use_pixel_payout == 1) {
			$sets['click_payout'] = $click_payout;
		}

		$data = array('$set' => $sets);

		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #210
	 * -used in /tracking202/static/pb.php(856)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '1', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * zinvalid update by aff campaign id, click id
	 */
	public static function delay_update_click_filtered_by_id_and_aff_campaign_id($click_id, $aff_campaign_id) {

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               '_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 1));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


	/**
	 * func #213
	 * -used in /tracking202/static/px.php(1710)
	 * -UPDATE 202_clicks_spy
	SET click_lead = '1', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	 *
	 * zinvalid update by click id
	 */
	public static function delay_update_click_filtered_by_id($click_id) {
		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 1));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


}