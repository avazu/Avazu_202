<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user(); ?>

<select name="ppc_network_id" id="ppc_network_id" onchange="load_ppc_account_id(this.value, 0);">
	<option value=""> --</option>
	<?
	$user_id = (int)$_SESSION['user_id'];
	$ppc_network_result = PpcNetworks_DAO::find_not_deleted_by_user_id($user_id);

	while ($ppc_network_row = $ppc_network_result->getNext()) {

		$html['ppc_network_name'] = htmlentities($ppc_network_row['ppc_network_name'], ENT_QUOTES, 'UTF-8');
		$html['ppc_network_id'] = htmlentities($ppc_network_row['ppc_network_id'], ENT_QUOTES, 'UTF-8');

		if ($_POST['ppc_network_id'] == $ppc_network_row['ppc_network_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s</option>', $selected, $html['ppc_network_id'], $html['ppc_network_name']);

	} ?>
</select>