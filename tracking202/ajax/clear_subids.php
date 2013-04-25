<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


$_values['user_id'] = (int)$_SESSION['user_id'];

if ($_POST['aff_network_id'] == 0) {
	$error['clear_subids'] = '<div class="error">You have to at least select an affiliate network to clear out</div>';
}
$_values['aff_network_id'] = (int)$_POST['aff_network_id'];

if ($error) {
	echo $error['clear_subids'];
	die();
}


if (!$error) {

	if ($_POST['aff_campaign_id'] != 0) {
		$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];

		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$click_result = ClicksAdvance_DAO::update_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id);

	} else {

		$aff_network_id = $_values['aff_network_id'];
		$user_id = $_values['user_id'];
		$click_result = ClicksAdvance_DAO::update_by_aff_network_id_and_user_id($aff_network_id, $user_id);

	}

	$clicks = $click_result ? $click_result : 0;
	echo "<div class=\"success\"><div><h3>You have reset <strong>$clicks</strong> subids!</h3>You can now re-upload your subids.</div></div>";

}