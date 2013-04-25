<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sessions
 *  - Time: March 18, 2011, 3:39 am
 */


class Sessions_DAO {
  const _coll = 'sessions';

  /**
   * func #73
   * -used in /202-config/sessions.php(956)
   * -SELECT session_data
  FROM 202_sessions
  WHERE session_id = '$newid'
  AND expires > '$time'
   *
   * find one by newid, time
   */
  public static function find_one_by_newid_and_time($newid, $time) {


    // query criteria
    $query = array('expires' => array('$gt' => $time),
                   '_id' => $newid);

    // options for query
    // fields needed
    $fields = array("session_data");

    return Db::findOne(self::_coll, $query, $fields);
  }


  /**
   * func #74
   * -used in /202-config/sessions.php(1733)
   * -DELETE
  FROM 202_sessions
  WHERE session_id = '$newid'
   *
   * remove by newid
   */
  public static function remove_by_newid($newid) {


    // query criteria
    $query = array('_id' => $newid);

    return Db::remove(self::_coll, $query);
  }


  /**
   * func #74
   * -used in /202-config/sessions.php(1733)
   * -DELETE
  FROM 202_sessions
  WHERE session_id = '$newid'
   *
   * remove by newid
   */
  public static function replace($newid, $newdata, $time) {

    // object to be created/updated
    $data = array('_id' => $newid,
                  'session_data' => $newdata,
                  'expires' => $time);

    //return Db::insert(self::_coll, $data);
    $db = self::getDb(self::_coll, false);
    $res = $db->command(array('findandmodify' => self::_coll,
                             'query' => array('_id' => $newid),
                             'update' => $data,
                             'new' => TRUE,
                             'upsert' => TRUE));
    return $res;
  }


}