<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//get the aff_camapaign_id
$_values['user_id'] = 1;
$_values['click_id'] = 0;
$_values['cid'] = 0;
$_values['use_pixel_payout'] = 0;

//first grab the cid
if (array_key_exists('cid', $_GET) && is_numeric($_GET['cid'])) {
	$_values['cid'] = (int)$_GET['cid'];
}

//see if it has the cookie in the campaign id, then the general match, then do whatever we can to grab SOMETHING to tie this lead to
if ($_COOKIE['tracking202subid_a_' . $_values['cid']]) {
	$_values['click_id'] = $_COOKIE['tracking202subid_a_' . $_values['cid']];
} else {
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
}

if (is_numeric($_values['click_id'])) {

	if ($_GET['amount'] && is_numeric($_GET['amount'])) {
		$_values['use_pixel_payout'] = 1;
		$_values['click_payout'] = (float)$_GET['amount'];
	}

	$click_id = (int)$_values['click_id'];
	$use_pixel_payout = $_values['use_pixel_payout'];
	$click_payout = $_values['click_payout'];
	ClicksAdvance_DAO::delay_update_click_filtered($click_id, $use_pixel_payout, $click_payout);
	//ClicksSpy_DAO::delay_update_click_filtered($click_id, $use_pixel_payout, $click_payout);
}
