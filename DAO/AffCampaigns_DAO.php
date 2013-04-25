<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: aff_campaigns
 *  - Time: March 18, 2011, 3:39 am
 */


class AffCampaigns_DAO {
	const _coll = 'aff_campaigns';

	/**
	 * func #179
	 * -used in /tracking202/setup/aff_campaigns.php(4972)
	 * -INSERT INTO 202_aff_campaigns
	SET aff_network_id = 'vv.aff_network_id', user_id = 'vv.user_id', aff_campaign_name = 'vv.aff_campaign_name', aff_campaign_url = 'vv.aff_campaign_url', aff_campaign_url_2 = 'vv.aff_campaign_url_2', aff_campaign_url_3 = 'vv.aff_campaign_url_3', aff_campaign_url_4 = 'vv.aff_campaign_url_4', aff_campaign_url_5 = 'vv.aff_campaign_url_5', aff_campaign_rotate = 'vv.aff_campaign_rotate', aff_campaign_payout = 'vv.aff_campaign_payout', aff_campaign_cloaking = 'vv.aff_campaign_cloaking', aff_campaign_time = 'vv.aff_campaign_time'
	 *
	 * update or created by values
	 */
	public static function upsert_by($_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$aff_campaign_name = $_values['aff_campaign_name'];
		$aff_campaign_url = $_values['aff_campaign_url'];
		$aff_campaign_url_2 = $_values['aff_campaign_url_2'];
		$aff_campaign_url_3 = $_values['aff_campaign_url_3'];
		$aff_campaign_url_4 = $_values['aff_campaign_url_4'];
		$aff_campaign_url_5 = $_values['aff_campaign_url_5'];
		$aff_campaign_rotate = $_values['aff_campaign_rotate'];
		$aff_campaign_payout = $_values['aff_campaign_payout'];
		$aff_campaign_cloaking = $_values['aff_campaign_cloaking'];
		$aff_campaign_time = $_values['aff_campaign_time'];
		$aff_network_id = $_values['aff_network_id'];

		//反规范化
		$aff_network = AffNetworks_DAO::get($aff_network_id);
		$aff_network_name = $aff_network['aff_network_name'];
		$aff_network_deleted = $aff_network['aff_network_deleted'];

		// object to be updated
		$data = array('user_id' => $user_id,
		              'aff_campaign_cloaking' => $aff_campaign_cloaking,
		              'aff_campaign_name' => $aff_campaign_name,
		              'aff_campaign_payout' => $aff_campaign_payout,
		              'aff_campaign_rotate' => $aff_campaign_rotate,
		              'aff_campaign_time' => $aff_campaign_time,
		              'aff_campaign_url' => $aff_campaign_url,
		              'aff_campaign_url_2' => $aff_campaign_url_2,
		              'aff_campaign_url_3' => $aff_campaign_url_3,
		              'aff_campaign_url_4' => $aff_campaign_url_4,
		              'aff_campaign_url_5' => $aff_campaign_url_5,
		              'aff_network_id' => $aff_network_id,
		              'aff_network_name' => $aff_network_name,
		              'aff_network_deleted' => $aff_network_deleted
		);

		$_id = -1;
		if (isset($_values['aff_campaign_id'])) {
			$_id = $_values['aff_campaign_id'];
			unset($_values['aff_campaign_id']); //todo fix need do this before each upsert_by_id
		}
		if ($_id < 0) {
			$_id = Db::seq(self::_coll);
			$data['aff_campaign_deleted'] = 0;
		}
		$data['_id'] = $_id;

		// query criteria
		//$query = array('_id', $_id);

		//return Db::upsertById(self::_coll, $data);
		return Db::upsertById(self::_coll, $data);
	}


	// if aff net is deleted, then here
	public static function mark_aff_campaign_deleted($aff_network_id) {

		// query criteria
		$query = array('aff_network_id' => $aff_network_id);

		// object to be updated
		$data = array('$set' => array('aff_network_deleted' => 1)); //,'aff_network_time' => time()));

		return Db::update(self::_coll, $query, $data);
	}

