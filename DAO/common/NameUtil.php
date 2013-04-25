<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/DebugUtil.php');

/**
 * prosper中view层和db层字段的映射处理
 */

class NameUtil {


	public static function get_id_name($_coll) {

		switch ($_coll) {
			case 'users_pref':
				$coll_doc_id_name = 'user_id';
				break;
			case 'clicks_advance':
			case 'clicks_spy':
				$coll_doc_id_name = 'click_id';
				break;
			case 'summary_overview':
				$coll_doc_id_name = '_id';
				break;
			case 'tracking_c1':
			case 'tracking_c2':
			case 'tracking_c3':
			case 'tracking_c4':
				$coll_doc_id_name = substr($_coll, 9) . '_id';
				break;
			default:
				$coll_doc_id_name = preg_replace('/s$/', '_id', $_coll);
		}
		return $coll_doc_id_name;
	}


	/**
	 * 从id名字获得name的列名字，如 ip_id -> ip_address, text_ad_id -> text_ad_name
	 * @static
	 * @param  $_id_name
	 * @return mixed|string
	 */
	public static function get_doc_name_by_id_name($_id_name) {

		switch ($_id_name) {
			case 'landing_page_id':
				$doc_name = "landing_page_nickname";
				break;
			case 'referer_site_domain_id':
			case 'site_domain_id':
				$doc_name = "site_domain_host";
				break;
			case 'site_url_id':
				$doc_name = "site_url_address";
				break;
			case 'ip_id':
				$doc_name = "ip_address";
				break;
			case 'keyword_id':
				$doc_name = "keyword";
				break;
			case 'c1_id':
			case 'c2_id':
			case 'c3_id':
			case 'c4_id':
				$doc_name = substr($_id_name, 0, 2);
				break;
			default:
				$doc_name = preg_replace('/_id$/', '_name', $_id_name);
		}
		return $doc_name;
	}


	/**
	 * 将_id换成doc_id形式
	 * @param  $doc doc
	 * @return 改变了_id之后的doc
	 */
	public static function replace_id($doc, $_coll) {
		if (is_null($doc) || !isset($doc['_id'])) {
			//echo "null or not include _id";
			//var_dump($doc);
			return $doc;
		}

		$coll_doc_id_name = self::get_id_name($_coll);

		$doc[$coll_doc_id_name] = $doc['_id'];
		unset($doc['_id']);

		//var_dump($doc);
		return $doc;
	}


	/**
	 * 根据文档名称的字段名获得文档对应的collection名
	 * @static
	 * @param  $_id_name
	 * @return mixed|string
	 */
	public static function get_coll_name_by_doc_name($name_str) {

		if (preg_match('/(\w+)_(name|nickname|address|host)$/', $name_str, $m)) {
			$coll_name = $m[1] . "s";
		} elseif ("keyword" == $name_str) {
			$coll_name = Keywords_DAO::_coll;
		} else {
			//todo 上线时可移去此处异常检查
			assert(false);
			die("NameCatcher.get: why here? name_str =" . $name_str);
			
			$coll_name = "";
		}

		return $coll_name;
	}


	//not used
	public static function get_coll_name_by_id_name($_id_name) {

		switch ($_id_name) {
			case 'c1_id':
			case 'c2_id':
			case 'c3_id':
			case 'c4_id':
				$coll_name = "tracking_" . substr($_id_name, 0, 2);
				break;
			default:
				$coll_name = preg_replace('/_id$/', 's', $_id_name);
		}
		return $coll_name;
	}

	//用于处理合理sort表相关的null
	static function sort_value_null_to_0($sort_a) {
		if (!is_array($sort_a)) {
			return $sort_a;
		}
		foreach ($sort_a as $k => $v) {
			//if ($v != 0 && $v != array() && empty($v)) {
			if (preg_match('/sort_\w+_(?!(from|to)$)/s', $k)) {
				if ($v === null || $v === '') {
					//echo "$k=$v need set to 0 ";
					$sort_a[$k] = 0;
				}
			}
		}
		return $sort_a;
	}

	static function empty_to_0($v) {

		if ($v === null || $v === '') {
			$v = 0;
		}
		return $v;
	}


