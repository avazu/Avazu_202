<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


?>

<select name="aff_network_id" id="aff_network_id" onchange="load_aff_campaign_id(this.value, 0);">
	<option value="0"> --</option>
	<?
	$user_id = (int)$_SESSION['user_id'];
	$aff_network_result = AffNetworks_DAO::find_not_deleted_by_user_id($user_id);

	while ($aff_network_row = $aff_network_result->getNext()) {

		$html['aff_network_name'] = htmlentities($aff_network_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
		$html['aff_network_id'] = htmlentities($aff_network_row['aff_network_id'], ENT_QUOTES, 'UTF-8');

		if ($_POST['aff_network_id'] == $aff_network_row['aff_network_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s</option>', $selected, $html['aff_network_id'], $html['aff_network_name']);

	} ?>
</select>