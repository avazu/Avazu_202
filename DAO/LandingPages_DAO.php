<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: landing_pages
 *  - Time: March 18, 2011, 3:39 am
 */


class LandingPages_DAO {
	const _coll = 'landing_pages';

	/**
	 * func #191
	 * -used in /tracking202/setup/landing_pages.php(18004)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE aff_campaign_id = 'vv.aff_campaign_id'
	AND landing_page_deleted = '0'
	AND landing_page_type = '0'
	 *
	 * find by aff campaign id
	 */
	public static function find_by_aff_campaign_id($aff_campaign_id) {


		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               'landing_page_deleted' => array('$ne' => 1),
		               'landing_page_type' => 0);

		return Db::find(self::_coll, $query);
	}


	/**
	 * func #120
	 * -used in /tracking202/ajax/landing_pages.php(239)
	/tracking202/ajax/landing_pages.php(1418)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE user_id = 'vv.user_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	AND landing_page_deleted = '0'
	ORDER BY aff_campaign_id , landing_page_nickname ASC
	 *
	 * zinvalid find by aff campaign id, user id
	 */
	public static function find_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id) {


		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               'landing_page_deleted' => array('$ne' => 1),
		               'user_id' => $user_id);

		// options for query
		$sort = array('aff_campaign_id' => 1, 'landing_page_nickname' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #186
	 * -used in /tracking202/setup/landing_pages.php(2342)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE user_id = 'vv.user_id'
	AND landing_page_id = 'vv.landing_page_id'
	 *
	 * find by landing page id, user id
	 */
	public static function find_by_id_and_user_id($landing_page_id, $user_id) {


		// query criteria
		$query = array('_id' => $landing_page_id,
		               'user_id' => $user_id);

		return Db::find(self::_coll, $query);
	}


	/**
	 * func #121
	 * -used in /tracking202/ajax/landing_pages.php(804)
	/tracking202/ajax/landing_pages.php(1797)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE user_id = 'vv.user_id'
	AND landing_page_type = '1'
	AND landing_page_deleted = '0'
	ORDER BY aff_campaign_id , landing_page_nickname ASC
	 *
	 * zinvalid find by user id
	 */
	public static function find_by_user_id($user_id) {


		// query criteria
		$query = array('landing_page_deleted' => array('$ne' => 1),
		               'landing_page_type' => 1,
		               'user_id' => $user_id);

		// options for query
		$sort = array('aff_campaign_id' => 1, 'landing_page_nickname' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #185
	 * -used in /tracking202/setup/get_adv_landing_code.php(804)
	/tracking202/setup/landing_pages.php(15082)
	/tracking202/setup/text_ads.php(18346)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE user_id = 'vv.user_id'
	AND landing_page_type = '1'
	AND landing_page_deleted = '0'
	 *
	 * find by user id
	 */
	public static function find_by_user_id1($user_id) {


		// query criteria
		$query = array('landing_page_deleted' => array('$ne' => 1),
		               'landing_page_type' => 1,
		               'user_id' => $user_id);

		return Db::find(self::_coll, $query);
	}


	/**
	 * func #146
	 * -used in /tracking202/ajax/update_cpc.php(3967)
	/tracking202/ajax/update_cpc2.php(4251)
	/tracking202/setup/landing_pages.php(6770)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE landing_page_id = 'vv.landing_page_id'
	AND user_id = 'vv.user_id'
	 *
	 * find one by landing page id, user id
	 */
	public static function find_one_by_id_and_user_id($landing_page_id, $user_id) {


		// query criteria
		$query = array('_id' => $landing_page_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #162 166
	 * -used in /tracking202/redirect/lp.php(1198)
	 * -SELECT 202_landing_pages.user_id, 202_landing_pages.landing_page_id, 202_landing_pages.landing_page_id_public, 202_landing_pages.aff_campaign_id, 202_aff_campaigns.aff_campaign_rotate, 202_aff_campaigns.aff_campaign_url, 202_aff_campaigns.aff_campaign_url_2, 202_aff_campaigns.aff_campaign_url_3, 202_aff_campaigns.aff_campaign_url_4, 202_aff_campaigns.aff_campaign_url_5, 202_aff_campaigns.aff_campaign_payout, 202_aff_campaigns.aff_campaign_cloaking
	FROM 202_landing_pages, 202_aff_campaigns
	WHERE 202_landing_pages.landing_page_id_public = 'vv.landing_page_id_public'
	AND 202_aff_campaigns.aff_campaign_id = 202_landing_pages.aff_campaign_id
	 *
	 * find one by landing page id public
	 */
	public static function find_one_with_aff_campaign_by_id_public($landing_page_id_public) {
		$lp = self::find_one_with_user_and_aff_c_id_by_id_public($landing_page_id_public);
		$aff_c_id = $lp['aff_campaign_id'];

//		// query criteria
//		$query = array('202_aff_campaigns.aff_campaign_id' => aff_campaign_id);
//
//		// options for query
//		// fields needed
//		$fields = array("aff_campaign_cloaking", "aff_campaign_id", "aff_campaign_payout", "aff_campaign_rotate", "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3", "aff_campaign_url_4", "aff_campaign_url_5", "_id", "landing_page_id_public", "user_id");

		$aff_c = AffCampaigns_DAO::get($aff_c_id);
		//$aff_c['aff_campaign_id'] = $aff_c['_id'];
		//unset($aff_c['_id']); //check if conflict
		
		return array_merge($lp, $aff_c);
	}


	/**
	 * func #166, #222
	 * -used in /tracking202/redirect/lpc.php(166)
	 * -SELECT aff_campaign_name, aff_campaign_rotate, aff_campaign_url, aff_campaign_url_2, aff_campaign_url_3, aff_campaign_url_4, aff_campaign_url_5
	FROM 202_landing_pages
	LEFT JOIN 202_aff_campaigns USING (aff_campaign_id)
	WHERE landing_page_id_public = 'vv.landing_page_id_public'
	 *
	 * find one by landing page id public
	 */
	public static function find_aff_campaign_by_id_public($landing_page_id_public) {

		$lp = self::find_one_with_user_and_aff_c_id_by_id_public($landing_page_id_public);
		$aff_c_id = $lp['aff_campaign_id'];

		// query criteria
		$query = array('aff_campaign_id' => $aff_c_id);
		// options for query
		// fields needed
		$fields = array("aff_campaign_name", "aff_campaign_rotate",
		                "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3",
		                "aff_campaign_url_4", "aff_campaign_url_5");

		return Db::findOne(AffCampaigns_DAO::_coll, $query, $fields);
	}


	/**
	 * func #214
	 * -used in /tracking202/static/record.php(312)
	 * -SELECT landing_page_type
	FROM 202_landing_pages
	WHERE landing_page_id_public = 'vv.landing_page_id_public'
	 *
	 * find one by landing page id public
	 */
	public static function find_one_by_id_public2($landing_page_id_public) {


		// query criteria
		$query = array('landing_page_id_public' => $landing_page_id_public);

		// options for query
		// fields needed
		$fields = array("landing_page_type");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #215
	 * -used in /tracking202/static/record_adv.php(204)
	 * -SELECT 202_landing_pages.user_id, 202_landing_pages.landing_page_id
	FROM 202_landing_pages
	WHERE 202_landing_pages.landing_page_id_public = 'vv.landing_page_id_public'
	 *
	 * find one by landing page id public
	 */
	public static function find_one_with_user_and_aff_c_id_by_id_public($landing_page_id_public) {


		// query criteria
		$query = array('landing_page_id_public' => $landing_page_id_public);

		// options for query
		// fields needed
		$fields = array("_id", "user_id", 'aff_campaign_id');

		return Db::findOne(self::_coll, $query, $fields);
	}


//	/**
//	 * func #222
//	 * -used in /tracking202/static/record_simple.php(204)
//	 * -SELECT 202_landing_pages.user_id, 202_landing_pages.landing_page_id, 202_landing_pages.aff_campaign_id, 202_aff_campaigns.aff_campaign_url, 202_aff_campaigns.aff_campaign_url_2, 202_aff_campaigns.aff_campaign_url_3, 202_aff_campaigns.aff_campaign_url_4, 202_aff_campaigns.aff_campaign_url_5, 202_aff_campaigns.aff_campaign_payout, 202_aff_campaigns.aff_campaign_cloaking, 202_aff_campaigns.aff_campaign_rotate
//	FROM 202_landing_pages, 202_aff_campaigns
//	WHERE 202_landing_pages.landing_page_id_public = 'vv.landing_page_id_public'
//	AND 202_aff_campaigns.aff_campaign_id = 202_landing_pages.aff_campaign_id
//	 *
//	 * find one by landing page id public
//	 */
//	public static function find_one_with_aff_campaign_by_id_public($landing_page_id_public) {
//
//
//		// query criteria
//		$query = array('202_aff_campaigns.aff_campaign_id' => aff_campaign_id,
//		               '202_landing_pages.landing_page_id_public' => $landing_page_id_public);
//
//		// options for query
//		// fields needed
//		$fields = array("aff_campaign_cloaking", "aff_campaign_id", "aff_campaign_payout", "aff_campaign_rotate", "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3", "aff_campaign_url_4", "aff_campaign_url_5", "_id", "user_id");
//
//		return Db::findOne(self::_coll, $query, $fields);
//	}


	/**
	 * func #115
	 * -used in /tracking202/ajax/generate_tracking_link.php(2841)
	/tracking202/ajax/get_adv_landing_code.php(885)
	/tracking202/ajax/get_landing_code.php(1407)
	 * -SELECT *
	FROM 202_landing_pages
	WHERE landing_page_id = 'vv.landing_page_id'
	 *
	 * get by idlanding page id
	 */
	public static function get($landing_page_id) {


		// query criteria
		$query = array('_id' => $landing_page_id);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #187
	 * -used in /tracking202/setup/landing_pages.php(4166)
	 * -UPDATE 202_landing_pages
	SET aff_campaign_id = 'vv.aff_campaign_id', landing_page_nickname = 'vv.landing_page_nickname', landing_page_url = 'vv.landing_page_url', landing_page_type = 'vv.landing_page_type', user_id = 'vv.user_id', landing_page_time = 'vv.landing_page_time WHERE'landing_page_id = 'vv.landing_page_id'
	 *
	 * update or created by values
	 */
	public static function upsert_by($_values) {
		//variables passed
		$aff_campaign_id = $_values['aff_campaign_id'];
		$landing_page_nickname = $_values['landing_page_nickname'];
		$landing_page_url = $_values['landing_page_url'];
		$landing_page_type = $_values['landing_page_type'];
		$user_id = $_values['user_id'];
		$landing_page_time = $_values['landing_page_time'];
		//$landing_page_id = $_values['landing_page_id'];

		// object to be updated
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'landing_page_nickname' => $landing_page_nickname,
		              'landing_page_time' => $landing_page_time,
		              'landing_page_type' => $landing_page_type, //0
		              'landing_page_url' => $landing_page_url,
		              'user_id' => $user_id);

		$_id = -1;
		if (isset($_values['landing_page_id'])) {
			$_id = $_values['landing_page_id'];
			unset($_values['landing_page_id']);
		}
		if ($_id < 0) {
			$_id = Db::seq(self::_coll);
			$data['landing_page_deleted'] = 0;
		}
		$data['_id'] = $_id;

		// query criteria
		//$query = array('_id', $landing_page_id);

		return Db::upsertById(self::_coll, $data);
	}


	/**
	 * func #188
	 * -used in /tracking202/setup/landing_pages.php(5496)
	 * -UPDATE 202_landing_pages
	SET landing_page_id_public = 'vv.landing_page_id_public'
	WHERE landing_page_id = 'vv.landing_page_id'
	 *
	 * update by landing page id, landing page id public
	 */
	public static function update_by_id_and_id_public($landing_page_id, $landing_page_id_public) {


		// query criteria
		$query = array('_id' => $landing_page_id);

		// object to be updated
		$data = array('$set' => array('landing_page_id_public' => $landing_page_id_public));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #189
	 * -used in /tracking202/setup/landing_pages.php(6095)
	 * -UPDATE 202_landing_pages
	SET landing_page_deleted = '1', landing_page_time = 'vv.landing_page_time'
	WHERE user_id = 'vv.user_id'
	AND landing_page_id = 'vv.landing_page_id'
	 *
	 * update by landing page id, landing page time, user id
	 */
	public static function update_by_id_and_time_and_user_id($landing_page_id, $landing_page_time, $user_id) {


		// query criteria
		$query = array('_id' => $landing_page_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('landing_page_deleted' => 1,
		                              'landing_page_time' => $landing_page_time));

		return Db::updateOne(self::_coll, $query, $data);
	}


}