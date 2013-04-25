<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: suggestions
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Suggestions_DAO {
	const _coll = 'suggestions';

	/**
	 * func #28
	 * -used in /202-config/functions-tracking202.php(56960)
	 * -SELECT * 
			FROM suggestions 
			WHERE suggestion_reply_to_id = 'vv.suggestion_id'
	 *
	 * find by suggestion id 
	 */
	public static function find_by_id($suggestion_id) {
		

		// query criteria
		$query = array('suggestion_reply_to_id' => $suggestion_id);
    
		return Db::find(self::_coll, $query);
	}


	/**
	 * func #29
	 * -used in /202-config/functions-tracking202.php(57541)
	 * -SELECT * 
			FROM suggestions 
			WHERE suggestion_reply_to_id = 'vv.suggestion_reply_to_id' 
			ORDER BY suggestion_votes DESC
	 *
	 * find by suggestion reply to id 
	 */
	public static function find_by_reply_to_id($suggestion_reply_to_id) {
		

		// query criteria
		$query = array('suggestion_reply_to_id' => $suggestion_reply_to_id);

		// options for query
		$sort = array('suggestion_votes' => -1);
    
		return Db::find(self::_coll, $query, array('sort' => $sort));
	}



}