<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


$_values['ppc_network_id'] = (int)$_POST['ppc_network_id'];
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$ppc_network_id = $_values['ppc_network_id'];
$ppc_account_result = PpcAccounts_DAO::find_by_ppc_network_id_and_user_id($ppc_network_id, $user_id);


if ($ppc_account_result->count(true) == 0) {

	//echo '<div class="error">You have not added any PPC accounts for this PPC network yet.</div>';

} else {
	?>

<select name="ppc_account_id" id="ppc_account_id">
	<option value=""> --</option> <?

	while ($ppc_account_row = $ppc_account_result->getNext()) {

		$html['ppc_account_id'] = htmlentities($ppc_account_row['ppc_account_id'], ENT_QUOTES, 'UTF-8');
		$html['ppc_account_name'] = htmlentities($ppc_account_row['ppc_account_name'], ENT_QUOTES, 'UTF-8');

		if ($_POST['ppc_account_id'] == $ppc_account_row['ppc_account_id']) {
			$selected = 'selected=""';
		} else {
			$selected = '';
		}

		printf('<option %s value="%s">%s</option>', $selected, $html['ppc_account_id'], $html['ppc_account_name']);

	} ?>
</select>
<? }
 