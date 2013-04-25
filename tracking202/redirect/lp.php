<?

#only allow numeric t202ids
$lpip = (int)$_GET['lpip'];
if (!is_numeric($lpip)) {
	die();
}


#cached redirects stored here:
$myFile = "cached/lp-cached.csv";


# check to see if mysql connection works, if not fail over to cached .CSV stored redirect urls
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config.php');

$usedCachedRedirect = false;
try {

	//$connect = new Mongo("mongodb://${dbuser}:${dbpass}@${dbhost}", array("replicaSet" => true));
	$connect = new Mongo("mongodb://$dbhost:27017",
		array("replicaSet" => true, 'connect' => TRUE, 'username' => $dbuser, 'password' => $dbpass));
}
catch (Exception $e) {
	$usedCachedRedirect = true;
}

#the mysql server is down, use the txt cached redirect
if ($usedCachedRedirect == true) {

	$handle = @fopen($myFile, 'r');
	while ($row = @fgetcsv($handle, 100000, ",")) {

		//if a cached key is found for this t202id, redirect to that url
		if ($row[0] == $lpip) {
			header('location: ' . $row[1]);
			die();
		}
	}
	@fclose($handle);

	die("<h2>Error establishing a database connection - please contact the webhost</h2>");
}


include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

$_values['landing_page_id_public'] = (int)$lpip;
DU::dump($_values);
$landing_page_id_public = $_values['landing_page_id_public'];
$tracker_row = LandingPages_DAO::find_one_with_aff_campaign_by_id_public($landing_page_id_public);
DU::dump($tracker_row);


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
		if ($row[0] == $lpip) {
			$writeNewIndex = false;
		}
	}
	@fclose($handle);

	if ($writeNewIndex) {
		//write this index to the txt file
		$newLine = "$lpip, {$tracker_row['aff_campaign_url']} \n";
		$newHandle = @fopen($myFile, 'a+');
		@fwrite($newHandle, $newLine);
		@fclose($newHandle);
	}
}


//grab the GET variables from the LANDING PAGE
$landing_page_site_url_address_parsed = parse_url($_SERVER['HTTP_REFERER']);
parse_str($landing_page_site_url_address_parsed['query'], $_GET);

if ($_GET['t202id']) {
	//grab tracker data if avaliable
	$_values['tracker_id_public'] = (int)$_GET['t202id'];


	$tracker_id_public = $_values['tracker_id_public'];
	$tracker_row2 = Trackers_DAO::find_one_by_id_public1($tracker_id_public);
	DU::dump($tracker_row2);


	if ($tracker_row2) {
		$tracker_row = array_merge($tracker_row, $tracker_row2);
		DU::dump($tracker_row);
	}
}

//INSERT THIS CLICK BELOW, if this click doesn't already exisit

//get mysql variables 
$_values['user_id'] = $tracker_row['user_id'];
$_values['aff_campaign_id'] = $tracker_row['aff_campaign_id'];
$_values['ppc_account_id'] = $tracker_row['ppc_account_id'];
$_values['click_cpc'] = $tracker_row['click_cpc'];
$_values['click_payout'] = $tracker_row['aff_campaign_payout'];
$_values['click_time'] = time();

$_values['landing_page_id'] = $tracker_row['landing_page_id'];
$_values['text_ad_id'] = $tracker_row['text_ad_id'];

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

/*
//ok we have our click recorded table, now lets insert theses
$click_sql = "INSERT INTO   clicks_record
			  SET           click_id='".$_values['click_id']."',
							click_id_public='".$_values['click_id_public']."',
							click_cloaking='".$_values['click_cloaking']."',
							click_in='".$_values['click_in']."',
							click_out='".$_values['click_out']."'";
$click_result = mysql_query($click_sql) or record_mysql_error($click_sql);

//now lets get variables for clicks site
$click_landing_site_url_id = INDEXES::get_site_url_id($_SERVER['HTTP_REFERER']);
$_values['click_landing_site_url_id'] = mysql_real_escape_string($click_landing_site_url_id);

$outbound_site_url = 'http://'.$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$click_outbound_site_url_id = INDEXES::get_site_url_id($outbound_site_url);
$_values['click_outbound_site_url_id'] = mysql_real_escape_string($click_outbound_site_url_id);
 */
if ($cloaking_on == true) {

	$cloaking_site_url = 'http://' . $_SERVER['SERVER_NAME'] . '/tracking202/redirect/lpc.php?lpip=' . $tracker_row['landing_page_id_public'];
	$click_cloaking_site_url_id = INDEXES::get_site_url_id($cloaking_site_url);
	$_values['click_cloaking_site_url_id'] = $click_cloaking_site_url_id;

}

$redirect_site_url = rotateTrackerUrl($tracker_row);
$click_id = $_COOKIE['tracking202subid_a_' . $tracker_row['aff_campaign_id']];
$_values['click_id'] = $click_id;
$_values['click_out'] = 1;


$click_out = $_values['click_out'];
$click_cloaking = $_values['click_cloaking'];
$click_id = $_values['click_id'];
$update_sql = ClicksAdvance_DAO::delay_update_record_data_by_click_cloaking_and_click_id_and_click_out($click_cloaking, $click_id, $click_out);



//$redirect_site_url = $redirect_site_url . $click_id;
$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url, $_values['click_id']);

$click_redirect_site_url_id = INDEXES::get_site_url_id($redirect_site_url);
$_values['click_redirect_site_url_id'] = $click_redirect_site_url_id;

//now we've recorded, now lets redirect them
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: ' . $cloaking_site_url);
} else {
	header('location: ' . $redirect_site_url);
}