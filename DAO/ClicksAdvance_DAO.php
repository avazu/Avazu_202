<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/Cursor.php');
require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: clicks
 *  - Time: March 18, 2011, 3:39 am
 *
 * 在clicks被记录到后，通过cron job(delayed command)或者消息队列异步记录成advance数据(todo)
 *
 * clicks_advance现被用作点击的最主要的数据载体，对应原prosper中clicks，clicks_advance, clicks_tracking, clicks_record, clicks_site的信息汇总
 *  所有的分析和统计都在这个collection数据的基础上来进行
 */


class ClicksAdvance_DAO {
	const _coll = 'clicks_advance';


	/**
	 * func #93, 94...
	 * -used in /tracking202/ajax/account_overview.php(3345...)
	 *
	 * aggre clicks by user pref show, values
	 *
	 * @param  $key 当前group by，但过滤条件中指定了唯一的值
	 * @param  click filter: click lead or filtered == 0/1
	 * @param  $user_pref_show: click_alp=0/1, campaign_id, account_id, lp_id等其他条件，来过滤clicks数据，用于统计mp的输入
	 * @param  $_values 只包含标准的三项：user，from 和 to
	 * @return array 一行
	 */
	//todo return multiple grouped by key to refactory account_overview.php
	public static function aggre_by_user_pref_show_and_others($key, $click_filtered, $user_pref_show, $_values) {
		//variables passed
		$user_id = $_values['user_id'];
		$from = $_values['from'];
		$to = $_values['to'];

		// query criteria
		$query = array('click_time' => array('$gt' => $from, '$lte' => $to),
		               'user_id' => $user_id);

		$query = array_merge($query, $click_filtered, $user_pref_show);

		$v = NameUtil::encode_click_adv_doc_simple(self::_coll, $key);
		$key_doc_str = '{' . "$key: this.$v" . '}';
		$aggre_docs = self::aggre_clicks($key_doc_str, $query);
		assert(count($aggre_docs) <= 1);
		//echo "aggre key=$key result:";
		DU::dump($aggre_docs, __FUNCTION__);

		$aggre_docs_0_value = $aggre_docs[0]['value'];
		//echo "....aggre_docs_0_value=";
		DU::dump($aggre_docs_0_value, __FUNCTION__);
		return $aggre_docs_0_value;
	}

	static function aggre_clicks($key_doc_str, $query) {

		$click_cpc_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_cpc");
		$click_payout_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_payout");
		$click_lead_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_lead");

		$map = //<<<MAP
						"
          function(){
            emit( $key_doc_str,
                  { clicks: 1,
											click_cpc: this.$click_cpc_str,
											click_lead: this.$click_lead_str,
											income: this.$click_payout_str * this.$click_lead_str
									}
                 );
          }";
		//MAP;

		//todo the value init?
		$reduce = //<<<REDUCE
						"
          function(key, values){
            var clicks = 0;
            var click_cpc = 0;
            var click_lead = 0;
            var income = 0;
            values.forEach(function(doc) {
              clicks += doc.clicks;
              click_cpc += doc.click_cpc;
              click_lead += doc.click_lead;
              income += doc.income;
            });

            return {  clicks: clicks,
                      click_cpc: click_cpc,
                      click_lead: click_lead,
                      income: income
                    };
          }
          ";
		//REDUCE;

		$finalize = //<<<FINALIZE
						"
          function(key, value)	{
            avg_cpc = value.click_cpc / value.clicks;
            return {
                      clicks: value.clicks,
                      click_cpc: value.click_cpc,
                      cost: value.click_cpc,
                      avg_cpc: avg_cpc,
                      click_lead: value.click_lead,
                      leads: value.click_lead,
                      income: value.income
                    };
          }
          ";
		//FINALIZE;
		return self::mapReduce($query, $map, $reduce, $finalize);
	}


	/**
	 * 计算符合条件的所有该时间段clicks之聚集
	 * #52 #56...
	 *
	 * @param  $pref_time
	 * @param  $pref_adv
	 * @param  $pref_show
	 * @param  $from
	 * @param  $to
	 * @param  $click_filtered
	 * @return void
	 */
	static function aggre_run($pref_time, $pref_adv, $pref_show, $from, $to, $click_filtered) {

		$affc_query = array('aff_campaign_deleted' => array('$ne' => 1),
		                    'aff_network_deleted' => array('$ne' => 1));
		$aff_campaign_ids = Db::distinct(AffCampaigns_DAO::_coll, '_id', $affc_query);

		$query = self::get_query($pref_time, $pref_adv, $pref_show);
		$query = array_merge($query, $click_filtered);
		$query['click_time'] = array('$gt' => $from, '$lte' => $to);

		//AND (2c.click_alp = '1' OR (2ac.aff_campaign_deleted='0' AND 2an.aff_network_deleted='0'))
		if (empty($aff_campaign_ids)) {
			$query['click_alp'] = 1;
		} else {
			$query['$or'] = array(array('click_alp' => 1), array('aff_campaign_id' => array('$in' => $aff_campaign_ids)));
		}

		//$key = $from; //这样所有的doc都可以累计在一起了
		//const key
		$key_doc_str = '{' . "from: $from, to: $to" . '}';
		$mp_ = self::aggre_clicks($key_doc_str, $query);

		if (empty($mp_)) {
			return array();
		}
		if ($mp_[0]['_id']['from'] != $from) {
			DU::dump($from);
			DU::dump($mp_[0]['_id']);
			die("why not from!");
		}
		return $mp_[0]['value'];
	}


	/**
	 * func #141 ...
	 * -used in /tracking202/ajax/sort_text_ads.php ...
	 *
	 * aggre clicks group by thing like text ad id ...
	 */
	public static function aggre_run_grouped($pref_time, $pref_adv, $pref_show, $click_filtered, $key) {

		$query = self::get_query($pref_time, $pref_adv, $pref_show);
		$query = array_merge($query, $click_filtered);

		$v = NameUtil::encode_click_adv_doc_simple(self::_coll, $key);
		$key_doc_str = '{' . "$key: this.$v" . '}';
		$aggre_r = self::aggre_clicks($key_doc_str, $query);
		//echo "\naggre run grouped: ";
		DU::dump($aggre_r);

		$plain_aggre_r = array();
		foreach ($aggre_r as $aggre_doc) {
			//这里的names不用预先填充，在每个sort_*.php中最后都会自己获取
			//$aggre_doc_id_with_name_arr = self::fill_doc_names($aggre_doc['_id']);
			//$plain_aggre_r[] = array_merge($aggre_doc_id_with_name_arr, $aggre_doc['value']);
			//$aggre_doc_id_decoded = NameUtil::decode_click_adv_doc(self::_coll, $aggre_doc['_id']);
			$plain_aggre_r[] = array_merge($aggre_doc['_id'], $aggre_doc['value']);
		}
		DU::dump($plain_aggre_r, __FUNCTION__);
		return $plain_aggre_r;
	}

