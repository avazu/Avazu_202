<?

#only allow numeric t202ids
$t202id = (int)$_GET['t202id'];
if (!is_numeric($t202id)) {
	die();
}


#cached redirects stored here:
$myFile = "cached/dl-cached.csv";


# check to see if mysql connection works, if not fail over to cached .CSV stored redirect urls
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config.php');
$usedCachedRedirect = false;
try {
	//http://derickrethans.nl/64bit-ints-in-mongodb.html
	//$connect = new Mongo("mongodb://${dbuser}:${dbpass}@${dbhost}", array("replicaSet" => true));
	$connect = new Mongo("mongodb://$dbhost:27017",
		array("replicaSet" => true, 'connect' => TRUE, 'username' => $dbuser, 'password' => $dbpass));
}
catch (Exception $e) {
	$usedCachedRedirect = true;
}

#the mysql server is down, use the txt cached redirect
if ($usedCachedRedirect == true) {

	$t202id = (int)$_GET['t202id'];
	$handle = @fopen($myFile, 'r');
	while ($row = @fgetcsv($handle, 100000, ",")) {

		//if a cached key is found for this t202id, redirect to that url
		if ($row[0] == $t202id) {
			header('location: ' . $row[1]);
			die();
		}
	}
	@fclose($handle);

	die("<h2>Error establishing a database connection - please contact the webhost</h2>");
}

//todo reduce the require once files
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//grab tracker data

$_values['tracker_id_public'] = (int)$t202id;

$tracker_id_public = $_values['tracker_id_public'];
$tracker_row = Trackers_DAO::find_one_by_id_public($tracker_id_public);

//echo "tracker row with campaign=";
//print_r($tracker_row);
//todo check if move here is right
if (!$tracker_row) {
	die();
}

if (is_writable(dirname(__FILE__) . '/cached')) {

	#if the file does not exist create it
	if (!file_exists($myFile)) {
		$handle = @fopen($myFile, 'w');
		@fclose($handle);
	}

	# now save this link to the 
	$handle = @fopen($myFile, 'r');
	$writeNewIndex = true;
	while (($row = @fgetcsv($handle, 100000, ",")) and ($writeNewIndex == true)) {
		if ($row[0] == $t202id) {
			$writeNewIndex = false;
		}
	}
	@fclose($handle);

	if ($writeNewIndex) {
		//write this index to the txt file
		$newLine = "$t202id, {$tracker_row['aff_campaign_url']} \n";
		$newHandle = @fopen($myFile, 'a+');
		@fwrite($newHandle, $newLine);
		@fclose($newHandle);
	}
}


//set the timezone to the users timezone
$_values['user_id'] = $tracker_row['user_id'];

$user_id = $_values['user_id'];
$user_row = Users_DAO::get5($user_id);



//now this sets it
AUTH::set_timezone($user_row['user_timezone']);


//get mysql variables
$_values['aff_campaign_id'] = $tracker_row['aff_campaign_id'];
$_values['ppc_account_id'] = $tracker_row['ppc_account_id'];
$_values['click_cpc'] = $tracker_row['click_cpc'];
$_values['click_payout'] = $tracker_row['aff_campaign_payout'];

//echo "click_payout=".$_values['click_payout'];
$_values['click_time'] = time();
$_values['text_ad_id'] = $tracker_row['text_ad_id'];

/* ok, if $_GET['OVRAW'] that is a yahoo keyword, if on the REFER, there is a $_GET['q], that is a GOOGLE keyword... */
//so this is going to check the REFERER URL, for a ?q=, which is the ACUTAL KEYWORD searched.
$referer_url_parsed = @parse_url($_SERVER['HTTP_REFERER']);
$referer_url_query = $referer_url_parsed['query'];

@parse_str($referer_url_query, $referer_query);

switch ($user_row['user_keyword_searched_or_bidded']) {

	case "bidded":
		#try to get the bidded keyword first
		if ($_GET['OVKEY']) { //if this is a Y! keyword
			$keyword = (string)$_GET['OVKEY'];
		} elseif ($_GET['t202kw']) {
			$keyword = (string)$_GET['t202kw'];
		} elseif ($referer_query['p']) {
			$keyword = $referer_query['p'];
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = (string)$_GET['target_passthrough'];
		} else { //if this is a zango, or more keyword
			$keyword = (string)$_GET['keyword'];
		}
		break;
	case "searched":
		#try to get the searched keyword
		if ($referer_query['q']) {
			$keyword = $referer_query['q'];
		} elseif ($referer_query['p']) {
			$keyword = $referer_query['p'];
		} elseif ($_GET['OVRAW']) { //if this is a Y! keyword
			$keyword = (string)$_GET['OVRAW'];
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = (string)$_GET['target_passthrough'];
		} elseif ($_GET['keyword']) { //if this is a zango, or more keyword
			$keyword = (string)$_GET['keyword'];
		} else {
			$keyword = (string)$_GET['t202kw'];
		}
		break;
}
$keyword = str_replace('%20', ' ', $keyword);
$keyword_id = INDEXES::get_keyword_id($keyword);
$_values['keyword_id'] = $keyword_id;

$c1 = (string)$_GET['c1'];
$c1 = str_replace('%20', ' ', $c1);
$c1_id = INDEXES::get_c1_id($c1);
$_values['c1_id'] = $c1_id;

