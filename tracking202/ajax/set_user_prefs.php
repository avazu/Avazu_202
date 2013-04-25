<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

//set the timezone for the user, for entering their dates.
AUTH::set_timezone($_SESSION['user_timezone']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//start - update user user_preferences
	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['user_pref_adv'] = (int)$_POST['user_pref_adv'];
	$_values['user_pref_ppc_network_id'] = (int)$_POST['ppc_network_id'];
	$_values['user_pref_ppc_account_id'] = (int)$_POST['ppc_account_id'];
	$_values['user_pref_aff_network_id'] = (int)$_POST['aff_network_id'];
	$_values['user_pref_aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
	$_values['user_pref_text_ad_id'] = (int)$_POST['text_ad_id'];
	$_values['user_pref_method_of_promotion'] = (string)$_POST['method_of_promotion'];
	$_values['user_pref_landing_page_id'] = (int)$_POST['landing_page_id'];
	$_values['user_pref_country_id'] = (int)$_POST['country_id'];
	$_values['user_pref_ip'] = (string)$_POST['ip'];
	$_values['user_pref_referer'] = (string)$_POST['referer'];
	$_values['user_pref_keyword'] = (string)$_POST['keyword'];
	$_values['user_pref_limit'] = (int)$_POST['user_pref_limit'];
	$_values['user_pref_breakdown'] = (string)$_POST['user_pref_breakdown'];
	$_values['user_pref_chart'] = (string)$_POST['user_pref_chart'];
	$_values['user_cpc_or_cpv'] = (string)$_POST['user_cpc_or_cpv'];
	$_values['user_pref_show'] = (string)$_POST['user_pref_show'];
	if (is_array($_POST['details'])) {
		foreach ($_POST['details'] AS $key => $value) {
			$_values['user_pref_group_' . ($key + 1)] = (int)$value;
		}
	}
}

//predefined timelimit set, set the options
if ($_POST['user_pref_time_predefined'] != '') {
	switch ($_POST['user_pref_time_predefined']) {
		case 'today';
		case 'yesterday';
		case 'last7';
		case 'last14';
		case 'last30';
		case 'thismonth';
		case 'lastmonth';
		case 'thisyear';
		case 'lastyear';
		case 'alltime';
			$clean['user_pref_time_predefined'] = (string)$_POST['user_pref_time_predefined'];
			break;
	}

	if (!isset($clean['user_pref_time_predefined'])) {
		$error['user_pref_time_predefined'] = '<div class="error">You choose an incorrect time user_preference</div>';
	}

} else {


	$from = explode('-', $_POST['from']);
	$from = explode(':', $from[1]);
	$from_hour = $from[0];
	$from_minute = $from[1];

	$from = explode('-', $_POST['from']);
	$from = explode('/', $from[0]);
	$from_month = trim($from[0]);
	$from_day = trim($from[1]);
	$from_year = trim($from[2]);

	$to = explode('-', $_POST['to']);
	$to = explode(':', $to[1]);
	$to_hour = $to[0];
	$to_minute = $to[1];

	$to = explode('-', $_POST['to']);
	$to = explode('/', $to[0]);
	$to_month = trim($to[0]);
	$to_day = trim($to[1]);
	$to_year = trim($to[2]);


	//if from or to, validate, and if validated, set it accordingly
	if (($from != '') and ((checkdate($from_month, $from_day, $from_year) == false) or (($from_hour < 0) or ($from_hour > 59) or (!is_numeric($from_hour)) or (($from_minute < 0) or ($from_minute > 59) or (!is_numeric($from_minute)))))) {
		$error['date'] = '<div class="error">Wrong date format, you must use the following military time format:   <strong>mm/dd/yyyy - hh:mms</strong></div>';
	} else {
		$clean['user_pref_time_from'] = mktime($from_hour, $from_minute, 0, $from_month, $from_day, $from_year);
	}

	if (($to != '') and ((checkdate($to_month, $to_day, $to_year) == false) or (($to_hour < 0) or ($to_hour > 59) or (!is_numeric($to_hour)) or (($to_minute < 0) or ($to_minute > 59) or (!is_numeric($to_minute)))))) {
		$error['date'] = '<div class="error">Wrong date format, you must use the following military time format:   <strong>mm/dd/yyyy - hh:mm</strong></div>';
	} else {
		$clean['user_pref_time_to'] = mktime($to_hour, $to_minute, 59, $to_month, $to_day, $to_year);
	}
}

echo $error['date'] . $error['user_pref_time_predefined'] . $error['user_pref_limit'] . $error['user_pref_show'];


if (!$error) {

	$_values['user_pref_time_predefined'] = $clean['user_pref_time_predefined'];
	$_values['user_pref_time_from'] = $clean['user_pref_time_from'];
	$_values['user_pref_time_to'] = $clean['user_pref_time_to'];


	$user_result = UsersPref_DAO::update_by($_values);



}