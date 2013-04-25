<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


if (($_POST['type'] != 'landingpage') and  ($_POST['type'] != 'advlandingpage')) {
	die();
}


$_values['user_id'] = (int)$_SESSION['user_id'];

if ($_POST['type'] == 'landingpage') {
	$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];

	$user_id = $_values['user_id'];
	$aff_campaign_id = $_values['aff_campaign_id'];
	$landing_page_result = LandingPages_DAO::find_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id);

}

if ($_POST['type'] == 'advlandingpage') {
	$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];

	$user_id = $_values['user_id'];
	$landing_page_result = LandingPages_DAO::find_by_user_id($user_id);

}

#print_r_html($_POST);

?><input id="landing_page_style_type" type="hidden" name="landing_page_style_type"
         value="<? echo htmlentities($_POST['type']); ?>"/><?

if ($landing_page_result->count(true) == 0) {

	//echo '<div class="error">You have not added any landing pages for this campaign yet.</div>';

} else {
	?>

<select name="landing_page_id" id="landing_page_id"
        onchange="<? if ($_POST['type'] == 'advlandingpage') {
	        echo 'load_adv_text_ad_id(this.value);';
        } else {
	        echo ' load_text_ad_id( $(\'aff_campaign_id\').value ); ';
        }  ?>">
	<option value="0"> --</option> <?
	while ($landing_page_row = $landing_page_result->getNext()) {

		$html['landing_page_id'] = htmlentities($landing_page_row['landing_page_id'], ENT_QUOTES, 'UTF-8');
		$html['landing_page_nickname'] = htmlentities($landing_page_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');

		if ($_POST['landing_page_id'] == $landing_page_row['landing_page_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s</option>', $selected, $html['landing_page_id'], $html['landing_page_nickname']);

	} ?>
</select> <?
} ?>
 