	static $clicks_advance_fields_encode_map = array(
		'_id' => '_id',
		'user_id' => 'u_k',
		'click_lead' => 'lead',
		'click_alp' => 'alp',
		'click_cpc' => 'cpc',
		'click_filtered' => 'filt',
		'click_payout' => 'pay',
		'click_time' => 't',
		'click_cloaking' => 'cloak',
		'click_id_public' => 'k_pub',
		'click_in' => 'cin',
		'click_out' => 'cout',
		'click_reviewed' => 'rev',
		'click_outbound_site_url_id' => 'o_url',
		'click_redirect_site_url_id' => 'rt_url',
		'click_referer_site_url_id' => 'rr_url',
		'click_landing_site_url_id' => 'land_url',
		'click_cloaking_site_url_id' => 'cloak_url',
		'referer_site_domain_id' => 'rr_site',
		'text_ad_id' => 'ad',
		'landing_page_id' => 'lp',
		'aff_campaign_id' => 'affc',
		'aff_network_id' => 'affn',
		'ppc_network_id' => 'ppcn',
		'ppc_account_id' => 'ppca',
		'browser_id' => 'br',
		'ip_id' => 'ip',
		'keyword_id' => 'kw',
		'platform_id' => 'os',
		'c1_id' => 'c1',
		'c2_id' => 'c2',
		'c3_id' => 'c3',
		'c4_id' => 'c4'
	);


	public static function encode_click_adv_doc($collection, $doc) {
		if (ClicksAdvance_DAO::_coll != $collection) {
			return $doc;
		}

		//DU::dump($doc);
		$doc_r = array();
		foreach ($doc as $k => $v) {
			//if(is_int($k)) return $doc; //对于多维数组中的sub doc为一维数组
			$k2 = array_key_exists($k, self::$clicks_advance_fields_encode_map) ?
							self::$clicks_advance_fields_encode_map[$k] : $k;
			if (is_array($v)) {
				$v = self::encode_click_adv_doc($collection, $v);
			}
			$doc_r[$k2] = $v;
		}

		//DU::dump($doc_r);
		return $doc_r;
	}

	/**
	 * @static 对一维数组处理
	 * @param  $doc
	 * @return array
	 */
	public static function encode_click_adv_doc_simple($collection, $doc) {
		if (ClicksAdvance_DAO::_coll != $collection) {
			return $doc;
		}

		DU::dump($doc);

		if (is_string($doc)) {
			$doc2 = self::$clicks_advance_fields_encode_map[$doc];
			//group report needs this special check for its using c1, c2, ...
			return empty($doc2) ? $doc : $doc2;
		}

		$doc_r = array();
		foreach ($doc as $k) {
			$k2 = self::$clicks_advance_fields_encode_map[$k];
			$doc_r[] = $k2;
		}

		DU::dump($doc_r);
		return $doc_r;
	}

	public static function decode_click_adv_doc($collection, $doc) {

		if (empty($doc)) {
			return $doc;
		}
		if (ClicksAdvance_DAO::_coll != $collection) {
			return $doc;
		}

		DU::dump($doc);
		$doc_r = array();
		foreach ($doc as $k => $v) {
			$k2 = self::$clicks_advance_fields_decode_map[$k];
			$doc_r[$k2] = $v;
		}

		DU::dump($doc_r);
		return $doc_r;
	}


	/**
	 * 避免简写和mongo的一些保留字冲突
	 */
	static $clicks_advance_fields_decode_map = array(
		'_id' => '_id',
		'u_k' => 'user_id',
		'lead' => 'click_lead',
		'alp' => 'click_alp',
		'cpc' => 'click_cpc',
		'filt' => 'click_filtered',
		'pay' => 'click_payout',
		't' => 'click_time',
		'cloak' => 'click_cloaking',
		'k_pub' => 'click_id_public',
		'in' => 'click_in',
		'out' => 'click_out',
		'rev' => 'click_reviewed',
		'o_url' => 'click_outbound_site_url_id',
		'rt_url' => 'click_redirect_site_url_id',
		'rr_url' => 'click_referer_site_url_id',
		'land_url' => 'click_landing_site_url_id',
		'cloak_url' => 'click_cloaking_site_url_id',
		'rr_site' => 'referer_site_domain_id',
		'ad' => 'text_ad_id',
		'lp' => 'landing_page_id',
		'affc' => 'aff_campaign_id',
		'affn' => 'aff_network_id',
		'ppcn' => 'ppc_network_id',
		'ppca' => 'ppc_account_id',
		'br' => 'browser_id',
		'ip' => 'ip_id',
		'kw' => 'keyword_id',
		'os' => 'platform_id',
		'c1' => 'c1_id',
		'c2' => 'c2_id',
		'c3' => 'c3_id',
		'c4' => 'c4_id'
	);


