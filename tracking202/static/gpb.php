<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');


if (!$_GET['subid'] and !$_GET['sid']) {
	die();
}

$click_id = (int)$_GET['subid'];
if ($_GET['sid']) {
	$click_id = (int)$_GET['sid'];
}

$_values['user_id'] = 1;
$_values['click_id'] = $click_id;
$_values['pixel_id'] = 0;
$_values['use_pixel_payout'] = 0;

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