	public static function fill_doc_names($doc) {
		$name_arr = array();
		foreach ($doc as $k => $v) {
			if (!preg_match('/\w+_id$/', $k) || preg_match('/(click|user|_site_url|referer|referer_site_domain)_id$/', $k)) {
				continue;
			}
			$doc_name = NameUtil::get_doc_name_by_id_name($k);
			$doc_name_value = NameCatcher::get($doc_name, $v);
			$name_arr[$doc_name] = $doc_name_value;
		}

		return array_merge($doc, $name_arr);
	}


	/**
	 * report summary form, group overview report
	 * @static
	 * @param  $query
	 * @param  $keys
	 * @return
	 */
	public static function aggre_group_overview_grouped($keys, $query) {

		$keys = NameUtil::translate_group_overview_id_to_clicks_advance_id($keys);
		$aggre_r = self::aggre_clicks_more($keys, $query);
		//echo "aggre group overview_grouped: ";
		DU::dump($aggre_r);

		$plain_aggre_r = array();
		foreach ($aggre_r as $aggre_doc) {

			//$aggre_doc_id_decoded = NameUtil::decode_click_adv_doc(self::_coll, $aggre_doc['_id']);
			//$aggre_doc_id_with_name_arr = self::fill_doc_names($aggre_doc_id_decoded);
			$aggre_doc_id_with_name_arr = self::fill_doc_names($aggre_doc['_id']);
			//改名字
			$aggre_doc_id_with_name_arr_alias = NameUtil::fill_group_overview_alias($aggre_doc_id_with_name_arr);

			$plain_aggre_r[] = array_merge($aggre_doc_id_with_name_arr_alias, $aggre_doc['value']);
		}

		DU::dump($plain_aggre_r, __FUNCTION__);
		return $plain_aggre_r;
	}

	// payout: this.aff_campaign_payout,
	static function aggre_clicks_more($keys, $query, $map = "") {
		$k_str_list = array();
		DU::dump($keys);
		foreach ($keys as $key) {
			$v = NameUtil::encode_click_adv_doc_simple(self::_coll, $key);
			$k_str_list[] = "$key: this.$v";
		}
		$keys_doc_string = "{" . join(', ', $k_str_list) . "}";


		$click_cpc_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_cpc");
		$click_out_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_out");
		$click_payout_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_payout");
		$click_lead_str = NameUtil::encode_click_adv_doc_simple(self::_coll, "click_lead");

		if ($map == "") {
			$map = //<<<MAP
							"
          function(){
            emit($keys_doc_string, { clicks: 1,
                              click_cpc: this.$click_cpc_str,
                              click_lead: this.$click_lead_str,
                              click_out: this.$click_out_str,
                              income: this.$click_payout_str * this.$click_lead_str
                            }
                 );
          }";
			//MAP;
		}
		$reduce = //<<<REDUCE
						"
          function(key, values){
            var clicks = 0;
            var click_cpc = 0;
            var click_lead = 0;
            var income = 0;
            var click_out = 0;
            values.forEach(function(doc) {
              clicks += doc.clicks;
              click_cpc += doc.click_cpc;
              click_lead += doc.click_lead;
              click_out += doc.click_out;
              income += doc.income;
            });

            return {  clicks: clicks,
                      click_cpc: click_cpc,
                      click_lead: click_lead,
                      click_out: click_out,
                      income: income
                    };
          }
          ";
		//REDUCE;

		$finalize = //<<<FINALIZE
						"
          function(key, value)	{
            avg_cpc = value.click_cpc / value.clicks;
            return {
                      clicks: value.clicks,
                      click_out: value.click_out,
                      leads: value.click_lead,
                      cost: value.click_cpc,
                      avg_cpc: avg_cpc,
                      income: value.income
                    };
          }
          ";
		//FINALIZE;
		return self::mapReduce($query, $map, $reduce, $finalize);
	}


	//todo move to Db lib
	public static function mapReduce($query, $map, $reduce, $finalize) {

		$collection = ClicksAdvance_DAO::_coll;

		DU::dump($query);
		$query = NameUtil::encode_click_adv_doc($collection, $query);
		DU::dump($query);

		$db = Db::getDb($collection, false);
		$response = $db->command(array(
		                              "mapreduce" => $collection,
		                              "query" => $query,
		                              "map" => new MongoCode($map),
		                              "reduce" => new MongoCode($reduce),
		                              "finalize" => new MongoCode($finalize),
		                              "out" => array("inline" => 1),
		                              "scope" => array("clicks" => 0,
		                                               "click_cpc" => 0,
		                                               "avg_cpc" => 0,
		                                               "click_lead" => 0,
		                                               "income" => 0,
		                                               "cost" => 0,
		                                               "leads" => 0,
		                                               "click_out" => 0
		                              ),
		                              'verbose' => true //todo debug only
		                         ));

		DU::dump($collection);
		//echo "query=";
		DU::dump($query);
		DU::die_if_empty($query);
		//echo "map=";
		DU::dump($map);
		//echo "map reduce return: ";
		DU::dump($response);
		//    if ($response->valid()) {
		//    }

		return $response['results'];
	}


