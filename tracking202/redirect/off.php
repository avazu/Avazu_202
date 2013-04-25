<?


#only allow numeric acip's
$acip = (int)$_GET['acip'];
if (!is_numeric($acip)) {
	die();
}

#cached redirects stored here:
$myFile = "cached/off-cached.csv";


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
		if ($row[0] == $acip) {
			header('location: ' . $row[1]);
			die();
		}
	}
	@fclose($handle);

	die("<h2>Error establishing a database connection - please contact the webhost</h2>");
}


include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

/* OK FIRST IF THERE IS NO PUBLIC CLICK_ID, JUST REDIRECT TO THE NORMAL CAMPAIGN */
if (!$_GET['pci']) {

	$_values['aff_campaign_id_public'] = (int)$acip;

	$aff_campaign_id_public = $_values['aff_campaign_id_public'];
	$aff_campaign_row = AffCampaigns_DAO::find_one_by_id_public($aff_campaign_id_public);



	if (empty($aff_campaign_row['aff_campaign_url'])) {
		die();
	} //if there is no aff_url to redirect to DIEEEEEE!!!!!!! EEEEEE!!!

	#cache the results 
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
			if ($row[0] == $acip) {
				$writeNewIndex = false;
			}
		}
		@fclose($handle);

		if ($writeNewIndex) {
			//write this index to the txt file
			$newLine = "$acip, {$aff_campaign_row['aff_campaign_url']} \n";
			$newHandle = @fopen($myFile, 'a+');
			@fwrite($newHandle, $newLine);
			@fclose($newHandle);
		}
	}

	$redirect_site_url = rotateTrackerUrl($aff_campaign_row);
	//$redirect_site_url = $redirect_site_url . $click_id;
	$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url, $click_id);

	//ok if there is a url that exists, if redirect php style, or if its cloaked, redirect meta refresh style.
	if ($aff_campaign_row['aff_campaign_cloaking'] == 0) {

		//cloaking OFF, so do a php header redirect
		header('location: ' . $redirect_site_url);

	} else {

		//cloaking ON, so do a meta REFRESH 
		$html['aff_campaign_name'] = $aff_campaign_row['aff_campaign_name']; ?>

	<html>
	<head>
		<title><? echo $html['aff_campaign_name']; ?></title>
		<meta name="robots" content="noindex">
		<meta http-equiv="refresh" content="1; url=<? echo $redirect_site_url; ?>">
	</head>
	<body>

	<form name="form1" id="form1" method="get" action="/tracking202/redirect/cl2.php">
		<input type="hidden" name="q" value="<? echo $redirect_site_url; ?>"/>
	</form>
	<script type="text/javascript">
		document.form1.submit();
	</script>

	<div style="padding: 30px; text-align: center;">
		You are being automatically redirected to <? echo $html['aff_campaign_name']; ?>.<br/><br/>
		Page Stuck? <a href="<? echo $redirect_site_url; ?>">Click Here</a>.
	</div>
	</body>
	</html>

	<?
	}

	//terminate this script, this is the end, if there was no public_click_id
	die();
}


/* ------------------------------------------------------- */
/* ------------------------------------------------------- */
/* ------------------------------------------------------- */
//
//
//
//	ANYTHING BELOW THIS ASSUMES THERE IS A PUBLIC CLICK ID
//
//
/* ------------------------------------------------------- */
/* ------------------------------------------------------- */
/* ------------------------------------------------------- */


$_values['aff_campaign_id_public'] = (int)$_GET['acip'];
$_values['click_id_public'] = (int)$_GET['pci'];


$aff_campaign_id_public = $_values['aff_campaign_id_public'];
$click_id_public = $_values['click_id_public'];
$info_row = AffCampaigns_DAO::find_one_by_id_public_and_click_id_public($aff_campaign_id_public, $click_id_public);




#cache the results
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
		if ($row[0] == $acip) {
			$writeNewIndex = false;
		}
	}
	@fclose($handle);

	if ($writeNewIndex) {
		//write this index to the txt file
		$newLine = "$acip, {$info_row['aff_campaign_url']} \n";
		$newHandle = @fopen($myFile, 'a+');
		@fwrite($newHandle, $newLine);
		@fclose($newHandle);
	}
}


$click_id = $info_row['click_id'];
$_values['click_id'] = $click_id;
/****** THESES ARE THE VARIABLES I NEED TO UPDATE TO THE PUBLIC_CLICK_ID *********/

// click spy & clicks
//	aff_campaign_id
// 	click_payout

$_values['aff_campaign_id'] = $info_row['aff_campaign_id']; //info is mainly aff c
$_values['click_payout'] = $info_row['aff_campaign_payout'];

//removed spy
//$update_sql = "
//	-UPDATE
//		202_clicks AS 2c
//		LEFT JOIN 202_clicks_spy AS 2cs ON (2c.click_id = 2cs.click_id)
//	SET
//		2c.aff_campaign_id='" . $_values['aff_campaign_id'] . "',
//		2cs.aff_campaign_id='" . $_values['aff_campaign_id'] . "',
//		2c.click_payout='" . $_values['click_payout'] . "',
//		2cs.click_payout='" . $_values['click_payout'] . "'
//	WHERE
//		2c.click_id='" . $_values['click_id'] . "'
//";


