<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: locations
 *  - Time: March 18, 2011, 3:39 am
 *
 * location not used
 */


class Locations_DAO {
	//const _coll = 'locations';
	//const _coll = 'locations_city';
	//const _coll = 'locations_coordinates';
	//const _coll = 'locations_country';
	//const _coll = 'locations_region';

	/**
	 * func #65
	 * -used in /202-config/functions.php(5060)
	 */
	public static function is_installed() {
		
		return false;

		/*
		if ($count != 161877) {
			return false;
		}

		$count = LocationsBlock_DAO::count();
		if ($count != 1593228) {
			return false;
		}

		$count = LocationsCity_DAO::count();
		if ($count != 101332) {
			return false;
		}

		$count = LocationsCoordinates_DAO::count();
		if ($count != 125204) {
			return false;
		}

		$count = LocationsCountry_DAO::count();
		if ($count != 235) {
			return false;
		}

		$count = LocationsRegion_DAO::count();
		if ($count != 396) {
			return false;
		}

		#if no return false
		return true;
		*/
	}

}