	/**
	 * 获取由条件产生的查询条件
	 *   taken out of functions-tracking202.php
	 *
	 * @static
	 * @param  $pref_time
	 * @param  $pref_adv
	 * @param  $pref_show
	 * @return array query用来过滤的条件
	 *
	 */
	static function get_query_for_group_overview($user_row, $from, $to) {

		$user_id = $user_row['user_id'];

		// query criteria
		$query = array('user_id' => $user_id);
		$query['click_time'] = array('$gt' => $from, '$lte' => $to);

		//set show preferences
		if ($user_row['user_pref_show'] == 'filtered') {
			$query['click_filtered'] = 1;
		} elseif ($user_row['user_pref_show'] == 'real') {
			$query['click_filtered'] = 0;
		} elseif ($user_row['user_pref_show'] == 'leads') {
			//			$query['click_filtered'] = 0;
			$query['click_lead'] = 1;
		}


		//set advanced preferences
		if ($user_row['user_pref_ppc_network_id'] and !($user_row['user_pref_ppc_account_id'])) {
			$query['ppc_network_id'] = $user_row['user_pref_ppc_network_id'];
		}

		if ($user_row['user_pref_ppc_account_id']) {
			$query['ppc_account_id'] = $user_row['user_pref_ppc_account_id'];
		}

		if ($user_row['user_pref_aff_network_id'] and !$user_row['user_pref_aff_campaign_id']) {
			$query['aff_network_id'] = $user_row['user_pref_aff_network_id'];
		}

		if ($user_row['user_pref_aff_campaign_id']) {
			$query['aff_campaign_id'] = $user_row['user_pref_aff_campaign_id'];
		}

		if ($user_row['user_pref_text_ad_id']) {
			$query['text_ad_id'] = $user_row['user_pref_text_ad_id'];
		}

		//if ($user_row['user_pref_method_of_promotion'] != 0) {
		//	if ($user_row['user_pref_method_of_promotion'] == 'directlink') {
		//		$query['landing_page_id'] = '';
		//	} elseif ($user_row['user_pref_method_of_promotion'] == 'landingpage') {
		//		$query['landing_page_id'] = array('$ne' => ''); //2c.landing_page_id!=''
		//	}
		//}
		//
		//if ($user_row['user_pref_landing_page_id']) {
		//	$query['landing_page_id'] = $user_row['user_pref_landing_page_id'];
		//}

		/*if ($user_row['pref_country_id']) {
	$_values['user_pref_country_id'] = $user_row['pref_country_id'];
	$click_sql .=   " AND      pref_country_id=".$_values['user_pref_country_id'];
}*/

		if ($user_row['user_pref_referer']) {
			//$user_pref_referer = CONVERT( _utf8 '%" . $_values['user_pref_referer'] . "%' USING latin1 ) COLLATE latin1_swedish_ci
			$site_url_ids = self::get_liked_ids(SiteUrls_DAO::_coll, $user_row['user_pref_referer'], 'site_url_address'); //site_domain_host 不够直接
			$query['click_referer_site_rul_id'] = array('$in' => $site_url_ids);
		}

		if ($user_row['user_pref_keyword']) {
			// LIKE CONVERT( _utf8 '%" . $_values['user_pref_keyword'] . "%' USING latin1 )	COLLATE latin1_swedish_ci
			$kw_ids = self::get_liked_ids(Keywords_DAO::_coll, $user_row['user_pref_keyword'], 'keyword');
			$query['keyword_id'] = array('$in' => $kw_ids);
		}

		if ($user_row['user_pref_ip']) {
			//LIKE CONVERT( _utf8 '%" . $_values['user_pref_ip'] . "%' USING latin1 )	COLLATE latin1_swedish_ci
			$ip_ids = self::get_liked_ids(Ips_DAO::_coll, $user_row['user_pref_ip'], 'ip_address');
			$query['ip_id'] = array('$in' => $ip_ids);
		}

		return $query;
	}

	/**
	 * 获取由条件产生的查询条件
	 *   taken out of functions-tracking202.php
	 *
	 * @static
	 * @param  $pref_time
	 * @param  $pref_adv
	 * @param  $pref_show
	 * @return array query用来过滤的条件
	 *
	 */
	static function get_query($pref_time, $pref_adv, $pref_show) {

		//grab user preferences
		$user_id = (int)$_SESSION['user_id'];
		$user_row = UsersPref_DAO::get($user_id);

		// query criteria
		$query = array('user_id' => $user_id);

		//set time preferences
		if ($pref_time == true) {

			$time = grab_timeframe(); //todo fix need require once functions-tracking202.php?

			$from = $time['from'];
			$to = $time['to'];
			if ($from != '' && $to != '') {
				$query['click_time'] = array('$gt' => $from, '$lte' => $to);
			} elseif ($from != '') {
				$query['click_time'] = array('$gt' => $from); //click_time >
			} elseif ($to != '') {
				$query['click_time'] = array('$lte' => $to); //click_time <
			}
		}

		//set show preferences
		if ($pref_show == true) {
			if ($user_row['user_pref_show'] == 'filtered') {
				$query['click_filtered'] = 1;
			} elseif ($user_row['user_pref_show'] == 'real') {
				$query['click_filtered'] = 0;
			} elseif ($user_row['user_pref_show'] == 'leads') {
				$query['click_filtered'] = 0; //todo check needed this?
				$query['click_lead'] = 1;
			}
		}

		//set advanced preferences
		if ($pref_adv == true) {
			if ($user_row['user_pref_ppc_network_id'] and !($user_row['user_pref_ppc_account_id'])) {
				$query['ppc_network_id'] = $user_row['user_pref_ppc_network_id'];
			}


			if ($user_row['user_pref_ppc_account_id']) {
				$query['ppc_account_id'] = $user_row['user_pref_ppc_account_id'];
			}

			if ($user_row['user_pref_aff_network_id'] and !$user_row['user_pref_aff_campaign_id']) {
				$query['aff_network_id'] = $user_row['user_pref_aff_network_id'];
			}

			if ($user_row['user_pref_aff_campaign_id']) {
				$query['aff_campaign_id'] = $user_row['user_pref_aff_campaign_id'];
			}
			if ($user_row['user_pref_text_ad_id']) {
				$query['text_ad_id'] = $user_row['user_pref_text_ad_id'];
			}
			if ($user_row['user_pref_method_of_promotion'] != 0) {
				if ($user_row['user_pref_method_of_promotion'] == 'directlink') {
					$query['landing_page_id'] = '';
				} elseif ($user_row['user_pref_method_of_promotion'] == 'landingpage') {
					$query['landing_page_id'] = array('$ne' => ''); //2c.landing_page_id!=''
				}
			}


			if ($user_row['user_pref_landing_page_id']) {
				$query['landing_page_id'] = $user_row['user_pref_landing_page_id'];
			}

			/*if ($user_row['pref_country_id']) {
	$_values['user_pref_country_id'] = $user_row['pref_country_id'];
	$click_sql .=   " AND      pref_country_id=".$_values['user_pref_country_id'];
}*/

			if ($user_row['user_pref_referer']) {
				//$user_pref_referer = CONVERT( _utf8 '%" . $_values['user_pref_referer'] . "%' USING latin1 ) COLLATE latin1_swedish_ci
				$site_url_ids = self::get_liked_ids(SiteUrls_DAO::_coll, $user_row['user_pref_referer'], 'site_url_address'); //site_domain_host 不够直接
				$query['click_referer_site_rul_id'] = array('$in' => $site_url_ids);
			}

			if ($user_row['user_pref_keyword']) {
				// LIKE CONVERT( _utf8 '%" . $_values['user_pref_keyword'] . "%' USING latin1 )	COLLATE latin1_swedish_ci
				$kw_ids = self::get_liked_ids(Keywords_DAO::_coll, $user_row['user_pref_keyword'], 'keyword');
				$query['keyword_id'] = array('$in' => $kw_ids);
			}

			if ($user_row['user_pref_ip']) {
				//LIKE CONVERT( _utf8 '%" . $_values['user_pref_ip'] . "%' USING latin1 )	COLLATE latin1_swedish_ci
				$ip_ids = self::get_liked_ids(Ips_DAO::_coll, $user_row['user_pref_ip'], 'ip_address');
				$query['ip_id'] = array('$in' => $ip_ids);
			}
		}


		//    //set limit preferences
		//    if ($pref_order == true) {
		//      $click_sql .= " ORDER BY " . $pref_order;
		//    }

		return $query;
	}