$c2 = (string)$_GET['c2'];
$c2 = str_replace('%20', ' ', $c2);
$c2_id = INDEXES::get_c2_id($c2);
$_values['c2_id'] = $c2_id;

$c3 = (string)$_GET['c3'];
$c3 = str_replace('%20', ' ', $c3);
$c3_id = INDEXES::get_c3_id($c3);
$_values['c3_id'] = $c3_id;

$c4 = (string)$_GET['c4'];
$c4 = str_replace('%20', ' ', $c4);
$c4_id = INDEXES::get_c4_id($c4);
$_values['c4_id'] = $c4_id;

$id = INDEXES::get_platform_and_browser_id();
$_values['platform_id'] = $id['platform'];
$_values['browser_id'] = $id['browser'];

$_values['click_in'] = 1;
$_values['click_out'] = 1;


$ip_id = INDEXES::get_ip_id($_SERVER['HTTP_X_FORWARDED_FOR']);
$_values['ip_id'] = $ip_id;


//before we finish filter this click
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
$user_id = $tracker_row['user_id'];

$click_filtered = FILTER::startFilter($click_id, $ip_id, $ip_address, $user_id);
$_values['click_filtered'] = $click_filtered;


//ok we have the main data, now insert this row
$click_id = ClicksCounter_DAO::getNextId();


//now gather the info for the advance click insert
$_values['click_id'] = $click_id;

//because this is a simple landing page, set click_alp (which stands for click advanced landing page, equal to 0)
$_values['click_alp'] = 0;


//ok we have the main data, now insert this row
$click_result = Clicks_DAO::create_for_dl_by($_values);

//ok we have the main data, now insert this row
//$click_result = ClicksSpy_DAO::create_by($_values);



$click_adv = ClicksAdvance_DAO::create_doc_for_dl_by($_values);

//now we have the click's advance data, now insert this row
$click_adv = ClicksAdvance_DAO::fill_advance_data($click_adv, $_values);



//insert the tracking data
$click_adv = ClicksAdvance_DAO::fill_tracking_data($click_adv, $_values);



//now gather variables for the clicks record db
//lets determine if cloaking is on
if (($tracker_row['click_cloaking'] == 1) or //if tracker has overrided cloaking on
    (($tracker_row['click_cloaking'] == -1) and ($tracker_row['aff_campaign_cloaking'] == 1)) or
    ((!isset($tracker_row['click_cloaking'])) and ($tracker_row['aff_campaign_cloaking'] == 1)) //if no tracker but but by default campaign has cloaking on
) {
	$cloaking_on = true;
	$_values['click_cloaking'] = 1;
	//if cloaking is on, add in a click_id_public, because we will be forwarding them to a cloaked /cl/xxxx link
	$click_id_public = rand(1, 9) . $click_id . rand(1, 9);
	$_values['click_id_public'] = (int)$click_id_public;
} else {
	$_values['click_cloaking'] = 0;
}

//ok we have our click recorded table, now lets insert theses
$click_adv = ClicksAdvance_DAO::fill_record_data($click_adv, $_values);



//now lets get variables for clicks site
//so this is going to check the REFERER URL, for a ?url=, which is the ACUTAL URL, instead of the google content, pagead2.google.... 
if ($referer_query['url']) {
	$click_referer_site_url_id = INDEXES::get_site_url_id($referer_query['url']);
} else {
	$click_referer_site_url_id = INDEXES::get_site_url_id($_SERVER['HTTP_REFERER']);
}

$_values['click_referer_site_url_id'] = $click_referer_site_url_id;

$outbound_site_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$click_outbound_site_url_id = INDEXES::get_site_url_id($outbound_site_url);
$_values['click_outbound_site_url_id'] = $click_outbound_site_url_id;

if ($cloaking_on == true) {
	$cloaking_site_url = 'http://' . $_SERVER['SERVER_NAME'] . '/tracking202/redirect/cl.php?pci=' . $click_id_public;
}


//rotate the urls
$redirect_site_url = rotateTrackerUrl($tracker_row);
//$redirect_site_url = $redirect_site_url . $click_id;
$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url, $click_id);


$click_redirect_site_url_id = INDEXES::get_site_url_id($redirect_site_url);
$_values['click_redirect_site_url_id'] = $click_redirect_site_url_id;

//insert this
$click_adv = ClicksAdvance_DAO::fill_site_data($click_adv, $_values);


//save it finally
ClicksAdvance_DAO::save($click_adv);

//update the click summary table if this is a 'real click'
#if ($click_filtered == 0) {

$now = time();

$today_day = date('j', time());
$today_month = date('n', time());
$today_year = date('Y', time());

//the click_time is recorded in the middle of the day
$click_time = mktime(12, 0, 0, $today_month, $today_day, $today_year);
$_values['click_time'] = $click_time;

//check to make sure this click_summary doesn't already exist
$check_count = SummaryOverview_DAO::count_by($_values);


//if this click summary hasn't been recorded do this now
if ($check_count == 0) {
	$insert_result = SummaryOverview_DAO::create_by($_values);

}
#} 

//set the cookie
setClickIdCookie($_values['click_id'], $_values['aff_campaign_id']);


//now we've recorded, now lets redirect them
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: ' . $cloaking_site_url);
} else {
	header('location: ' . $redirect_site_url);
} 

