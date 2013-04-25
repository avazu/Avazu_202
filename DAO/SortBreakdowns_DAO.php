<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_breakdowns
 *  - Time: March 18, 2011, 3:39 am
 */


class SortBreakdowns_DAO {
  const _coll = 'sort_breakdowns';

  /**
   * func #53
   * -used in /202-config/functions-tracking202.php(97812)
  /202-config/functions-tracking202.php(111686)
  /202-config/functions-tracking202.php(124856)
   * -INSERT INTO 202_sort_breakdowns
  SET sort_breakdown_from = 'vv.from', sort_breakdown_to = 'vv.to', user_id = 'vv.user_id', sort_breakdown_clicks = 'vv.clicks', sort_breakdown_leads = 'vv.leads', sort_breakdown_su_ratio = 'vv.su_ratio', sort_breakdown_payout = 'vv.sort_breakdown_payout', sort_breakdown_epc = 'vv.epc', sort_breakdown_avg_cpc = 'vv.avg_cpc', sort_breakdown_income = 'vv.income', sort_breakdown_cost = 'vv.cost', sort_breakdown_net = 'vv.net', sort_breakdown_roi = 'vv.roi'
   *
   * create by values
   */
  public static function create_by($_values) {
    //variables passed
    $from = $_values['from'];
    $to = $_values['to'];
    $user_id = $_values['user_id'];
    $clicks = $_values['clicks'];
    $leads = $_values['leads'];
    $su_ratio = $_values['su_ratio'];
    $sort_breakdown_payout = isset($_values['sort_breakdown_payout']) ? $_values['sort_breakdown_payout'] : 0.0;
    $epc = $_values['epc'];
    $avg_cpc = $_values['avg_cpc'];
    $income = $_values['income'];
    $cost = $_values['cost'];
    $net = $_values['net'];
    $roi = $_values['roi'];

    // object to be created
    $data = array('user_id' => $user_id,
                  'sort_breakdown_from' => $from,
                  'sort_breakdown_to' => $to,
                  'sort_breakdown_avg_cpc' => $avg_cpc,
                  'sort_breakdown_clicks' => $clicks,
                  'sort_breakdown_cost' => $cost,
                  'sort_breakdown_epc' => $epc,
                  'sort_breakdown_income' => $income,
                  'sort_breakdown_leads' => $leads,
                  'sort_breakdown_net' => $net,
                  'sort_breakdown_payout' => $sort_breakdown_payout,
                  'sort_breakdown_roi' => $roi,
                  'sort_breakdown_su_ratio' => $su_ratio);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);

    return Db::insert(self::_coll, $data);
  }


  /**
   * func #125
   * -used in /tracking202/ajax/sort_breakdown.php(7927)
  /tracking202/ajax/sort_hourly.php(7889)
  /tracking202/ajax/sort_weekly.php(7893)
   * -SELECT *
  FROM 202_sort_breakdowns
  WHERE user_id = 'vv.user_id'
  ORDER BY 'vv.order'
   *
   * find by order, user id
   */
  public static function find_by_order_and_user_id($raw_order, $user_id) {

    // query criteria
    $query = array('user_id' => $user_id);

    // options for query
    $sort = ClicksAdvance_DAO::get_sort_from_raw_order($raw_order);

    return Db::find(self::_coll, $query, array('sort' => $sort));
  }


  /**
   * func #54
   * -used in /202-config/functions-tracking202.php(98741)
   * -SELECT *
  FROM 202_sort_breakdowns
  WHERE user_id = 'vv.user_id'
   *
   * find by user id
   */
  public static function find_array_by_user_id1($user_id) {


    // query criteria
    $query = array('user_id' => $user_id);

    return Db::finda(self::_coll, $query);
  }


  /**
   * func #57
   * -used in /202-config/functions-tracking202.php(112541)
  /202-config/functions-tracking202.php(125709)
   * -SELECT *
  FROM 202_sort_breakdowns
  WHERE user_id = 'vv.user_id'
  ORDER BY sort_breakdown_from ASC
   *
   * find by user id
   */
  public static function find_array_by_user_id($user_id) {


    // query criteria
    $query = array('user_id' => $user_id);

    // options for query
    $sort = array('sort_breakdown_from' => 1);

    return Db::finda(self::_coll, $query, array('sort' => $sort));
  }


  /**
   * func #51
   * -used in /202-config/functions-tracking202.php(93072)
  /202-config/functions-tracking202.php(107140)
  /202-config/functions-tracking202.php(120085)
   * -DELETE
  FROM 202_sort_breakdowns
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