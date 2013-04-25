<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: keywords
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Keywords_DAO {
	const _coll = 'keywords';

	/**
	 * func #39
	 * -used in /202-config/functions-tracking202.php(87282)
	 * -INSERT INTO 202_keywords 
			SET keyword = 'vv.keyword'
	 *
	 * create by keyword 
	 */
	public static function create_by_keyword($keyword) {
		

		// object to be created
		$data = array('keyword' => $keyword);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #38
	 * -used in /202-config/functions-tracking202.php(86846)
	 * -SELECT keyword_id 
			FROM 202_keywords 
			WHERE keyword = 'vv.keyword'
	 *
	 * find one by keyword 
	 */
	public static function find_one_by_keyword($keyword) {
		

		// query criteria
		$query = array('keyword' => $keyword);

		// options for query
		// fields needed
		$fields = array("_id");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	public static function get($keyword_id) {
		return Db::findOne(self::_coll, $keyword_id);
	}


}