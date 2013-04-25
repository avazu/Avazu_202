<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: delayed_sqls
 *  - Time: March 18, 2011, 3:39 am
 */


class DelayedCommands_DAO {
	const _coll = 'delayed_commands';

	/**
	 * func #59
	 * -used in /202-config/functions-tracking202.php(137515)
	 * -INSERT INTO 202_delayed_sqls
	SET delayed_command = 'vv.delayed_command', delayed_time = 'vv.delayed_time'
	 *
	 * create by delayed sql, delayed time
	 */
	public static function delay_command($coll_name, $query, $data) {
		assert(!empty($query['_id']));

		// object to be created
		$data = array('coll_name' => $coll_name,
		              'query' => $query,
		              'data' => $data,
		              'delayed_time' => time());

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #81
	 * -used in /202-cronjobs/index.php(5550)
	 * -SELECT delayed_command
	FROM 202_delayed_sqls
	WHERE delayed_time <= '$time'
	 *
	 * find by time
	 */
	public static function find_by_time($time) {


		// query criteria
		$query = array('delayed_time' => array('$lte' => $time));

		// options for query
		// fields needed
		//$fields = array("delayed_command");

		return Db::find(self::_coll, $query);
	}


	/**
	 * func #82
	 * -used in /202-cronjobs/index.php(5975)
	 * -DELETE
	FROM 202_delayed_sqls
	WHERE delayed_time <= '$time'
	 *
	 * remove by time
	 */
	public static function remove_by_time($time) {

		// query criteria
		$query = array('delayed_time' => array('$lte' => $time));

		return Db::remove(self::_coll, $query);
	}

	/**
	 *called by cron jobs
	 */
	public static function run_delayed_commands($time) {
		$delayed_result = self::find_by_time($time);
		while ($delayed_row = $delayed_result->getNext()) {
			//foreach ($delayed_result as $id => $delayed_row) {
			//echo "id=".$id;
			//DU::dump($delayed_row, __FUNCTION__);
			//die();
			//run each sql
			$coll_name = $delayed_row['coll_name'];
			assert(!empty($coll_name));
			$query = $delayed_row['query'];
			$data = $delayed_row['data'];

			Db::update($coll_name, $query, $data);
		}

		//delete all old delayed commands
		self::remove_by_time($time);
	}

	public static function delay_update_command($coll_name, $query, $data) {


		Db::update($coll_name, $query, $data);
	}

}