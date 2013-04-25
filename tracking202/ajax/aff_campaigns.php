<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


$_values['aff_network_id'] = (int)$_POST['aff_network_id'];
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$aff_network_id = $_values['aff_network_id'];
$aff_campaign_result = AffCampaigns_DAO::find_by_aff_network_id_and_user_id($aff_network_id, $user_id);
if ($aff_campaign_result->count(true) == 0) {



	// echo '<div class="error">You have not added any campaigns for this affiliate network yet.</div>';

} else {
	?>

<select name="aff_campaign_id" id="aff_campaign_id"
        onchange="load_text_ad_id(this.value);  if($('landing_page_style_type')){load_landing_page( $('aff_campaign_id').value, 0, $('landing_page_style_type').getValue());}; if($('unsecure_pixel')) { pixel_data_changed(); }">
	<option value="0"> --</option> <?

	while ($aff_campaign_row = $aff_campaign_result->getNext()) {

		$html['aff_campaign_id'] = htmlentities($aff_campaign_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
		$html['aff_campaign_name'] = htmlentities($aff_campaign_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
		$html['aff_campaign_payout'] = htmlentities($aff_campaign_row['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');

		if ($_POST['aff_campaign_id'] == $aff_campaign_row['aff_campaign_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s &middot; &#36;%01.2f</option>', $selected, $html['aff_campaign_id'], $html['aff_campaign_name'], $html['aff_campaign_payout']);

	} ?>
</select>
<? }
 