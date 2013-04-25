<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: rotations
 *  - Time: March 18, 2011, 3:39 am
 */

 
class Rotations_DAO {
	const _coll = 'rotations';

	/**
	 * func #62
	 * -used in /202-config/functions-tracking202.php(139178)
	 * -INSERT INTO 202_rotations 
			SET aff_campaign_id = 'vv.aff_campaign_id', rotation_num = 'vv.num'
	 *
	 * create by aff campaign id, num 
	 */
	public static function create_by_aff_campaign_id_and_num($aff_campaign_id, $num) {
		

		// object to be created
		$data = array('aff_campaign_id' => $aff_campaign_id,
					'rotation_num' => $num);
    
		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #60
	 * -used in /202-config/functions-tracking202.php(138513)
	 * -SELECT rotation_num 
			FROM 202_rotations 
			WHERE aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * find one by aff campaign id 
	 */
	public static function find_one_by_aff_campaign_id($aff_campaign_id) {
		

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id);

		// options for query
		// fields needed
		$fields = array("rotation_num");
    
		return Db::findOne(self::_coll, $query, $fields);
	}


	/**
	 * func #61
	 * -used in /202-config/functions-tracking202.php(138906)
	 * -UPDATE 202_rotations 
			SET rotation_num = 'vv.num'
			WHERE aff_campaign_id = 'vv.aff_campaign_id'
	 *
	 * update by aff campaign id, num 
	 */
	public static function update_by_aff_campaign_id_and_num($aff_campaign_id, $num) {
		

		// query criteria
		$query = array('aff_campaign_id' => $aff_campaign_id);

		// object to be updated
		$data = array('$set' =>array('rotation_num' => $num) );
    
		return Db::update(self::_coll, $query, $data);
	}



}