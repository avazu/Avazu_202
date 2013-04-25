<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: summary_overview
 *  - Time: March 18, 2011, 3:39 am
 */


class SummaryOverview_DAO {
	const _coll = 'summary_overview';

	/**
	 * func #160
	 * -used in /tracking202/redirect/dl.php(12987)
	/tracking202/static/record_simple.php(12915)
	 * -SELECT COUNT(*)
	FROM 202_summary_overview
	WHERE user_id = 'vv.user_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	AND ppc_account_id = 'vv.ppc_account_id'
	AND click_time = 'vv.click_time'
	 *
	 * count by values
	 */
	public static function count_by($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$click_time = $_values['click_time'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$ppc_account_id = $_values['ppc_account_id'];

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               'click_time' => $click_time,
		               'ppc_account_id' => $ppc_account_id,
		               'user_id' => $user_id);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #172
	 * -used in /tracking202/redirect/off.php(10942)
	 * -SELECT COUNT(*)
	FROM 202_summary_overview
	WHERE user_id = 'vv.user_id'
	AND landing_page_id = 'vv.landing_page_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	AND click_time = 'vv.click_time'
	 *
	 * count by1 values
	 */
	public static function count_by1($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$landing_page_id = $_values['landing_page_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$click_time = $_values['click_time'];

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               'click_time' => $click_time,
		               'landing_page_id' => $landing_page_id,
		               'user_id' => $user_id);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #220
	 * -used in /tracking202/static/record_adv.php(10803)
	 * -SELECT COUNT(*)
	FROM 202_summary_overview
	WHERE user_id = 'vv.user_id'
	AND landing_page_id = 'vv.landing_page_id'
	AND ppc_account_id = 'vv.ppc_account_id'
	AND click_time = 'vv.click_time'
	 *
	 * count by2 values
	 */
	public static function count_by2($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$landing_page_id = $_values['landing_page_id'];
		$ppc_account_id = $_values['ppc_account_id'];
		$click_time = $_values['click_time'];

		// query criteria
		$query = array('click_time' => $click_time,
		               'landing_page_id' => $landing_page_id,
		               'ppc_account_id' => $ppc_account_id,
		               'user_id' => $user_id);

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #161
	 * -used in /tracking202/redirect/dl.php(13515)
	/tracking202/static/record_simple.php(13441)
	 * -INSERT INTO 202_summary_overview
	SET user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', ppc_account_id = 'vv.ppc_account_id', click_time = 'vv.click_time'
	 *
	 * create by values
	 */
	public static function create_by($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$click_time = $_values['click_time'];
		$ppc_account_id = $_values['ppc_account_id'];

		assert(!isset($_values['landing_page_id']));

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'click_time' => $click_time,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
									'landing_page_id' => 0
		);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #173
	 * -used in /tracking202/redirect/off.php(11468)
	 * -INSERT INTO 202_summary_overview
	SET user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', aff_campaign_id = 'vv.aff_campaign_id', click_time = 'vv.click_time'
	 *
	 * create by1 values
	 */
	public static function create_by1($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$click_time = $_values['click_time'];
		$landing_page_id = $_values['landing_page_id'];

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'click_time' => $click_time,
		              'landing_page_id' => $landing_page_id,
		              'user_id' => $user_id);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #221
	 * -used in /tracking202/static/record_adv.php(11327)
	 * -INSERT INTO 202_summary_overview
	SET user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_time = 'vv.click_time'
	 *
	 * create by2 values
	 */
	public static function create_by2($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$click_time = $_values['click_time'];
		$landing_page_id = $_values['landing_page_id'];
		$ppc_account_id = $_values['ppc_account_id'];

		// object to be created
		$data = array('click_time' => $click_time,
		              'landing_page_id' => $landing_page_id,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #91
	 * -used in /tracking202/ajax/account_overview.php(1694)
	/tracking202/ajax/account_overview.php(19751)
	/tracking202/ajax/account_overview.php(12300)
	 * -SELECT 202_aff_campaigns.aff_campaign_id, aff_campaign_name, aff_campaign_payout, aff_network_name
	FROM 202_summary_overview
	LEFT JOIN 202_aff_campaigns USING (aff_campaign_id)
	LEFT JOIN 202_aff_networks USING(aff_network_id)
	WHERE 202_aff_networks.user_id = 'vv.user_id'
	AND 202_aff_networks.aff_network_deleted = 0
	AND 202_aff_campaigns.aff_campaign_deleted = 0
	AND 202_summary_overview.click_time >= 'vv.from'
	AND 202_summary_overview.click_time < 'vv.to'
	AND landing_page_id = 0
	GROUP BY aff_campaign_id
	ORDER BY 202_aff_networks.aff_network_name ASC, 202_aff_campaigns.aff_campaign_name ASC
	 *
	 * find by from, to, user id
	 */
	public static function find_aff_campaigns_by($from, $to, $user_id, $landing_page_id = 0) {

		// query criteria
		$query = array(
			'user_id' => $user_id,
			'click_time' => array('$gte' => $from, '$lt' => $to),
			//'aff_campaign_deleted' => array('$ne' => 1),
			//'aff_network_deleted' => array('$ne' => 1),
			'landing_page_id' => $landing_page_id);
		
		$ids = Db::distinct(self::_coll, 'aff_campaign_id', $query);
		DU::dump($ids, __FILE__, __FUNCTION__);

		$query = array('_id' => array('$in' => $ids),
		               'aff_campaign_deleted' => array('$ne' => 1),
		               'aff_network_deleted' => array('$ne' => 1));
		// fields needed
		$fields = array("_id", "aff_campaign_name", "aff_campaign_payout",
		                "aff_network_name");
		$sort = array('aff_network_name' => 1, 'aff_campaign_name' => 1);
		return Db::find(AffCampaigns_DAO::_coll, $query, array('fields' => $fields, 'sort' => $sort));
	}


	/**
	 * func #100
	 * -used in /tracking202/ajax/account_overview.php(30610)
	 * -SELECT 202_landing_pages.landing_page_id, 202_ppc_accounts.ppc_account_id, ppc_account_name, ppc_network_name
	FROM 202_summary_overview
	LEFT JOIN 202_landing_pages ON (202_landing_pages.landing_page_id = 202_summary_overview.landing_page_id)
	LEFT JOIN 202_ppc_accounts ON (202_ppc_accounts.ppc_account_id = 202_summary_overview.ppc_account_id)
	LEFT JOIN 202_ppc_networks USING (ppc_network_id)
	WHERE 202_ppc_networks.ppc_network_deleted = 0
	AND 202_ppc_accounts.ppc_account_deleted = 0
	AND 202_landing_pages.user_id = 'vv.user_id'
	AND 202_landing_pages.landing_page_deleted = 0
	AND 202_summary_overview.click_time >= 'vv.from'
	AND 202_summary_overview.click_time < 'vv.to'
	AND 202_landing_pages.landing_page_id != 0
	AND 202_landing_pages.landing_page_id = 'vv.landing_page_id'
	GROUP BY 202_ppc_accounts.ppc_account_id
	ORDER BY 202_ppc_networks.ppc_network_name ASC, 202_ppc_accounts.ppc_account_name ASC
	 *
	 * find ppc accounts with advanced landing pages
	 */
	public static function find_alp_ppc_accounts_by($from, $to, $user_id, $landing_page_id) {

		// query criteria
		$query = array(
			'user_id' => $user_id,
			'click_time' => array('$gte' => $from, '$lt' => $to),
			'landing_page_id' => $landing_page_id,
			'landing_page_id' => array('$ne' => 0)
			//'ppc_account_deleted' => array('$ne' => 1),
			//'ppc_network_deleted' => array('$ne' => 1),
			//'landing_page_deleted' => array('$ne' => 1),
		);

		//todo check this
		$lp = LandingPages_DAO::get($landing_page_id);
		assert($lp['landing_page_deleted'] == 0);
		
		$ids = Db::distinct(self::_coll, 'ppc_account_id', $query);

		$query = array('_id' => array('$in' => $ids),
		               'ppc_account_deleted' => array('$ne' => 1),
		               'ppc_network_deleted' => array('$ne' => 1));
		// fields needed
		$fields = array("_id", "ppc_account_name", "ppc_network_name"); //"landing_page_id",
		$sort = array('ppc_network_name' => 1, 'ppc_account_name' => 1);
		return Db::find(PpcAccounts_DAO::_coll, $query, array('fields' => $fields, 'sort' => $sort));
	}

	/**
	 * func #99
	 * -used in /tracking202/ajax/account_overview.php(29515)
	 * -SELECT aff_campaign_id, 202_ppc_accounts.ppc_account_id, ppc_account_name, ppc_network_name
	FROM 202_summary_overview
	LEFT JOIN 202_ppc_accounts ON (202_ppc_accounts.ppc_account_id = 202_summary_overview.ppc_account_id)
	LEFT JOIN 202_ppc_networks USING (ppc_network_id)
	WHERE 202_ppc_networks.ppc_network_deleted = 0
	AND 202_ppc_accounts.ppc_account_deleted = 0
	AND 202_summary_overview.aff_campaign_id = 'vv.aff_campaign_id'
	AND 202_summary_overview.click_time >= 'vv.from'
	AND 202_summary_overview.click_time < 'vv.to'
	GROUP BY ppc_account_id
	ORDER BY 202_ppc_networks.ppc_network_name ASC, 202_ppc_accounts.ppc_account_name ASC
	 *
	 * find by aff campaign id, from, to
	 */
	public static function find_nomal_ppc_accounts_by_aff_campaign_id_and_from_and_to($from, $to, $aff_campaign_id) {


		// query criteria
		$query = array(
			'aff_campaign_id' => $aff_campaign_id,
			'click_time' => array('$gte' => $from, '$lt' => $to),
			//'ppc_account_deleted' => array('$ne' => 1),
			//'ppc_network_deleted' => array('$ne' => 1)
		);
		$ids = Db::distinct(self::_coll, 'ppc_account_id', $query);

		$query = array('_id' => array('$in' => $ids),
		               'ppc_account_deleted' => array('$ne' => 1),
		               'ppc_network_deleted' => array('$ne' => 1));
		// fields needed
		// todo fix not right field - there're much bugs like this, maybe wrap cursor and document is the right way?
		//todo refoctory the account voerview while loop to use map reduce to resolve here problem of group by and id conflict
		//$fields = array("aff_campaign_id", "ppc_account_id", "ppc_account_name", "ppc_network_name");
		$fields = array("_id", "ppc_account_name", "ppc_network_name"); //"aff_campaign_id" 这个是已知项
		$sort = array('ppc_network_name' => 1, 'ppc_account_name' => 1);
		return Db::find(PpcAccounts_DAO::_coll, $query, array('fields' => $fields, 'sort' => $sort));
	}

	/**
	 * func #92
	 * -used in /tracking202/ajax/account_overview.php(2617)
	/tracking202/ajax/account_overview.php(20627)
	 * -SELECT 202_landing_pages.landing_page_id, landing_page_nickname
	FROM 202_summary_overview
	LEFT JOIN 202_landing_pages USING (landing_page_id)
	WHERE 202_landing_pages.user_id = 'vv.user_id'
	AND 202_landing_pages.landing_page_deleted = 0
	AND 202_summary_overview.click_time >= 'vv.from'
	AND 202_summary_overview.click_time < 'vv.to'
	AND 202_landing_pages.landing_page_id != 0
	GROUP BY landing_page_id
	ORDER BY 202_landing_pages.landing_page_nickname ASC
	 *
	 * find by from, to, user id
	 */
	public static function find_landing_pages_by($from, $to, $user_id) {

		// query criteria
		$query = array(
			'user_id' => $user_id,
			'click_time' => array('$gte' => $from, '$lt' => $to),
			//'landing_page_deleted' => array('$ne' => 1),
			'landing_page_id' => array('$ne' => 0)
		);
		$ids = Db::distinct(self::_coll, 'landing_page_id', $query);

		$query = array('_id' => array('$in' => $ids),
		               'landing_page_deleted' => array('$ne' => 1));
		// fields needed
		$fields = array("_id", "landing_page_nickname");
		$sort = array('landing_page_nickname' => 1);

		return Db::find(LandingPages_DAO::_coll, $query, array('fields' => $fields, 'sort' => $sort));
	}


}