$aff_campaign_id = $_values['aff_campaign_id'];
$click_payout = $_values['click_payout'];
$click_id = $_values['click_id'];
$update_sql = ClicksAdvance_DAO::delay_update_by_id_and_payout_and_aff_campaign_id($click_id, $click_payout, $aff_campaign_id);


//clicks_record
// 	click_cloaking
// 	click_out

$_values['click_out'] = 1;

if (($info_row['click_cloaking'] == 1) or //if tracker has overrided cloaking on                                                             
    (($info_row['click_cloaking'] == -1) and ($info_row['aff_campaign_cloaking'] == 1)) or
    ((!isset($info_row['click_cloaking'])) and ($info_row['aff_campaign_cloaking'] == 1)) //if no tracker but but by default campaign has cloaking on
) {
	$cloaking_on = true;
	$_values['click_cloaking'] = 1;
	//if cloaking is on, add in a click_id_public, because we will be forwarding them to a cloaked /cl/xxxx link
} else {
	$_values['click_cloaking'] = 0;
}


$click_out = $_values['click_out'];
$click_cloaking = $_values['click_cloaking'];
$click_id = $_values['click_id'];
ClicksAdvance_DAO::delay_update_record_data_by_click_cloaking_and_click_id_and_click_out($click_cloaking, $click_id, $click_out);



//clicks_site
//	click_outbound_site_url_id='".$_values['click_outbound_site_url_id']."',
//	click_cloaking_site_url_id='".$_values['click_cloaking_site_url_id']."',
//	click_redirect_site_url_id='".$_values['click_redirect_site_url_id']."'";

$outbound_site_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$click_outbound_site_url_id = INDEXES::get_site_url_id($outbound_site_url);
$_values['click_outbound_site_url_id'] = $click_outbound_site_url_id;

if ($cloaking_on == true) {
	$cloaking_site_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}


$redirect_site_url = rotateTrackerUrl($info_row);
//$redirect_site_url = $redirect_site_url . $click_id;
$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url, $click_id);

$click_redirect_site_url_id = INDEXES::get_site_url_id($redirect_site_url);
$_values['click_redirect_site_url_id'] = $click_redirect_site_url_id;


$click_outbound_site_url_id = $_values['click_outbound_site_url_id'];
$click_redirect_site_url_id = $_values['click_redirect_site_url_id'];
$click_id = $_values['click_id'];
ClicksAdvance_DAO::delay_update_site_data_by_id_and_outbound_site_url_id_and_redirect_site_url_id($click_id, $click_outbound_site_url_id, $click_redirect_site_url_id);



//alright now the updates,
//WE WANT TO DELAY THESES UPDATES, in a MYSQL DATBASES? Or else the UPDATES lag the server, the UPDATES have to wait until it locks to update the server
//so what happens is if there not delayed, if someone is pulling MASSIVE queries on the t202 website, it'll wait till they load before our update runs,
//and that means if this update wasn't delayed it'd wait untill their queries were done on the site before moving forward.  Massive slowness, so we update delays theses in  cronjobs a at a lter time.


//ADD TO CLICK SUMMARY TABLE?

//update the click summary table if this is a 'real click'
#if ($info_row['click_filtered'] == 0) {

$_values['landing_page_id'] = $info_row['landing_page_id'];
$_values['user_id'] = $info_row['user_id'];

//set timezone correctly

$user_id = $_values['user_id'];
$user_row = Users_DAO::get6($user_id);
AUTH::set_timezone($user_row['user_timezone']);



$now = time();

$today_day = date('j', time());
$today_month = date('n', time());
$today_year = date('Y', time());

//the click_time is recorded in the middle of the day
$click_time = mktime(12, 0, 0, $today_month, $today_day, $today_year);
$_values['click_time'] = $click_time;

//check to make sure this click_summary doesn't already exist

$check_count = SummaryOverview_DAO::count_by1($_values);


//if this click summary hasn't been recorded do this now
if ($check_count == 0) {
	$insert_result = SummaryOverview_DAO::create_by1($_values);


}
#}

//set the cookie
setClickIdCookie($_values['click_id'], $_values['aff_campaign_id']);

//NOW LETS REDIRECT

if ($cloaking_on == true) {

	//if cloaking is turned on, meta refresh out

	?>
<html>
<head>
	<title><? echo $html['aff_campaign_name']; ?></title>
	<meta name="robots" content="noindex">
	<meta http-equiv="refresh" content="1; url=<? echo $redirect_site_url; ?>">
</head>
<body>

<form name="form1" id="form1" method="get" action="/tracking202/redirect/cl2.php">
	<input type="hidden" name="q" value="<? echo $redirect_site_url; ?>"/>
</form>
<script type="text/javascript">
	document.form1.submit();
</script>

<div style="padding: 30px; text-align: center;">
	You are being automatically redirected to <? echo $html['aff_campaign_name']; ?>.<br/><br/>
	Page Stuck? <a href="<? echo $redirect_site_url; ?>">Click Here</a>.
</div>
</body>
</html>
<?
} else {

	//if cloaking is turned off, php header redirect out
	header('location: ' . $redirect_site_url);

}

die();