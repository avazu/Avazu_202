<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$aff_campaign_id = $_values['aff_campaign_id'];
$text_ad_result = TextAds_DAO::find_by_aff_campaign_id_and_user_id($aff_campaign_id, $user_id);



if ($text_ad_result->count(true) == 0) {

	//echo '<div class="error">You have not added any ad copies for this campaign yet.</div>';

} else {
	?>

<select id="text_ad_id" name="text_ad_id" onchange="load_ad_preview(this.value);">
	<option value="0"> --</option> <?

	while ($text_ad_row = $text_ad_result->getNext()) {

		$html['text_ad_id'] = htmlentities($text_ad_row['text_ad_id'], ENT_QUOTES, 'UTF-8');
		$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');

		if ($_POST['text_ad_id'] == $text_ad_row['text_ad_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s</option>', $selected, $html['text_ad_id'], $html['text_ad_name']);

	} ?>
</select>
<? }
 