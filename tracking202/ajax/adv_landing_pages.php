<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


$count = (string)$_POST['counter'];
$count = $count + 1;
$html['count'] = htmlentities($count, ENT_QUOTES, 'UTF-8');

?>

<div id="area_<? echo $count; ?>">
	<select name="aff_campaign_id_<? echo $count; ?>" id="aff_campaign_id_<? echo $count; ?>" onchange="">
		<option value="0"> --</option>
		<?	 $_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$aff_campaign_result = AffCampaigns_DAO::find_by_user_id($user_id);
		while ($aff_campaign_row = $aff_campaign_result->getNext()) {



			$html['aff_campaign_id'] = htmlentities($aff_campaign_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
			$html['aff_campaign_name'] = htmlentities($aff_campaign_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
			$html['aff_network_name'] = htmlentities($aff_campaign_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
			printf('<option value="%s">%s: %s</option>', $html['aff_campaign_id'], $html['aff_network_name'], $html['aff_campaign_name']);
		} ?>
	</select>
	<a class="onclick_color" onclick="remove_area(<? echo $count; ?>);">[remove]</a>
</div>

<img id="load_aff_campaign_<? echo $count; ?>_loading" style="display: none;" src="/202-img/loader-small.gif"/>
<div id="load_aff_campaign_<? echo $count; ?>"></div>
