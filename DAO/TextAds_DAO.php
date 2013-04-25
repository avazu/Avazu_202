<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: text_ads
 *  - Time: March 18, 2011, 3:39 am
 */

 
class TextAds_DAO {
	const _coll = 'text_ads';

	/**
	 * func #204
	 * -used in /tracking202/setup/text_ads.php(21933)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE aff_campaign_id = 'vv.aff_campaign_id'
				AND text_ad_deleted = '0' 
			ORDER BY text_ad_name ASC
	 *
	 * find by aff campaign id 
	 */
	public static function find_by_aff_campaign_id($aff_campaign_id) {
		

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
					'text_ad_deleted' => array('$ne' => 1));

		// options for query
		$sort = array('text_ad_name' => 1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #143
	 * -used in /tracking202/ajax/text_ads.php(253)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE user_id = 'vv.user_id'
				AND aff_campaign_id = 'vv.aff_campaign_id'
				AND text_ad_deleted = '0' 
			ORDER BY aff_campaign_id , text_ad_name ASC
	 *
	 * find by aff campaign id, user id 
	 */
	public static function find_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id) {
		

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
					'text_ad_deleted' => array('$ne' => 1),
					'user_id' => $user_id);

		// options for query
		$sort = array('aff_campaign_id' => 1, 'text_ad_name' => 1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #200
	 * -used in /tracking202/setup/text_ads.php(2357)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE user_id = 'vv.user_id'
				AND text_ad_id = 'vv.text_ad_id'
	 *
	 * find by text ad id, user id 
	 */
	public static function find_by_id_and_user_id($text_ad_id, $user_id) {
		

		// query criteria
		$query = array('_id' => $text_ad_id,
					'user_id' => $user_id);
    
		return Db::find(self::_coll, $query);
	}


	/**
	 * func #203
	 * -used in /tracking202/setup/text_ads.php(19013)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE landing_page_id = 'vv.landing_page_id'
				AND text_ad_deleted = '0' 
			ORDER BY text_ad_name ASC
	 *
	 * find by landing page id 
	 */
	public static function find_by_landing_page_id($landing_page_id) {
		

		// query criteria
		$query = array('landing_page_id' => $landing_page_id,
					'text_ad_deleted' => array('$ne' => 1));

		// options for query
		$sort = array('text_ad_name' => 1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #105
	 * -used in /tracking202/ajax/adv_text_ads.php(251)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE user_id = 'vv.user_id'
				AND landing_page_id = 'vv.landing_page_id'
				AND text_ad_deleted = '0' 
				AND text_ad_type = 1 
			ORDER BY aff_campaign_id , text_ad_name ASC
	 *
	 * find by landing page id, user id 
	 */
	public static function find_by_landing_page_id_and_user_id($landing_page_id, $user_id) {
		

		// query criteria
		$query = array('landing_page_id' => $landing_page_id,
					'text_ad_deleted' => array('$ne' => 1),
					'text_ad_type' => 1,
					'user_id' => $user_id);

		// options for query
		$sort = array('aff_campaign_id' => 1, 'text_ad_name' => 1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #103
	 * -used in /tracking202/ajax/ad_preview.php(284)
				/tracking202/ajax/update_cpc.php(2653)
				/tracking202/ajax/update_cpc2.php(2822)
				/tracking202/setup/text_ads.php(6387)
				/tracking202/setup/text_ads.php(7752)
	 * -SELECT * 
			FROM 202_text_ads 
			WHERE text_ad_id = 'vv.text_ad_id'
				AND user_id = 'vv.user_id'
	 *
	 * find one by text ad id, user id 
	 */
	public static function find_one_by_id_and_user_id($text_ad_id, $user_id) {
		

		// query criteria
		$query = array('_id' => $text_ad_id,
					'user_id' => $user_id);
    
		return Db::findOne(self::_coll, $query);
	}


	/**
	 * func #201
	 * -used in /tracking202/setup/text_ads.php(3618)
	 * -UPDATE 202_text_ads 
			SET aff_campaign_id = 'vv.aff_campaign_id', text_ad_type = 'vv.text_ad_type', landing_page_id = 'vv.landing_page_id', text_ad_name = 'vv.text_ad_name', text_ad_headline = 'vv.text_ad_headline', text_ad_description = 'vv.text_ad_description', text_ad_display_url = 'vv.text_ad_display_url', user_id = 'vv.user_id', text_ad_time = 'vv.text_ad_time'
			WHERE text_ad_id = 'vv.text_ad_id'
	 *
	 * update or created by values
   */
  public static function upsert_by($_values) {
    //variables passed
    $aff_campaign_id = $_values['aff_campaign_id'];
    $text_ad_type = $_values['text_ad_type'];
    $landing_page_id = $_values['landing_page_id'];
    $text_ad_name = $_values['text_ad_name'];
    $text_ad_headline = $_values['text_ad_headline'];
    $text_ad_description = $_values['text_ad_description'];
    $text_ad_display_url = $_values['text_ad_display_url'];
    $user_id = $_values['user_id'];
    $text_ad_time = $_values['text_ad_time'];
    //$text_ad_id = $_values['text_ad_id'];


    // object to be updated
		$data = array('aff_campaign_id' => $aff_campaign_id,
					'landing_page_id' => $landing_page_id,
					'text_ad_description' => $text_ad_description,
					'text_ad_display_url' => $text_ad_display_url,
					'text_ad_headline' => $text_ad_headline,
					'text_ad_name' => $text_ad_name,
					'text_ad_time' => $text_ad_time,
					'text_ad_type' => $text_ad_type, //0
					'user_id' => $user_id);

    $_id = -1;
    if (isset($_values['text_ad_id'])) {
      $_id = $_values['text_ad_id'];
			unset($_values['text_ad_id']);
    }
    if ($_id < 0) {
      $_id = Db::seq(self::_coll);
			$data['text_ad_deleted'] = 0;
    }
    $data['_id'] = $_id;

    // query criteria
    //$query = array('_id', $_id);

    return Db::upsertById(self::_coll, $data);
  }


	/**
	 * func #202
	 * -used in /tracking202/setup/text_ads.php(5812)
	 * -UPDATE 202_text_ads 
			SET text_ad_deleted = '1', text_ad_time = 'vv.text_ad_time'
			WHERE user_id = 'vv.user_id'
				AND text_ad_id = 'vv.text_ad_id'
	 *
	 * update by text ad id, text ad time, user id 
	 */
	public static function update_by_id_and_time_and_user_id($text_ad_id, $text_ad_time, $user_id) {
		

		// query criteria
		$query = array('_id' => $text_ad_id,
					'user_id' => $user_id);

		// object to be updated
		$data = array('$set' =>array('text_ad_deleted' => 1,
					'text_ad_time' => $text_ad_time) );
    
		return Db::updateOne(self::_coll, $query, $data);
	}

	public static function get($text_ad_id) {
		return Db::findOne(self::_coll, $text_ad_id);
	}

}