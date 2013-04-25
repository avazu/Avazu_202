<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: charts
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Charts_DAO {
	const _coll = 'charts';

	/**
	 * func #49
	 * -used in /202-config/functions-tracking202.php(91522)
	 * -INSERT INTO 202_charts 
			SET chart_xml = 'vv.chart_xml'
	 *
	 * create by chart xml 
	 */
	public static function create_by_xml($chart_xml) {
		

		// object to be created
		$data = array('chart_xml' => $chart_xml);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #18
	 * -used in /202-charts/showChart.php(142)
	 * -SELECT chart_xml 
			FROM 202_charts 
			WHERE chart_id = 'vv.chart_id'
	 *
	 * get by idchart id 
	 */
	public static function get($chart_id) {
		

		// query criteria
		$query = array('_id' => $chart_id);

		// options for query
		// fields needed
		$fields = array("chart_xml");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #79
	 * -used in /202-cronjobs/index.php(2008)
	 * -DELETE 
			FROM 202_charts
	 *
	 * remove 
	 */
	public static function remove() {
		

		// query criteria
		$query = array();
    
		return Db::remove(self::_coll, $query);
	}



}