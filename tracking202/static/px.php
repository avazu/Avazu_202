<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//get the aff_camapaign_id
$_values['aff_campaign_id_public'] = (int)$_GET['acip'];

$aff_campaign_id_public = $_values['aff_campaign_id_public'];
$aff_campaign_row = AffCampaigns_DAO::find_one_by_id_public2($aff_campaign_id_public);




$_values['user_id'] = $aff_campaign_row['user_id'];

//see if it has the cookie, do whatever we can to grab to grab SOMETHING to tie this lead to
if ($_COOKIE['tracking202subid']) {

	$_values['click_id'] = $_COOKIE['tracking202subid'];

} else {

	//ok grab the last click from this ip_id
	$_values['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$daysago = time() - 2592000; // 30 days ago

	$ip_address = $_values['ip_address'];
	$user_id = $_values['user_id'];
	$click_row1 = ClicksAdvance_DAO::find_one_by_daysago_and_ip_address_and_user_id($daysago, $ip_address, $user_id);



	$_values['click_id'] = $click_row1['click_id'];

}


if ($_values['click_id']) {

	//ok now update and fire the pixel tracking

	$click_id = $_values['click_id'];
	ClicksAdvance_DAO::delay_update_click_filtered_by_id($click_id);


	//ClicksSpy_DAO::delay_update_click_filtered_by_id($click_id);


}
