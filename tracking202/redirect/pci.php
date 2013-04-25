<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

$_values['click_id_public'] = (int)$_GET['pci'];


$click_id_public = $_values['click_id_public'];
$click_row = ClicksAdvance_DAO::find_one_by_id_public1($click_id_public);



$click_id = $click_row['click_id'];
$aff_campaign_id = $click_row['aff_campaign_id'];
$_values['click_id'] = $click_id;
$_values['aff_campaign_id'] = $aff_campaign_id;
$_values['click_out'] = 1;


$click_out = $_values['click_out'];
$click_id = $_values['click_id'];
ClicksAdvance_DAO::delay_update_record_data_by_click_id_and_click_out($click_id, $click_out);



//see if cloaking was turned on
if ($click_row['click_cloaking'] == 1) {
	$cloaking_on = true;
	$_values['site_url_id'] = $click_row['click_cloaking_site_url_id'];

	$site_url_id = $_values['site_url_id'];
	$site_url_row = SiteUrls_DAO::get($site_url_id);
	$cloaking_site_url = $site_url_row['site_url_address'];


	$cloaking_site_url = $site_url_row['site_url_address'];
} else {
	$cloaking_on = false;
	$_values['site_url_id'] = $click_row['click_redirect_site_url_id'];

	$site_url_id = $_values['site_url_id'];
	$site_url_row = SiteUrls_DAO::get($site_url_id);
	$cloaking_site_url = $site_url_row['site_url_address'];


	$redirect_site_url = $site_url_row['site_url_address'];
}


//set the cookie
setClickIdCookie($_values['click_id'], $_values['aff_campaign_id']);

//now we've updated, lets redirect
if ($cloaking_on == true) {
	//if cloaked, redirect them to the cloaked site. 
	header('location: ' . $cloaking_site_url);
} else {
	header('location: ' . $redirect_site_url);
}

