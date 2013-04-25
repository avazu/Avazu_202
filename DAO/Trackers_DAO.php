<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: trackers
 *  - Time: March 18, 2011, 3:39 am
 */


class Trackers_DAO {
	const _coll = 'trackers';

	/**
	 * func #116
	 * -used in /tracking202/ajax/generate_tracking_link.php(3758)
	 * -INSERT INTO 202_trackers
	SET user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', text_ad_id = 'vv.text_ad_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', landing_page_id = 'vv.landing_page_id', click_cloaking = 'vv.click_cloaking', tracker_time = 'vv.tracker_time'
	 *
	 * create by values
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$text_ad_id = $_values['text_ad_id'];
		$ppc_account_id = $_values['ppc_account_id'];
		$click_cpc = $_values['click_cpc'];
		$landing_page_id = $_values['landing_page_id'];
		$click_cloaking = $_values['click_cloaking'];
		$tracker_time = $_values['tracker_time'];

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
		              'click_cloaking' => $click_cloaking,
		              'click_cpc' => $click_cpc,
		              'landing_page_id' => $landing_page_id,
		              'ppc_account_id' => $ppc_account_id,
		              'text_ad_id' => $text_ad_id,
		              'tracker_time' => $tracker_time,
		              'user_id' => $user_id);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #151
	 * -used in /tracking202/redirect/dl.php(1269)
	 * -SELECT 202_trackers.user_id, 202_trackers.aff_campaign_id, text_ad_id, ppc_account_id, click_cpc, click_cloaking, aff_campaign_rotate, aff_campaign_url, aff_campaign_url_2, aff_campaign_url_3, aff_campaign_url_4, aff_campaign_url_5, aff_campaign_payout, aff_campaign_cloaking
	FROM 202_trackers
	LEFT JOIN 202_aff_campaigns USING (aff_campaign_id)
	WHERE tracker_id_public = 'vv.tracker_id_public'
	 *
	 * find one by tracker id public
	 */
	public static function find_one_by_id_public($tracker_id_public) {

		// query criteria
		$query = array('tracker_id_public' => $tracker_id_public);
		// fields needed
		$fields = array("aff_campaign_id", "click_cloaking", "click_cpc", "ppc_account_id", "text_ad_id", "user_id");
		$tracker = Db::findOne(self::_coll, $query, $fields);

		$aff_c_id = $tracker['aff_campaign_id'];
		// fields needed
		//$fields = array("aff_campaign_cloaking", "aff_campaign_payout", "aff_campaign_rotate", "aff_campaign_url", "aff_campaign_url_2", "aff_campaign_url_3", "aff_campaign_url_4", "aff_campaign_url_5");
		$aff_c = AffCampaigns_DAO::get($aff_c_id);
		//$aff_c['aff_campaign_id'] = $aff_c['_id'];
		//unset($aff_c['_id']);

		//echo "tracker_id_public=$tracker_id_public   ";
		//DU::dump($tracker);
		return array_merge($tracker, $aff_c);
	}


	/**
	 * func #163
	 * -used in /tracking202/redirect/lp.php(3081)
	/tracking202/static/record_simple.php(1623)
	 * -SELECT text_ad_id, ppc_account_id, click_cpc, click_cloaking
	FROM 202_trackers
	WHERE tracker_id_public = 'vv.tracker_id_public'
	 *
	 * find one by tracker id public
	 */
	public static function find_one_by_id_public1($tracker_id_public) {


		// query criteria
		$query = array('tracker_id_public' => $tracker_id_public);

		// options for query
		// fields needed
		$fields = array("click_cloaking", "click_cpc", "ppc_account_id", "text_ad_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #216
	 * -used in /tracking202/static/record_adv.php(1117)
	 * -SELECT text_ad_id, ppc_account_id, click_cpc, click_cloaking, aff_campaign_id
	FROM 202_trackers
	WHERE tracker_id_public = 'vv.tracker_id_public'
	 *
	 * find one by tracker id public
	 */
	public static function find_one_by_id_public2($tracker_id_public) {


		// query criteria
		$query = array('tracker_id_public' => $tracker_id_public);

		// options for query
		// fields needed
		$fields = array("aff_campaign_id", "click_cloaking", "click_cpc", "ppc_account_id", "text_ad_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #117
	 * -used in /tracking202/ajax/generate_tracking_link.php(4636)
	 * -UPDATE 202_trackers
	SET tracker_id_public = 'vv.tracker_id_public'
	WHERE tracker_id = 'vv.tracker_id'
	 *
	 * update by tracker id, tracker id public
	 */
	public static function update_by_id_and_id_public($tracker_id, $tracker_id_public) {


		// query criteria
		$query = array('_id' => $tracker_id);

		// object to be updated
		$data = array('$set' => array('tracker_id_public' => $tracker_id_public));

		return Db::updateOne(self::_coll, $query, $data);
	}


}