<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//get the aff_camapaign_id
$_values['aff_campaign_id_public'] = (int)$_GET['acip'];

$aff_campaign_id_public = $_values['aff_campaign_id_public'];
$aff_campaign_row = AffCampaigns_DAO::find_one_by_id_public1($aff_campaign_id_public);



if (!$aff_campaign_row) {
	die();
}

$_values['aff_campaign_id'] = $aff_campaign_row['aff_campaign_id'];

if (!$_GET['subid']) {
	die();
}

$_values['click_id'] = (int)$_GET['subid'];

//ok now update and fire the pixel tracking

$click_id = $_values['click_id'];
$aff_campaign_id = $_values['aff_campaign_id'];
ClicksAdvance_DAO::delay_update_click_filtered_by_id_and_aff_campaign_id($click_id, $aff_campaign_id);

//ClicksSpy_DAO::delay_update_click_filtered_by_id_and_aff_campaign_id($click_id, $aff_campaign_id);