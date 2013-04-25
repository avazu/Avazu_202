<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: suggestion_votes
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SuggestionVotes_DAO {
	const _coll = 'suggestion_votes';

	/**
	 * func #26
	 * -used in /202-config/functions-tracking202.php(51761)
	 * -SELECT COUNT(*) 
			FROM suggestion_votes 
			WHERE user_id = 'vv.user_id'
				AND suggestion_id = 'vv.suggestion_id'
	 *
	 * count by suggestion id, user id 
	 */
	public static function count_by_suggestion_id_and_user_id($suggestion_id, $user_id) {
		

		// query criteria
		$query = array('suggestion_id' => $suggestion_id,
					'user_id' => $user_id);
    
		return Db::count(self::_coll, $query);
	}



}