	/**
	 * func #184
	 * -used in /tracking202/setup/aff_campaigns.php(19487)
	/tracking202/setup/landing_pages.php(17129)
	/tracking202/setup/text_ads.php(21076)
	 * -SELECT *
	FROM 202_aff_campaigns
	WHERE aff_network_id = 'vv.aff_network_id'
	AND aff_campaign_deleted = '0'
	ORDER BY aff_campaign_name ASC
	 *
	 * find by aff network id
	 */
	public static function find_by_aff_network_id($aff_network_id) {


		// query criteria
		$query = array('aff_campaign_deleted' => array('$ne' => 1),
		               'aff_network_id' => $aff_network_id);

		// options for query
		$sort = array('aff_campaign_name' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #106
	 * -used in /tracking202/ajax/aff_campaigns.php(251)
	 * -SELECT *
	FROM 202_aff_campaigns
	WHERE user_id = 'vv.user_id'
	AND aff_network_id = 'vv.aff_network_id'
	AND aff_campaign_deleted = '0'
	ORDER BY aff_campaign_name ASC
	 *
	 * find by aff network id, user id
	 */
	public static function find_by_aff_network_id_and_user_id($aff_network_id, $user_id) {


		// query criteria
		$query = array('aff_campaign_deleted' => array('$ne' => 1),
		               'aff_network_id' => $aff_network_id,
		               'user_id' => $user_id);

		// options for query
		$sort = array('aff_campaign_name' => 1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #178
	 * -used in /tracking202/setup/aff_campaigns.php(2187)
	/tracking202/setup/landing_pages.php(1638)
	/tracking202/setup/text_ads.php(684)
	 * -SELECT *
	FROM 202_aff_campaigns
	WHERE user_id = 'vv.user_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * find by aff campaign id, user id
	 */
	public static function find_by_id_and_user_id($aff_campaign_id, $user_id) {


		// query criteria
		$query = array('_id' => $aff_campaign_id,
		               'user_id' => $user_id);

		return Db::find(self::_coll, $query);
	}


	/**
	 * func #104
	 * -used in /tracking202/ajax/adv_landing_pages.php(474)
	/tracking202/setup/get_adv_landing_code.php(2002)
	 * -SELECT aff_campaign_id, aff_campaign_name, aff_network_name
	FROM 202_aff_campaigns
	LEFT JOIN 202_aff_networks USING (aff_network_id)
	WHERE 202_aff_campaigns.user_id = 'vv.user_id'
	AND aff_campaign_deleted = '0'
	AND aff_network_deleted = 0
	ORDER BY aff_network_name ASC
	 *
	 * find by user id
	 */
	public static function find_by_user_id($user_id) {


		// query criteria
		$query = array('user_id' => $user_id,
		               'aff_campaign_deleted' => array('$ne' => 1),
		               'aff_network_deleted' => array('$ne' => 1));

		// options for query
		// fields needed
		$fields = array("_id", "aff_campaign_name", "aff_network_name");
		$sort = array('aff_network_name' => 1);

		return Db::find(self::_coll, $query, array('fields' => $fields, 'sort' => $sort));
	}


	/**
	 * func #145
	 * -used in /tracking202/ajax/update_cpc.php(1849)
	/tracking202/ajax/update_cpc2.php(2018)
	/tracking202/setup/aff_campaigns.php(8941)
	 * -SELECT *
	FROM 202_aff_campaigns
	WHERE aff_campaign_id = 'vv.aff_campaign_id'
	AND user_id = 'vv.user_id'
	 *
	 * find one by aff campaign id, user id
	 */
	public static function find_one_by_id_and_user_id($aff_campaign_id, $user_id) {


		// query criteria
		$query = array('_id' => $aff_campaign_id,
		               'user_id' => $user_id);

		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #167
	 * -used in /tracking202/redirect/off.php(1311)
	 * -SELECT aff_campaign_rotate, aff_campaign_url, aff_campaign_url_2, aff_campaign_url_3, aff_campaign_url_4, aff_campaign_url_5, aff_campaign_name, aff_campaign_cloaking
	FROM 202_aff_campaigns
	WHERE aff_campaign_id_public = 'vv.aff_campaign_id_public'
	 *
	 * find one by aff campaign id public
	 */
	public static function find_one_by_id_public($aff_campaign_id_public) {


		// query criteria
		$query = array('aff_campaign_id_public' => $aff_campaign_id_public);

		// options for query
		// fields needed
		$fields = array("aff_campaign_cloaking", "aff_campaign_name", "aff_campaign_rotate", "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3", "aff_campaign_url_4", "aff_campaign_url_5");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #208
	 * -used in /tracking202/static/pb.php(177)
	 * -SELECT aff_campaign_id
	FROM 202_aff_campaigns
	WHERE aff_campaign_id_public = 'vv.aff_campaign_id_public'
	 *
	 * find one by aff campaign id public
	 */
	public static function find_one_by_id_public1($aff_campaign_id_public) {


		// query criteria
		$query = array('aff_campaign_id_public' => $aff_campaign_id_public);

		// options for query
		// fields needed
		$fields = array("_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #211
	 * -used in /tracking202/static/px.php(180)
	 * -SELECT user_id
	FROM 202_aff_campaigns
	WHERE aff_campaign_id_public = 'vv.aff_campaign_id_public'
	 *
	 * find one by aff campaign id public
	 */
	public static function find_one_by_id_public2($aff_campaign_id_public) {


		// query criteria
		$query = array('aff_campaign_id_public' => $aff_campaign_id_public);

		// options for query
		// fields needed
		$fields = array("user_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #168
	 * -used in /tracking202/redirect/off.php(4649)
	 * -SELECT 2c.click_id, 2c.user_id, click_filtered, landing_page_id, click_cloaking, click_cloaking_site_url_id, click_redirect_site_url_id, 2ac.aff_campaign_id, aff_campaign_rotate, aff_campaign_url, aff_campaign_url_2, aff_campaign_url_3, aff_campaign_url_4, aff_campaign_url_5, aff_campaign_name, aff_campaign_cloaking, aff_campaign_payout
	FROM 202_aff_campaigns AS 2ac, 202_clicks_record AS 2cr
	LEFT JOIN 202_clicks AS 2c ON (2c.click_id = 2cr.click_id)
	LEFT JOIN 202_clicks_site AS 2cs ON (2cs.click_id = 2cr.click_id)
	WHERE 2ac.aff_campaign_id_public = 'vv.aff_campaign_id_public'
	AND 2cr.click_id_public = 'vv.click_id_public'
	 *
	 * find one by aff campaign id public, click id public
	 */
	public static function find_one_by_id_public_and_click_id_public($aff_campaign_id_public, $click_id_public) {

		// query criteria
		$query = array('click_id_public' => $click_id_public);

		$fields = array("_id", "click_cloaking", "click_cloaking_site_url_id", "click_filtered", "aff_campaign_id", "click_redirect_site_url_id", "landing_page_id", "user_id");

		$c_adv = Db::findOne(ClicksAdvance_DAO::_coll, $query, $fields);

		//		$query = array('aff_campaign_id_public' => $aff_campaign_id_public);
		//		// fields needed
		//		$fields = array("aff_campaign_cloaking", "_id", "aff_campaign_name", "aff_campaign_payout", "aff_campaign_rotate", "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3", "aff_campaign_url_4", "aff_campaign_url_5");
		$aff_campaign_id = $c_adv['aff_campaign_id'];
		$aff_c = self::get($aff_campaign_id);
		assert($aff_c['$aff_campaign_id_public'] == $aff_campaign_id_public);

		//$c_adv['click_id'] = $c_adv['_id'];
		//unset($c_adv['_id']);

		return array_merge($aff_c, $c_adv);
	}


	/**
	 * func #118
	 * -used in /tracking202/ajax/get_adv_landing_code.php(3898)
	 * -SELECT aff_campaign_id_public, aff_campaign_name
	FROM 202_aff_campaigns
	WHERE aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * get by idaff campaign id
	 */
	public static function get_little_info($aff_campaign_id) {


		// query criteria
		$query = array('_id' => $aff_campaign_id);

		// options for query
		// fields needed
		$fields = array("aff_campaign_id_public", "aff_campaign_name");

		DU::dump($query);
		DU::dump($fields);
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #119
	 * -used in /tracking202/ajax/get_postback.php(402)
	/tracking202/setup/landing_pages.php(8661)
	/tracking202/setup/text_ads.php(9792)
	 * -SELECT *
	FROM 202_aff_campaigns
	WHERE aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * get by id1 aff campaign id
	 */
	public static function get($aff_campaign_id) {

		// query criteria
		$query = array('_id' => $aff_campaign_id);

		return Db::findOne(self::_coll, $query);
	}

	/**	 *
	 * -used in record_dev.php
	 * @static 获得 payout
	 * @param  $aff_campaign_id
	 * @return float
	 */
	public static function get_payout_by_id($aff_campaign_id) {

		// query criteria
		$query = array('_id' => $aff_campaign_id);
		$fields = array("aff_campaign_payout");
		$aff_c = Db::findOne(self::_coll, $query, $fields);

		if ($aff_c) {
			return $aff_c["aff_campaign_payout"];
		}
		else {
			return 0.0;
		}
	}


//	/**
//	 * func #180
//	 * -used in /tracking202/setup/aff_campaigns.php(6045)
//	 * -UPDATE 202_aff_campaigns
//			SET aff_network_id = 'vv.aff_network_id', user_id = 'vv.user_id', aff_campaign_name = 'vv.aff_campaign_name', aff_campaign_url = 'vv.aff_campaign_url', aff_campaign_url_2 = 'vv.aff_campaign_url_2', aff_campaign_url_3 = 'vv.aff_campaign_url_3', aff_campaign_url_4 = 'vv.aff_campaign_url_4', aff_campaign_url_5 = 'vv.aff_campaign_url_5', aff_campaign_rotate = 'vv.aff_campaign_rotate', aff_campaign_payout = 'vv.aff_campaign_payout', aff_campaign_cloaking = 'vv.aff_campaign_cloaking', aff_campaign_time = 'vv.aff_campaign_time'
//			WHERE aff_campaign_id = 'vv.aff_campaign_id'
//	 *
//	 * update by values
//	 */
//	public static function update_by($_values) {
//		//variables passed
//		$aff_network_id = $_values['aff_network_id'];
//		$user_id = $_values['user_id'];
//		$aff_campaign_name = $_values['aff_campaign_name'];
//		$aff_campaign_url = $_values['aff_campaign_url'];
//		$aff_campaign_url_2 = $_values['aff_campaign_url_2'];
//		$aff_campaign_url_3 = $_values['aff_campaign_url_3'];
//		$aff_campaign_url_4 = $_values['aff_campaign_url_4'];
//		$aff_campaign_url_5 = $_values['aff_campaign_url_5'];
//		$aff_campaign_rotate = $_values['aff_campaign_rotate'];
//		$aff_campaign_payout = $_values['aff_campaign_payout'];
//		$aff_campaign_cloaking = $_values['aff_campaign_cloaking'];
//		$aff_campaign_time = $_values['aff_campaign_time'];
//		$aff_campaign_id = $_values['aff_campaign_id'];
//
//		// query criteria
//		$query = array('_id' => $aff_campaign_id);
//
//		// object to be updated
//		$data = array('$set' =>array('aff_campaign_cloaking' => $aff_campaign_cloaking,
//					'aff_campaign_name' => $aff_campaign_name,
//					'aff_campaign_payout' => $aff_campaign_payout,
//					'aff_campaign_rotate' => $aff_campaign_rotate,
//					'aff_campaign_time' => $aff_campaign_time,
//					'aff_campaign_url' => $aff_campaign_url,
//					'aff_campaign_url_2' => $aff_campaign_url_2,
//					'aff_campaign_url_3' => $aff_campaign_url_3,
//					'aff_campaign_url_4' => $aff_campaign_url_4,
//					'aff_campaign_url_5' => $aff_campaign_url_5,
//					'aff_network_id' => $aff_network_id,
//					'user_id' => $user_id) );
//
//		return Db::update(self::_coll, $query, $data);
//	}


	/**
	 * func #181
	 * -used in /tracking202/setup/aff_campaigns.php(7727)
	 * -UPDATE 202_aff_campaigns
	SET aff_campaign_id_public = 'vv.aff_campaign_id_public'
	WHERE aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * update by aff campaign id, aff campaign id public
	 */
	public static function update_by_id_and_id_public($aff_campaign_id, $aff_campaign_id_public) {


		// query criteria
		//$query = array('_id' => $aff_campaign_id);

		// object to be updated
		$data = array('$set' => array('aff_campaign_id_public' => $aff_campaign_id_public));

		return Db::updateOne(self::_coll, $aff_campaign_id, $data);
	}


	/**
	 * func #182
	 * -used in /tracking202/setup/aff_campaigns.php(8321)
	 * -UPDATE 202_aff_campaigns
	SET aff_campaign_deleted = '1', aff_campaign_time = 'vv.aff_campaign_time'
	WHERE user_id = 'vv.user_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * update by aff campaign id, aff campaign time, user id
	 */
	public static function update_by_id_and_time_and_user_id($aff_campaign_id, $aff_campaign_time, $user_id) {


		// query criteria
		$query = array('_id' => $aff_campaign_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('aff_campaign_deleted' => 1,
		                              'aff_campaign_time' => $aff_campaign_time));

		return Db::updateOne(self::_coll, $query, $data);
	}


}