	//$query['site_domain_host'] = new MongoRegex("/^" . $user_row['user_pref_referer'] . "/i");
	static function get_liked_ids($coll, $regex, $like_key) {
		$m_regex = new MongoRegex("/" . $regex . "/i");
		$like_query = array($like_key => $m_regex);
		$ids = Db::distinct($coll, '_id', $like_query); //todo use finda is ok too
		return $ids;
	}

	/**
	 * 获取查询结果的offset，limit，page，from和to条件
	 * @static
	 * @param  $offset
	 * @param  $pref_limit
	 * @param  $count
	 * @return
	 */
	static function get_query_limit_and_pages($offset, $pref_limit, $count) {

		//todo fix how about $count=false?
		//grab user preferences
		$user_id = (int)$_SESSION['user_id'];
		$user_row = UsersPref_DAO::get($user_id);

		//only if there is a limit set, run this code
		if ($pref_limit != false) {

			$click_query['offset'] = $offset;

			if (is_numeric($offset) and ($pref_limit == true)) {
				//declare starting row number
				$click_query['from'] = ($offset * $user_row['user_pref_limit']) + 1;
			} else {
				$click_query['from'] = 1;
			}

			if ($pref_limit == true) {
				if (is_numeric($pref_limit)) {
					$click_query['limit'] = $pref_limit;
				} else {
					$click_query['limit'] = $user_row['user_pref_limit'];
				}
				//declare the number of pages
				$click_query['pages'] = ceil($count / $user_row['user_pref_limit']) + 1;

				//declare end starting row number
				$click_query['to'] = ($click_query['from'] + $user_row['user_pref_limit']) - 1;
				if ($click_query['to'] > $count) {
					$click_query['to'] = $count;
				}
			} else {
				$click_query['pages'] = 1;
				$click_query['to'] = $count;
			}

			if (($click_query['from'] == 1) and ($click_query['to'] == 0)) {
				$click_query['from'] = 0;
			}
		}

		$click_query['skip'] = $click_query['from'] == 0 ? 0 : $click_query['from'] - 1;
		$click_query['rows'] = $count;

		DU::dump($click_query);
		return $click_query;
	}

	/**
	 * func #109
	 * -used in /tracking202/ajax/click_history.php(1016)
	/ tracking202/visitors/download/index.php(456)
	 *
	 */
	public static function get_history_clicks($pref_time, $pref_adv, $pref_show) {

		// query criteria
		$query = self::get_query($pref_time, $pref_adv, $pref_show);

		DU::dump($query);
		// options for query
		$sort = array('_id' => -1);

		return Db::find(self::_coll, $query, array('sort' => $sort));
	}


	/**
	 * func #
	 * -used in /tracking202/analyze/things_download.php
	 *
	 */
	public static function get_sort_things($coll, $pref_time, $pref_adv, $pref_show, $raw_order) {

		// query criteria
		$query = self::get_query($pref_time, $pref_adv, $pref_show);
		//echo "get_sort_things query=";
		//DU::dump($query);

		// options for query
		// todo fix $raw_order from mysql to array()
		//$sort = array('_id' => -1);
		$sort = self::get_sort_from_raw_order($raw_order);


		DU::dump($query, __FUNCTION__);

		$things = Db::find($coll, $query, array('sort' => $sort));

		return $things;
	}


	/**
	 * mysql order to mongo sort array
	 * @static
	 * @param  $raw_order
	 * @return array
	 */
	static function get_sort_from_raw_order($raw_order) {

		$sort = array();
		if (preg_match('/`?(\w+)`?\s+(ASC|DESC)/', $raw_order, $m)) {
			$o = ($m[2] == 'ASC') ? 1 : -1;
			$sort = array($m[1] => $o);
		} else {
		}

		return $sort;
	}

	/**
	 * func #12
	 * -used in /202-account/administration.php(3879)
	 * -SELECT COUNT(*)
	FROM 202_clicks
	 *
	 * count
	 */
	public static function count() {


		// query criteria
		$query = array();

		return Db::count(self::_coll, $query);
	}


