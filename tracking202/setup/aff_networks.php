<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

$user_id = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$aff_network_name = trim($_POST['aff_network_name']);
	if (empty($aff_network_name)) {
		$error['aff_network_name'] = '<div class="error">Type in the name of your affiliate network.</div>';
	}

	if (!$error) {
		$aff_network_result = AffNetworks_DAO::create_by_name_and_user_id($aff_network_name, $user_id);

		$add_success = true;
	}
}

if (isset($_GET['delete_aff_network_id'])) {

	$_values['aff_network_id'] = (int)$_GET['delete_aff_network_id'];


	$aff_network_id = $_values['aff_network_id'];
	$delete_success = AffNetworks_DAO::delete_by_id_and_user_id($aff_network_id, $user_id);
}


template_top('Affiliate Networks Setup', NULL, NULL, NULL); ?>

<div id="info">
	<h2>Affiliate Network Setup</h2>
	Add the affiliate networks you work with here.
</div>

<table cellspacing="0" cellpadding="0" class="setup">
	<tr valign="top">
		<td>
			<? if ($error) { ?>
			<div class="warning">
				<div><h3>There were errors with your submission.</h3></div>
			</div>
			<? } echo $error['token']; ?>

			<? if ($add_success == true) { ?>
			<div class="success">
				<div><h3>Your submission was successful</h3>You have succesfully added an affiliate network to your account.</div>
			</div>
			<? } ?>

			<? if ($delete_success == true) { ?>
			<div class="success">
				<div><h3>You deletion was successful</h3>You have succesfully deleted an affiliate network from your account.</div>
			</div>
			<? } ?>

			<form method="post" action="<? echo $_SERVER['REDIRECT_URL']; ?>">
				<table style="margin: 0px auto;">
					<tr>
						<td colspan="2" style="width: 400px;">
							<h2 class="green">Add Affiliate Network</h2>

							<p style="text-align: justify;">What affiliate companies do you use? Some examples include Copeac, Blooads, and Commission Junction.</p>
						</td>
					</tr>
					<tr>
						<td/>
						<br/></tr>
					<tr>
						<td class="left_caption">Affiliate Network</td>
						<td>
							<input type="text" name="aff_network_name" style="display: inline;"/>
							<input type="submit" value="Add" style="display: inline; margin-left: 10px;"/>
						</td>
					</tr>
				</table>
				<? echo $error['aff_network_name']; ?>
			</form>
		</td>
		<td class="setup-right">
			<h2 class="green">My Affiliate Networks</h2>

			<ul>
				<?
				$user_id = (int)$_SESSION['user_id'];
				$aff_network_result = AffNetworks_DAO::find_not_deleted_by_user_id($user_id);
				
				if ($aff_network_result->count(true) == 0) {
					?>
					<li>You have not added any networks.</li><?
				}

				while ($aff_network_row = $aff_network_result->getNext()) {
					$html['aff_network_name'] = htmlentities($aff_network_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
					$html['aff_network_id'] = htmlentities($aff_network_row['aff_network_id'], ENT_QUOTES, 'UTF-8');

					printf('<li>%s - <a href="?delete_aff_network_id=%s" style="font-size: 9px;">remove</a></li>', $html['aff_network_name'], $html['aff_network_id']);

				} ?>
			</ul>
		</td>
	</tr>
</table>



<? template_bottom();