	/**
	 * 将group report中别名（用于report summary form的已有函数）
	 * 转为clicks advance中对应的id
	 * @static
	 * @param  $keys
	 * @return
	 */
	public static function translate_group_overview_id_to_clicks_advance_id($keys) {

		//DU::dump($keys, __FUNCTION__);

		$c_adv_ks = array();
		foreach ($keys as $k) {

			$c_adv_k = $k;

			switch ($k) {
				case 'affiliate_network_id':
					$c_adv_k = 'aff_network_id';
					break;
				case 'affiliate_campaign_id':
					$c_adv_k = 'aff_campaign_id';
					break;
				case 'referer_id':
					$c_adv_k = 'click_referer_site_url_id';
					break;
				case 'redirect_id':
					$c_adv_k = 'click_redirect_site_url_id';
					break;
			}
			$c_adv_ks[] = $c_adv_k;
		}

		//DU::dump($c_adv_ks, __FUNCTION__);

		return $c_adv_ks;
	}

	//处理group的别名，用于report summary form的已有函数
	public static function fill_group_overview_alias($aggre_doc) {

		//DU::dump($aggre_doc, __FUNCTION__);

		foreach ($aggre_doc as $k => $v) {
			switch ($k) {
				case 'aff_network_id':
					$aggre_doc['affiliate_network_id'] = $aggre_doc['aff_network_id'];
					unset($aggre_doc['aff_network_id']);
					break;
				case 'aff_network_name':
					$aggre_doc['affiliate_network_name'] = $aggre_doc['aff_network_name'];
					unset($aggre_doc['aff_network_name']);
					break;
				case 'aff_campaign_id':
					$aggre_doc['affiliate_campaign_id'] = $aggre_doc['aff_campaign_id'];
					unset($aggre_doc['aff_campaign_id']);
					break;
				case 'aff_campaign_name':
					$aggre_doc['affiliate_campaign_name'] = $aggre_doc['aff_campaign_name'];
					unset($aggre_doc['aff_campaign_name']);
					break;
				case 'landing_page_nickname':
					$aggre_doc['landing_page_name'] = $aggre_doc['landing_page_nickname'];
					unset($aggre_doc['landing_page_nickname']);
					break;
				case 'keyword':
					$aggre_doc['keyword_name'] = $aggre_doc['keyword'];
					unset($aggre_doc['keyword']);
					break;
				case 'ip_address':
					$aggre_doc['ip_name'] = $aggre_doc['ip_address'];
					unset($aggre_doc['ip_address']);
					break;
				case 'click_referer_site_url_id':
					$aggre_doc['referer_id'] = $aggre_doc['click_referer_site_url_id'];
					$addr = NameCatcher::get('site_url_address', $aggre_doc['click_referer_site_url_id']);
					$aggre_doc['referer_name'] = $addr;
					unset($aggre_doc['click_referer_site_url_id']);
					break;
				case 'site_url_address':
					unset($aggre_doc['site_url_address']);
					break;
				case 'click_redirect_site_url_id':
					$aggre_doc['redirect_id'] = $aggre_doc['click_redirect_site_url_id'];
					$addr = NameCatcher::get('site_url_address', $aggre_doc['click_redirect_site_url_id']);
					$aggre_doc['redirect_name'] = $addr;
					unset($aggre_doc['click_redirect_site_url_id']);
			}
		}

		return $aggre_doc;

	}


}
