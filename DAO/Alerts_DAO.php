<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: alerts
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Alerts_DAO {
	const _coll = 'alerts';

	/**
	 * func #15
	 * -used in /202-account/ajax/alerts.php(464)
	 * -SELECT COUNT(*) AS count 
			FROM 202_alerts 
			WHERE prosper_alert_id = 'vv.prosper_alert_id'
				AND prosper_alert_seen = '1'
	 *
	 * count by prosper alert id 
	 */
	public static function count_by_prosper_id($prosper_alert_id) {
		

		// query criteria
		$query = array('prosper_alert_id' => $prosper_alert_id,
					'prosper_alert_seen' => 1);
    
		return Db::count(self::_coll, $query);
	}


	/**
	 * func #14
	 * -used in /202-account/ajax/alert-seen.php(192)
	 * -INSERT INTO 202_alerts 
			SET prosper_alert_seen = '1', prosper_alert_id = 'vv.prosper_alert_id'
	 *
	 * create by prosper alert id 
	 */
	public static function create_by_prosper_id($prosper_alert_id) {
		

		// object to be created
		$data = array('prosper_alert_id' => $prosper_alert_id,
					'prosper_alert_seen' => 1);
    
		return Db::insert(self::_coll, $data);
	}



}