	/**
	 * func #154
	 * -used in /tracking202/redirect/dl.php(7334)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_alp = 'vv.click_alp', click_filtered = 'vv.click_filtered', click_time = 'vv.click_time'
	 *
	 * create by values
	 */
	public static function create_doc_for_dl_by($_values) {

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
		$data = array('_id' => $click_id,
		              'aff_campaign_id' => $aff_campaign_id,
		              'click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0);

		//return Db::insert(self::_coll, $data);
		return $data;
	}


	/**
	 * func #217
	 * -used in /tracking202/static/record_adv.php(6611)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by1 values
	 */
	public static function create_doc_for_adv_by($_values) {
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
		$data = array('_id' => $click_id,
		              'click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'landing_page_id' => $landing_page_id,
		              'ppc_account_id' => $ppc_account_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		//return Db::insert(self::_coll, $data);
		return $data;
	}


	/**
	 * func #223
	 * -used in /tracking202/static/record_simple.php(7183)
	 * -INSERT INTO 202_clicks
	SET click_id = 'vv.click_id', user_id = 'vv.user_id', aff_campaign_id = 'vv.aff_campaign_id', landing_page_id = 'vv.landing_page_id', ppc_account_id = 'vv.ppc_account_id', click_cpc = 'vv.click_cpc', click_payout = 'vv.click_payout', click_filtered = 'vv.click_filtered', click_alp = 'vv.click_alp', click_time = 'vv.click_time'
	 *
	 * create by2 values
	 */
	public static function create_doc_for_simple_by($_values) {
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
		$data = array('_id' => $click_id,
		              'click_alp' => $click_alp,
		              'click_cpc' => $click_cpc,
		              'click_filtered' => $click_filtered,
		              'click_payout' => $click_payout,
		              'click_time' => $click_time,
		              'ppc_account_id' => $ppc_account_id,
		              'aff_campaign_id' => $aff_campaign_id,
		              'landing_page_id' => $landing_page_id,
		              'user_id' => $user_id,
		              'click_lead' => 0
		);

		//return Db::insert(self::_coll, $data);
		return $data;
	}

	/*
	 * store the click adv to db after fill all kinds of data
	 */
	public static function save($click_adv) {

		$click_adv = self::fill_relation_ids($click_adv);

		return Db::save(self::_coll, $click_adv);
	}

	//处理relation
	public static function fill_relation_ids($c_adv) {
		$ids = array('click_id_public',
		             'landing_page_id',
			//'click_referer_site_url_id',
		             'click_landing_site_url_id',
		             'click_outbound_site_url_id',
		             'click_cloaking_site_url_id',
		             'click_redirect_site_url_id');
		foreach ($ids as $k) {
			//if (preg_match('/_id(_public)?$/', $k)) {
			if (!isset($c_adv[$k])) { //if set null or ", will die after later check
				$c_adv[$k] = 0;
			}
			//}
		}

		if (!isset($c_adv['click_referer_site_url_id']) || empty($c_adv['click_referer_site_url_id'])) { //include ppc_a = 0
			$c_adv['click_referer_site_url_id'] = 0;
			$c_adv['referer_site_domain_id'] = 0;
		} else {
			$site_url = SiteUrls_DAO::get($c_adv['click_referer_site_url_id']);
			DU::dump($site_url);
			$c_adv['referer_site_domain_id'] = $site_url['site_domain_id'];
		}

		//不能放入for loop
		if (!isset($c_adv['ppc_account_id']) || empty($c_adv['ppc_account_id'])) { //include ppc_a = 0
			$c_adv['ppc_account_id'] = 0;
			$c_adv['ppc_network_id'] = 0;
		} else {
			$ppc_a = PpcAccounts_DAO::get($c_adv['ppc_account_id']);
			$c_adv['ppc_network_id'] = $ppc_a['ppc_network_id'];
		}

		if (!isset($c_adv['aff_campaign_id']) || empty($c_adv['aff_campaign_id'])) { //include aff_c = 0
			$c_adv['aff_campaign_id'] = 0;
			$c_adv['aff_network_id'] = 0;
		} else {
			$aff_c = AffCampaigns_DAO::get($c_adv['aff_campaign_id']);
			$c_adv['aff_network_id'] = $aff_c['aff_network_id'];
		}

		return $c_adv;
	}

	/**
	 * func #156
	 * -used in /tracking202/redirect/dl.php(8696)
	/tracking202/static/record_adv.php(7967)
	/tracking202/static/record_simple.php(8666)
	 * -INSERT INTO 202_clicks_advance
	SET click_id = 'vv.click_id', text_ad_id = 'vv.text_ad_id', keyword_id = 'vv.keyword_id', ip_id = 'vv.ip_id', platform_id = 'vv.platform_id', browser_id = 'vv.browser_id'
	 *
	 * create by values
	 */
	public static function fill_advance_data($click_adv, $_values) {
		//variables passed
		$text_ad_id = $_values['text_ad_id'];
		$keyword_id = $_values['keyword_id'];
		$ip_id = $_values['ip_id'];
		$platform_id = $_values['platform_id'];
		$browser_id = $_values['browser_id'];

		// object to be created
		$data = array('browser_id' => $browser_id,
		              'ip_id' => $ip_id,
		              'keyword_id' => $keyword_id,
		              'platform_id' => $platform_id,
		              'text_ad_id' => $text_ad_id);

		$click_adv = array_merge($click_adv, $data);
		return $click_adv;
	}

	/**
	 * func #157
	 * -used in /tracking202/redirect/dl.php(9157)
	/tracking202/static/record_adv.php(8428)
	/tracking202/static/record_simple.php(9127)
	 * -INSERT INTO 202_clicks_tracking
	SET click_id = 'vv.click_id', c1_id = 'vv.c1_id', c2_id = 'vv.c2_id', c3_id = 'vv.c3_id', c4_id = 'vv.c4_id'
	 *
	 * create by values
	 */
	public static function fill_tracking_data($click_adv, $_values) {
		//variables passed
		$c1_id = $_values['c1_id'];
		$c2_id = $_values['c2_id'];
		$c3_id = $_values['c3_id'];
		$c4_id = $_values['c4_id'];

		// object to be created
		$data = array('c1_id' => $c1_id,
		              'c2_id' => $c2_id,
		              'c3_id' => $c3_id,
		              'c4_id' => $c4_id);

		$click_adv = array_merge($click_adv, $data);
		return $click_adv;
	}

	/**
	 * func #158, #164
	 * -used in /tracking202/redirect/dl.php(10388)
	/tracking202/static/record_adv.php(9000)
	/tracking202/static/record_simple.php(10226)
	/tracking202/redirect/lp.php(5044)
	 * -INSERT INTO 202_clicks_record
	SET click_id = 'vv.click_id', click_id_public = 'vv.click_id_public', click_cloaking = 'vv.click_cloaking', click_in = 'vv.click_in', click_out = 'vv.click_out'
	 *
	 * create by values
	 */
	public static function fill_record_data($click_adv, $_values) {
		//variables passed
		$click_id_public = $_values['click_id_public'];
		$click_cloaking = $_values['click_cloaking'];
		$click_in = $_values['click_in'];
		$click_out = $_values['click_out'];

		// object to be created
		$data = array('click_cloaking' => $click_cloaking,
		              'click_id_public' => $click_id_public,
		              'click_in' => $click_in,
		              'click_out' => $click_out,
		              'click_reviewed' => 0
		);

		$click_adv = array_merge($click_adv, $data);
		return $click_adv;
	}


	/**
	 * func #165
	 * -used in /tracking202/redirect/lp.php(6488)
	/tracking202/redirect/off.php(7999)
	 * -UPDATE 202_clicks_record
	SET click_out = 'vv.click_out', click_cloaking = 'vv.click_cloaking'
	WHERE click_id = 'vv.click_id'
	 *
	 * zinvalid update by click cloaking, click id, click out
	 */
	public static function delay_update_record_data_by_click_cloaking_and_click_id_and_click_out($click_cloaking, $click_id, $click_out) {

		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('click_cloaking' => $click_cloaking,
		                              'click_out' => $click_out));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


	/**
	 * func #175
	 * -used in /tracking202/redirect/pci.php(772)
	 * -UPDATE 202_clicks_record
	SET click_out = 'vv.click_out'
	WHERE click_id = 'vv.click_id'
	 *
	 * zinvalid update by click id, click out
	 */
	public static function delay_update_record_data_by_click_id_and_click_out($click_id, $click_out) {


		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('click_out' => $click_out));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


	/**
	 * func #159
	 * -used in /tracking202/redirect/dl.php(12086)
	 * -INSERT INTO 202_clicks_site
	SET click_id = 'vv.click_id', click_referer_site_url_id = 'vv.click_referer_site_url_id', click_outbound_site_url_id = 'vv.click_outbound_site_url_id', click_redirect_site_url_id = 'vv.click_redirect_site_url_id'
	 *
	 * create by values
	 */
	public static function fill_site_data($click_adv, $_values) {
		//variables passed
		$click_referer_site_url_id = $_values['click_referer_site_url_id'];
		$click_outbound_site_url_id = $_values['click_outbound_site_url_id'];
		$click_redirect_site_url_id = $_values['click_redirect_site_url_id'];

		// object to be created
		$data = array(
			'click_outbound_site_url_id' => $click_outbound_site_url_id,
			'click_redirect_site_url_id' => $click_redirect_site_url_id,
			'click_referer_site_url_id' => $click_referer_site_url_id);

		$click_adv = array_merge($click_adv, $data);
		return $click_adv;
	}


	/**
	 * func #219
	 * -used in /tracking202/static/record_adv.php(9739)
	/tracking202/static/record_simple.php(11852)
	 * -INSERT INTO 202_clicks_site
	SET click_id = 'vv.click_id', click_referer_site_url_id = 'vv.click_referer_site_url_id', click_landing_site_url_id = 'vv.click_landing_site_url_id', click_outbound_site_url_id = 'vv.click_outbound_site_url_id', click_cloaking_site_url_id = 'vv.click_cloaking_site_url_id', click_redirect_site_url_id = 'vv.click_redirect_site_url_id'
	 *
	 * create by1 values
	 */
	public static function fill_site_data1($click_adv, $_values) {
		//variables passed
		$click_referer_site_url_id = $_values['click_referer_site_url_id'];
		$click_outbound_site_url_id = $_values['click_outbound_site_url_id'];
		$click_redirect_site_url_id = $_values['click_redirect_site_url_id'];
		$click_landing_site_url_id = $_values['click_landing_site_url_id'];
		$click_cloaking_site_url_id = $_values['click_cloaking_site_url_id'];

		//反规范化
		//加入domain id

		// object to be created
		$data = array('click_cloaking_site_url_id' => $click_cloaking_site_url_id,
		              'click_landing_site_url_id' => $click_landing_site_url_id,
		              'click_outbound_site_url_id' => $click_outbound_site_url_id,
		              'click_redirect_site_url_id' => $click_redirect_site_url_id,
		              'click_referer_site_url_id' => $click_referer_site_url_id);

		$click_adv = array_merge($click_adv, $data);
		return $click_adv;
	}


	/**
	 * func #170
	 * -used in /tracking202/redirect/off.php(9219)
	 * -UPDATE 202_clicks_site
	SET click_outbound_site_url_id = 'vv.click_outbound_site_url_id', click_redirect_site_url_id = 'vv.click_redirect_site_url_id'
	WHERE click_id = 'vv.click_id'
	 *
	 * zinvalid update by click id, click outbound site url id, click redirect site url id
	 */
	public static function delay_update_site_data_by_id_and_outbound_site_url_id_and_redirect_site_url_id($click_id, $click_outbound_site_url_id, $click_redirect_site_url_id) {


		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('click_outbound_site_url_id' => $click_outbound_site_url_id,
		                              'click_redirect_site_url_id' => $click_redirect_site_url_id));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


	/**
	 * func #207
	 * -used in /tracking202/static/gpx.php(956)
	/tracking202/static/px.php(855)
	 * -SELECT 202_clicks.click_id
	FROM 202_clicks
	LEFT JOIN 202_clicks_advance USING (click_id)
	LEFT JOIN 202_ips USING (ip_id)
	WHERE 202_ips.ip_address = 'vv.ip_address'
	AND 202_clicks.user_id = 'vv.user_id'
	AND 202_clicks.click_time >= '$daysago'
	ORDER BY 202_clicks.click_id DESC
	LIMIT 1
	 *
	 * find one by daysago, ip address, user id
	 */
	public static function find_one_by_daysago_and_ip_address_and_user_id($daysago, $ip_address, $user_id) {

		$ip = Ips_DAO::find_one_by_address($ip_address);
		$ip_id = $ip['ip_id'];

		// query criteria
		$query = array('click_time' => array('$gte' => $daysago),
		               'user_id' => $user_id,
		               'ip_id' => $ip_id);

		// options for query
		// fields needed
		$fields = array("_id");
		$sort = array('_id' => -1);

		return Db::findOne(self::_coll, $query,
		                   array('fields' => $fields, 'sort' => $sort, 'limit' => 1));
	}


	/**
	 * func #225
	 * -used in /tracking202/update/delete-subids.php(468)
	/tracking202/update/subids.php(550)
	 * -SELECT 2c.click_id
	FROM 202_clicks AS 2c
	WHERE 2c.click_id = 'vv.click_id'
	AND 2c.user_id = 'vv.user_id'
	 *
	 * find one by click id, user id
	 */
	public static function find_one_by_id_and_user_id($click_id, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// options for query
		// fields needed
		$fields = array("_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #150
	 * -used in /tracking202/redirect/cl.php(158)
	 * -SELECT aff_campaign_name, site_url_address
	FROM 202_clicks, 202_clicks_record, 202_clicks_site, 202_site_urls, 202_aff_campaigns
	WHERE 202_clicks.aff_campaign_id = 202_aff_campaigns.aff_campaign_id
	AND 202_clicks.click_id = 202_clicks_record.click_id
	AND 202_clicks_record.click_id_public = 'vv.click_id_public'
	AND 202_clicks_record.click_id = 202_clicks_site.click_id
	AND 202_clicks_site.click_redirect_site_url_id = 202_site_urls.site_url_id
	 *
	 * find one by click id public
	 */
	public static function find_one_by_id_public($click_id_public) {


		// query criteria
		//$query = array('202_clicks.aff_campaign_id' => 202_aff_campaigns . aff_campaign_id,
		//			'202_clicks.click_id' => 202_clicks_record . click_id,
		//			'202_clicks_record.click_id' => 202_clicks_site . click_id,
		//			'202_clicks_record.click_id_public' => $click_id_public,
		//			'202_clicks_site.click_redirect_site_url_id' => 202_site_urls . site_url_id);
		//
		//// options for query
		//// fields needed
		//$fields = array("aff_campaign_name", "site_url_address");
		//
		//return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #174
	 * -used in /tracking202/redirect/pci.php(142)
	 * -SELECT 202_clicks.click_id, 202_clicks.aff_campaign_id, click_cloaking, click_cloaking_site_url_id, click_redirect_site_url_id
	FROM 202_clicks
	LEFT JOIN 202_clicks_record USING (click_id)
	LEFT JOIN 202_clicks_site USING (click_id)
	WHERE click_id_public = 'vv.click_id_public'
	 *
	 * find one by click id public
	 */
	public static function find_one_by_id_public1($click_id_public) {

		// query criteria
		$query = array('click_id_public' => $click_id_public);

		// options for query
		// fields needed
		$fields = array("aff_campaign_id", "click_cloaking", "click_cloaking_site_url_id", "_id", "click_redirect_site_url_id");

		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #63
	 * -used in /202-config/functions-tracking202.php(139729)
	 * -SELECT 2c.click_id, 2tc1.c1, 2tc2.c2, 2tc3.c3, 2tc4.c4
	FROM 202_clicks AS 2c
	LEFT OUTER JOIN 202_clicks_tracking AS 2ct ON (2ct.click_id = 2c.click_id)
	LEFT OUTER JOIN 202_tracking_c1 AS 2tc1 ON (2ct.c1_id = 2tc1.c1_id)
	LEFT OUTER JOIN 202_tracking_c2 AS 2tc2 ON (2ct.c2_id = 2tc2.c2_id)
	LEFT OUTER JOIN 202_tracking_c3 AS 2tc3 ON (2ct.c3_id = 2tc3.c3_id)
	LEFT OUTER JOIN 202_tracking_c4 AS 2tc4 ON (2ct.c4_id = 2tc4.c4_id)
	WHERE 2c.click_id = 'vv.click_id'
	 *
	 * get by idclick id
	 */
	public static function get_cs_names($click_id) {


		// query criteria
		//$query = array('_id' => $click_id);
		$c_adv = self::get($click_id);

		// options for query
		// fields needed
		//$fields = array("c1", "c2", "c3", "c4", "_id");
		$c_adv['c1'] = TrackingC1_DAO::get_name($c_adv['c1_id']);
		$c_adv['c2'] = TrackingC2_DAO::get_name($c_adv['c2_id']);
		$c_adv['c3'] = TrackingC3_DAO::get_name($c_adv['c3_id']);
		$c_adv['c4'] = TrackingC4_DAO::get_name($c_adv['c4_id']);

		return $c_adv;
	}

	public static function get($click_id) { //todo fix not right c1, c2, ...

		// query criteria
		$query = array('_id' => $click_id);

		return Db::findOne(self::_coll, $query);
	}

	/**
	 * func #233
	 * -used in /tracking202/visitors/download/index.php(4120)
	 *  click_history.php
	 * -SELECT 2c.click_id, click_alp, text_ad_name, aff_campaign_name, aff_campaign_id_public, landing_page_nickname, ppc_network_name, ppc_account_name, ip_address, keyword, click_out, click_lead, click_filtered, click_id_public, click_cloaking, click_referer_site_url_id, click_landing_site_url_id, click_outbound_site_url_id, click_cloaking_site_url_id, click_redirect_site_url_id, 202_browsers.browser_id, 202_platforms.platform_id
	FROM 202_clicks AS 2c
	LEFT JOIN 202_clicks_advance USING (click_id)
	LEFT JOIN 202_clicks_record USING (click_id)
	LEFT JOIN 202_clicks_site USING (click_id)
	LEFT JOIN 202_aff_campaigns ON (202_aff_campaigns.aff_campaign_id = 2c.aff_campaign_id)
	LEFT JOIN 202_ppc_accounts ON (202_ppc_accounts.ppc_account_id = 2c.ppc_account_id)
	LEFT JOIN 202_ppc_networks USING (ppc_network_id)
	LEFT JOIN 202_landing_pages ON (202_landing_pages.landing_page_id = 2c.landing_page_id)
	LEFT JOIN 202_text_ads ON (202_text_ads.text_ad_id = 202_clicks_advance.text_ad_id)
	LEFT JOIN 202_ips ON (202_ips.ip_id = 202_clicks_advance.ip_id)
	LEFT JOIN 202_keywords ON (202_keywords.keyword_id = 202_clicks_advance.keyword_id)
	LEFT JOIN 202_browsers ON (202_browsers.browser_id = 202_clicks_advance.browser_id)
	LEFT JOIN 202_platforms ON (202_platforms.platform_id = 202_clicks_advance.platform_id)
	WHERE 2c.click_id = 'vv.click_id'
	 *
	 * get by id2 click id
	 */
	public static function get_click_with_all_related_things($click_id) {


		// query criteria
		$query = array('_id' => $click_id);

		// options for query
		// fields needed
		$fields = array("browser_id", "click_alp", "click_cloaking", "click_cloaking_site_url_id", "click_filtered", "_id", "click_id_public", "click_landing_site_url_id", "click_lead", "click_out", "click_outbound_site_url_id", "click_redirect_site_url_id", "platform_id", "click_referer_site_url_id", "aff_campaign_id", "ppc_account_id", "text_ad_id", "ip_id", "keyword_id", "landing_page_id");

		$c_adv = Db::findOne(self::_coll, $query, $fields);

		/*
		$aff_c = AffCampaigns_DAO::get($c_adv['aff_campaign_id']);
		$c_adv['aff_campaign_name'] = $aff_c['aff_campaign_name'];
		$c_adv['aff_campaign_id_public'] = $aff_c['aff_campaign_id_public'];
		$ppc_a = AffCampaigns_DAO::get($c_adv['ppc_account_id']);
		$c_adv['ppc_network_name'] = $ppc_a['ppc_network_name'];
		$c_adv['ppc_account_name'] = $ppc_a['ppc_account_name'];
		$text_ad = TextAds_DAO::get($c_adv['text_ad_id']);
		$c_adv['text_ad_name'] = $text_ad['text_ad_name'];
		$ip = Ips_DAO::get($c_adv['ip_id']);
		$c_adv['ip_address'] = $ip['ip_address'];
		$keyword = Keywords_DAO::get($c_adv['keyword_id']);
		$c_adv['keyword'] = $keyword['keyword'];
		$lp = LandingPages_DAO::get($c_adv['landing_page_id']);
		$c_adv['landing_page_nickname'] = $lp['landing_page_nickname'];
		*/

		$c_adv_with_name = self::fill_doc_names($c_adv);

		return $c_adv_with_name;
	}


	/**
	 * func #107
	 * -used in /tracking202/ajax/clear_subids.php(652)
	 * -UPDATE 202_clicks
	SET click_lead = '0'
	WHERE user_id = 'vv.user_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * update by aff campaign id, user id
	 */
	public static function update_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id) {


		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_lead' => 0));

		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #108
	 * -used in /tracking202/ajax/clear_subids.php(969)
	 * -UPDATE 202_clicks AS 2c INNER JOIN 202_aff_campaigns AS 2ac ON ( 2c.aff_campaign_id = 2ac.aff_campaign_id
	AND 2ac.aff_network_id = 'vv.aff_network_id')
	SET click_lead = '0'
	WHERE 2c.user_id = 'vv.user_id'
	 *
	 * update by aff network id, user id
	 */
	public static function update_by_aff_network_id_and_user_id($aff_network_id, $user_id) {

		// query criteria
		//$acs = AffCampaigns_DAO::find_by_aff_network_id_and_user_id($aff_network_id, $user_id);
		//$aff_c_ids = array();
		//foreach ($acs as $aff_c) {
		//	$aff_c_ids[] = $aff_c['aff_campaign_id'];
		//}
		$query = array('aff_network_id' => $aff_network_id,
		               'user_id' => $user_id);
		$aff_c_ids = Db::distinct(AffCampaigns_DAO::_coll, '_id', $query);

		// query criteria
		$query = array('user_id' => $user_id,
		               'aff_campaign_id' => array('$in' => $aff_c_ids));

		// object to be updated
		$data = array('$set' => array('click_lead' => 0));

		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #149
	 * -used in /tracking202/ajax/update_cpc2.php(7119)
	 *
	 * update by click cpc, user id
	 */
	public static function update_by_cpc_and_user_id($click_cpc, $user_id, $_values) {

		// query criteria
		$query = array('user_id' => $user_id);
		$query['click_time'] = array('$gte' => $_values['from'], '$lte' => $_values['to']);


		if ($_values['aff_network_id']) {
			$query['aff_network_id'] = $_values['aff_network_id'];
		}
		if ($_values['aff_campaign_id']) {
			$query['aff_campaign_id'] = $_values['aff_campaign_id'];
		}
		if ($_values['text_ad_id']) {
			$query['text_ad_id'] = $_values['text_ad_id'];
		}
		if ($_values['landing_page_id']) {
			$query['landing_page_id'] = $_values['landing_page_id'];
		}
		if ($_values['ppc_network_id']) {
			$query['ppc_network_id'] = $_values['ppc_network_id'];
		}
		if ($_values['ppc_account_id']) {
			$query['ppc_account_id'] = $_values['ppc_account_id'];
		}
		DU::dump($query);

		//method_of_promotion 是query condition
		if (isset($_values['method_of_promotion'])) {
			$query = array_merge($query, $_values['method_of_promotion']);
		}


		// object to be updated
		$data = array('$set' => array('click_cpc' => $click_cpc));

		DU::dump($query);
		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #230
	 * -used in /tracking202/update/upload.php(4514)
	 * -UPDATE 202_clicks
	SET click_lead = '1', click_filtered = '0', click_payout = 'vv.click_payout'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, click payout, user id
	 */
	public static function update_by_id_and_payout_and_user_id($click_id, $click_payout, $user_id) {


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
	 * func #226
	 * -used in /tracking202/update/delete-subids.php(858)
	 * -UPDATE 202_clicks
	SET click_lead = '0', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, user id
	 */
	public static function update_by_id_and_user_id($click_id, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 0));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #228
	 * -used in /tracking202/update/subids.php(982)
	 * -UPDATE 202_clicks
	SET click_lead = '1', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND user_id = 'vv.user_id'
	 *
	 * update by click id, user id
	 */
	public static function update_by_id_and_user_id1($click_id, $user_id) {


		// query criteria
		$query = array('_id' => $click_id,
		               'user_id' => $user_id);

		// object to be updated
		$data = array('$set' => array('click_filtered' => 0,
		                              'click_lead' => 1));

		return Db::updateOne(self::_coll, $query, $data);
	}


	/**
	 * func #205
	 * -used in /tracking202/static/gpb.php(567)
	/tracking202/static/gpx.php(1780)
	 * -UPDATE 202_clicks
	SET click_lead = '1', click_filtered = '0'
	 *	if ($_values['use_pixel_payout'] == 1) {
	click_payout=$_values['click_payout']
	}
	WHERE	click_id='" . $_values['click_id']
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
		$click_payout = empty($click_payout)? 0: $click_payout;
		$data = array('$set' => $sets);

		//echo "gpb update lead";
		//		DU::dump($click_id);
		//		DU::dump($use_pixel_payout);
		//		DU::dump($query);
		//
		//		DU::dump($data);
		return Db::update(self::_coll, $query, $data);
	}


	/**
	 * func #212
	 * -used in /tracking202/static/px.php(1560)
	 * -UPDATE 202_clicks
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


	/**
	 * func #209
	 * -used in /tracking202/static/pb.php(658)
	 * -UPDATE 202_clicks
	SET click_lead = '1', click_filtered = '0'
	WHERE click_id = 'vv.click_id'
	AND aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * zinvalid update by click id, aff campaign id
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
	 * func #169
	 * -used in /tracking202/redirect/off.php(7005)
	 * -UPDATE 202_clicks
	SET 2c.aff_campaign_id = 'vv.aff_campaign_id', 2c.click_payout = 'vv.click_payout'
	WHERE 2c.click_id = 'vv.click_id'
	 *
	 * zinvalid update by click id, click payout, aff campaign id
	 */
	public static function delay_update_by_id_and_payout_and_aff_campaign_id($click_id, $click_payout, $aff_campaign_id) {

		// query criteria
		$query = array('_id' => $click_id);

		// object to be updated
		$data = array('$set' => array('aff_campaign_id' => $aff_campaign_id,
		                              'click_payout' => $click_payout));

		return DelayedCommands_DAO::delay_command(self::_coll, $query, $data);
	}


}