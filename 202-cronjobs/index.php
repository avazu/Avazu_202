<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');


//heres the psuedo cronjobs
if (RunSecondsCronjob() == true) {
	if (RunHourlyCronJob() == true) {
		RunDailyCronjob();
	}
}

function RunDailyCronjob() {
	//check to run the daily cronjob
	$now = time();

	$today_day = date('j', time());
	$today_month = date('n', time());
	$today_year = date('Y', time());

	//the click_time is recorded in the middle of the day
	$cronjob_time = mktime(12, 0, 0, $today_month, $today_day, $today_year);
	$_values['cronjob_time'] = $cronjob_time;
	$_values['cronjob_type'] = 'daily';

	//check to make sure this click_summary doesn't already exist

	$cronjob_type = $_values['cronjob_type'];
	$cronjob_time = $_values['cronjob_time'];
	$check_result = Cronjobs_DAO::count_by_time_and_type($cronjob_time, $cronjob_type);
	$check_count = $check_result;



	if ($check_count == 0) {

		//if a cronjob hasn't run today, record it now.

		$cronjob_type = $_values['cronjob_type'];
		$cronjob_time = $_values['cronjob_time'];
		$insert_result = Cronjobs_DAO::create_by_time_and_type($cronjob_time, $cronjob_type);




		/* -------- THIS CLEARS OUT THE CLICK SPY MEMORY TABLE --------- */
		//this function runs everyday at midnight to clear out the temp clicks_memory table
		$from = time() - 86400;

		//this makes it so we only have the most recent last 24 hour stuff, anything older, kill it.
		//we want to keep our SPY TABLE, low

		//$click_result = ClicksSpy_DAO::remove_by_from($from);



		//clear the last 24 hour ip addresses

		$last_ip_result = LastIps_DAO::remove_by_from($from);



		/* -------- THIS CLEARS OUT THE CHART TABLE --------- */

		$chart_result = Charts_DAO::remove();



		/* -------- NOW DELETE ALL THE OLD CRONJOB ENTRIES STUFF --------- */
		$_values['cronjob_time'] = $_values['cronjob_time'] - 86400;

		$cronjob_time = $_values['cronjob_time'];
		$delete_result = Cronjobs_DAO::remove_by_time($cronjob_time);




		return true;
	} else {
		return false;
	}
}


function RunHourlyCronJob() {
	//check to run the daily cronjob, not currently in-use
	$now = time();

	$today_day = date('j', time());
	$today_month = date('n', time());
	$today_year = date('Y', time());
	$today_hour = date('G', time());

	//the click_time is recorded in the middle of the day
	$cronjob_time = mktime($today_hour, 0, 0, $today_month, $today_day, $today_year);
	$_values['cronjob_time'] = $cronjob_time;
	$_values['cronjob_type'] = 'hour';

	//check to make sure this click_summary doesn't already exist

	$cronjob_type = $_values['cronjob_type'];
	$cronjob_time = $_values['cronjob_time'];
	$check_result = Cronjobs_DAO::count_by_time_and_type($cronjob_time, $cronjob_type);
	$check_count = $check_result;



	if ($check_count == 0) {
		/*
		//if a cronjob hasn't run today, record it now.
		
$cronjob_type = $_values['cronjob_type'];
$cronjob_time = $_values['cronjob_time'];
$insert_result = Cronjobs_DAO::create_by_time_and_type($cronjob_time, $cronjob_type);



		/* -------- CURL THE WEBSITES TO SEE IF UP OR NOT --------- */
		/*
		$c = new curl("http://party202.com") ;
		$c->setopt(CURLOPT_FOLLOWLOCATION, true) ;
		echo $c->exec() ;
		if ($theError = $c->hasError()) {  
			
			mail('5034444444@mobile.att.net','Server Down',$theError);
			echo $theError ; 
		
		}
		$c->close() ;
		*/
		return true;
	} else {
		return false;
	}
}


function RunSecondsCronjob() {

	//check to run the 1minute cronjob, change this to every minute
	$now = time();

	$everySeconds = 20;

	//check to run the 1minute cronjob, change this to every minute
	$now = time();

	$today_second = date('s', time());
	$today_minute = date('i', time());
	$today_hour = date('G', time());
	$today_day = date('j', time());
	$today_month = date('n', time());
	$today_year = date('Y', time());

	$today_second = ceil($today_second / $everySeconds);
	if ($today_second == 0) {
		$today_second++;
	}

	//the click_time is recorded in the middle of the day
	$cronjob_time = mktime($today_hour, $today_minute, $today_second, $today_month, $today_day, $today_year);

	$_values['cronjob_time'] = $cronjob_time;
	$_values['cronjob_type'] = 'secon';

	//check to make sure this click_summary doesn't already exist

	$cronjob_type = $_values['cronjob_type'];
	$cronjob_time = $_values['cronjob_time'];
	$check_result = Cronjobs_DAO::count_by_time_and_type($cronjob_time, $cronjob_type);
	$check_count = $check_result;



	if ($check_count == 0) {

		//if a cronjob hasn't run today, record it now.

		$insert_result = Cronjobs_DAO::create_by_time_and_type($cronjob_time, $cronjob_type);




		/* -------- THIS RUNS THE DELAYED QUERIES --------- */
		$time = time();
		$delayed_result = DelayedCommands_DAO::run_delayed_commands($time);


		return true;
	} else {
		return false;
	}
}
