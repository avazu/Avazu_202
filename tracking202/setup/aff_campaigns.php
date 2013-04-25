<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

if ($_GET['edit_aff_campaign_id']) {
	$editing = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$aff_network_id = trim($_POST['aff_network_id']);
	if (empty($aff_network_id)) {
		$error['aff_network_id'] = '<div class="error">Type in the name the ppc network.</div>';
	}

	$aff_campaign_name = trim($_POST['aff_campaign_name']);
	if (empty($aff_campaign_name)) {
		$error['aff_campaign_name'] = '<div class="error">What is the name of this campaign.</div>';
	}

	$aff_campaign_url = trim($_POST['aff_campaign_url']);
	if (empty($aff_campaign_url)) {
		$error['aff_campaign_url'] = '<div class="error">What is your affiliate link? Make sure subids can be added to it.</div>';
	}

	if ((substr($_POST['aff_campaign_url'], 0, 7) != 'http://') and (substr($_POST['aff_campaign_url'], 0, 8) != 'https://')) {
		$error['aff_campaign_url'] .= '<div class="error">Your Landing Page URL must start with http:// or https://</div>';
	}

	$aff_campaign_payout = trim($_POST['aff_campaign_payout']);
	if (!is_numeric($aff_campaign_payout)) {
		$error['aff_campaign_payout'] .= '<div class="error">Please enter in a numeric number for the payout.</div>';
	}

	//check to see if they are the owners of this affiliate network
	$_values['aff_network_id'] = (int)$_POST['aff_network_id'];
	$_values['user_id'] = (int)$_SESSION['user_id'];

	$user_id = $_values['user_id'];
	$aff_network_id = $_values['aff_network_id'];
	$aff_network_result = AffNetworks_DAO::find_by_id_and_user_id($aff_network_id, $user_id);
	if ($aff_network_result->count(true) == 0) {


		$error['wrong_user'] = '<div class="error">You are not authorized to add an campaign to another users network</div>';
	}


	//if editing, check to make sure the own the campaign they are editing
	if ($editing == true) {
		$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
		$_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$aff_campaign_result = AffCampaigns_DAO::find_by_id_and_user_id($aff_campaign_id, $user_id);
		if ($aff_campaign_result->count(true) == 0) {


			$error['wrong_user'] .= '<div class="error">You are not authorized to modify another users campaign</div>';
		}
	}

	if (!$error) {
		$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
		$_values['aff_network_id'] = (int)$_POST['aff_network_id'];
		$_values['aff_campaign_name'] = (string)$_POST['aff_campaign_name'];
		$_values['aff_campaign_url'] = (string)$_POST['aff_campaign_url'];
		$_values['aff_campaign_url_2'] = (string)$_POST['aff_campaign_url_2'];
		$_values['aff_campaign_url_3'] = (string)$_POST['aff_campaign_url_3'];
		$_values['aff_campaign_url_4'] = (string)$_POST['aff_campaign_url_4'];
		$_values['aff_campaign_url_5'] = (string)$_POST['aff_campaign_url_5'];
		$_values['aff_campaign_rotate'] = (int)$_POST['aff_campaign_rotate'];
		$_values['aff_campaign_payout'] = (float)$_POST['aff_campaign_payout'];
		$_values['aff_campaign_cloaking'] = (int)$_POST['aff_campaign_cloaking'];
		$_values['user_id'] = (int)$_SESSION['user_id'];
		$_values['aff_campaign_time'] = time();

		if ($editing != true) {
			$_values['aff_campaign_id'] = -1;
		}
		$aff_campaign_result = AffCampaigns_DAO::upsert_by($_values);
		DU::dump($aff_campaign_result);

		$add_success = $aff_campaign_result === false ? false : true;

		if ($editing != true) {
			//if this landing page is brand new, add on a landing_page_id_public
			$aff_campaign_row['aff_campaign_id'] = $aff_campaign_result['aff_campaign_id'];
			$aff_campaign_id_public = rand(1, 9) . $aff_campaign_row['aff_campaign_id'] . rand(1, 9);
			$_values['aff_campaign_id_public'] = (int)$aff_campaign_id_public;
			$_values['aff_campaign_id'] = $aff_campaign_row['aff_campaign_id'];


			$aff_campaign_id_public = $_values['aff_campaign_id_public'];
			$aff_campaign_id = $_values['aff_campaign_id'];
			$aff_campaign_result = AffCampaigns_DAO::update_by_id_and_id_public($aff_campaign_id, $aff_campaign_id_public);




		}

	}
}

if (isset($_GET['delete_aff_campaign_id'])) {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['aff_campaign_id'] = (int)$_GET['delete_aff_campaign_id'];
	$_values['date_deleted'] = time();


	$aff_campaign_time = $_values['aff_campaign_time'];
	$user_id = $_values['user_id'];
	$aff_campaign_id = $_values['aff_campaign_id'];
	$delete_result = AffCampaigns_DAO::update_by_id_and_time_and_user_id($aff_campaign_id, $aff_campaign_time, $user_id);




	if ($delete_result) {
		$delete_success = true;
	}
}

if ($_GET['edit_aff_campaign_id']) {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['aff_campaign_id'] = (int)$_GET['edit_aff_campaign_id'];


	$aff_campaign_id = $_values['aff_campaign_id'];
	$user_id = $_values['user_id'];
	$aff_campaign_row = AffCampaigns_DAO::find_one_by_id_and_user_id($aff_campaign_id, $user_id);




	$selected['aff_network_id'] = $aff_campaign_row['aff_network_id'];
	$html = array_map('htmlentities', $aff_campaign_row);
	$html['aff_campaign_id'] = htmlentities($_GET['edit_aff_campaign_id'], ENT_QUOTES, 'UTF-8');

}

//this will override the edit, if posting and edit fail
if (($_SERVER['REQUEST_METHOD'] == 'POST') and ($add_success != true)) {

	$selected['aff_network_id'] = (int)$_POST['aff_network_id'];
	$html = array_map('htmlentities', $_POST);
}

template_top('Affiliate Campaigns Setup', NULL, NULL, NULL); ?>

<div id="info">
	<h2>Affiliate Campaign Setup</h2>
	Add the affiliate network campaigns you want to run. <a class="onclick_color"
	                                                        onclick="Effect.toggle('helper','appear')">[help]</a>

	<div style="display: none;" id="helper">
		<br/>Please make sure to enter the campaign url in so that the subid can be inserted after it. If you do not
		understand how subids work at your network, stop, and contact your affiliate manager about how to add subids to
		your affiliate links. You may also contact us and we will help you out as well. <br/><br/>Tracking202 supports
		the ability to cloak your traffic; cloaking will prevent your advertisers and the affiliate networks who you
		work with from seeing your keywords. Please note if you are doing direct linking with Google Adwords, a cloaked
		direct linking setup can kill your qualitly score.
		Don't understand cloaking? Leave it off for now and learn more about it in our help section later.
	</div>
</div>

<table cellspacing="3" cellpadding="3" class="setup">
<tr valign="top">
<td>
	<? if ($error) { ?>
	<div class="warning">
		<div><h3>There were errors with your submission.</h3></div>
	</div>
	<? } echo $error['token']; ?>

	<? if ($add_success == true) { ?>
	<div class="success">
		<div><h3>Your submission was successful</h3>Your changes were made succesfully.</div>
	</div>
	<? } ?>

	<? if ($delete_success == true) { ?>
	<div class="success">
		<div><h3>You deletion was successful</h3>You have succesfully removed a campaign.</div>
	</div>
	<? } ?>
	<form method="post" action="<? if ($delete_success == true) {
		echo $_SERVER['REDIRECT_URL'];
	}?>" style>
		<input name="aff_campaign_id" type="hidden" value="<? echo $html['aff_campaign_id']; ?>"/>
		<table>
			<tr>
				<td colspan="2">
					<h2 class="green">Add A Campaign</h2>

					<p style="text-align: justify;">Here you add each of the affiliate campaigns you are
						promoting.</p>
				</td>
			</tr>
			<tr>
				<td/>
				<br/></tr>
			<tr>
				<td class="left_caption">Affiliate Network</td>
				<td>
					<select name="aff_network_id">
						<option value=""> --</option>
						<?  $_values['user_id'] = (int)$_SESSION['user_id'];

						$user_id = $_values['user_id'];
						$aff_network_result = AffNetworks_DAO::find_not_deleted_by_user_id($user_id);
						while ($aff_network_row = $aff_network_result->getNext()) {



							$html['aff_network_name'] = htmlentities($aff_network_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
							$html['aff_network_id'] = htmlentities($aff_network_row['aff_network_id'], ENT_QUOTES, 'UTF-8');

							if ($selected['aff_network_id'] == $aff_network_row['aff_network_id']) {
								printf('<option selected="selected" value="%s">%s</option>', $html['aff_network_id'], $html['aff_network_name']);
							} else {
								printf('<option value="%s">%s</option>', $html['aff_network_id'], $html['aff_network_name']);
							}
						} ?>
					</select>
					<? echo $error['pcc_network_id']; ?>
				</td>
			</tr>
			<tr>
				<td class="left_caption">Campaign Name</td>
				<td>
					<input type="text" name="aff_campaign_name" value="<? echo $html['aff_campaign_name']; ?>"
					       style="display: inline;"/>
				</td>
			</tr>
			<tr>
				<td class="left_caption">Rotate Urls</td>
				<td>
					<input type="radio" name="aff_campaign_rotate" value="0"
					       onClick="showAllRotatingUrls('false');" <? if ($html['aff_campaign_rotate'] == 0) {
						echo ' CHECKED ';
					} ?>>
					No
							<span style="padding-left: 10px;"><input type="radio" name="aff_campaign_rotate" value="1"
							                                         onClick="showAllRotatingUrls('true');" <? if ($html['aff_campaign_rotate'] == 1) {
								echo ' CHECKED ';
							} ?>> Yes</span>

					<script type="text/javascript">
						function showAllRotatingUrls(bool) {

							if (bool == 'true') {

								document.getElementById('rotateUrl2').style.display = 'table-row';
								document.getElementById('rotateUrl3').style.display = 'table-row';
								document.getElementById('rotateUrl4').style.display = 'table-row';
								document.getElementById('rotateUrl5').style.display = 'table-row';

							} else {

								document.getElementById('rotateUrl2').style.display = 'none';
								document.getElementById('rotateUrl3').style.display = 'none';
								document.getElementById('rotateUrl4').style.display = 'none';
								document.getElementById('rotateUrl5').style.display = 'none';
							}
						}
					</script>
				</td>
			</tr>
			<tr>
				<td class="left_caption" style="vertical-align:top">Affiliate URL <a class="onclick_color"
				                                                                     onclick="alert('This your affiliate link for the campaign. If you do not know how to track subids or what a subid is, ask your affiliate manager before moving forward. If you do not set up subids properly, your campaigns will not track!');">
					[?] </a></td>
				<td style="white-space: nowrap;">
					<input type="text" name="aff_campaign_url" value="<? echo $html['aff_campaign_url']; ?>"
					       style="width: 200px; display: inline;"/>

					<div>
						The following tracking placeholders can be used:<br/>
						[[subid]], [[c1]], [[c2]], [[c3]], [[c4]]
					</div>
				</td>
			</tr>
			<tr id="rotateUrl2" <? if ($html['aff_campaign_rotate'] == 0) {
				echo ' style="display:none;" ';
			} ?>>
				<td class="left_caption">Rotate Url #2</td>
				<td><input type="text" name="aff_campaign_url_2" value="<? echo $html['aff_campaign_url_2']; ?>"
				           style="width: 200px; display: inline;"/></td>
			</tr>
			<tr id="rotateUrl3" <? if ($html['aff_campaign_rotate'] == 0) {
				echo ' style="display:none;" ';
			} ?>>
				<td class="left_caption">Rotate Url #3</td>
				<td><input type="text" name="aff_campaign_url_3" value="<? echo $html['aff_campaign_url_3']; ?>"
				           style="width: 200px; display: inline;"/></td>
			</tr>
			<tr id="rotateUrl4" <? if ($html['aff_campaign_rotate'] == 0) {
				echo ' style="display:none;" ';
			} ?>>
				<td class="left_caption">Rotate Url #4</td>
				<td><input type="text" name="aff_campaign_url_4" value="<? echo $html['aff_campaign_url_4']; ?>"
				           style="width: 200px; display: inline;"/></td>
			</tr>
			<tr id="rotateUrl5" <? if ($html['aff_campaign_rotate'] == 0) {
				echo ' style="display:none;" ';
			} ?>>
				<td class="left_caption">Rotate Url #5</td>
				<td><input type="text" name="aff_campaign_url_5" value="<? echo $html['aff_campaign_url_5']; ?>"
				           style="width: 200px; display: inline;"/></td>
			</tr>

			<tr>
				<td class="left_caption">Payout $</td>
				<td>
					<input type="text" name="aff_campaign_payout" size="4"
					       value="<? echo $html['aff_campaign_payout']; ?>" style="display: inline;"/>
				</td>
			</tr>
			<tr>
				<td class="left_caption">Cloaking</td>
				<td style="white-space: nowrap;">
					<select name="aff_campaign_cloaking">
						<option <? if ($html['aff_campaign_cloaking'] == '0') {
							echo 'selected=""';
						} ?> value="0">Off by default
						</option>
						<option <? if ($html['aff_campaign_cloaking'] == '1') {
							echo 'selected=""';
						} ?> value="1">On by default
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td/>
				<td>
					<input type="submit" value="<? if ($editing == true) {
						echo 'Edit';
					} else {
						echo 'Add';
					} ?>" style="display: inline;"/>
					<? if ($editing == true) { ?>
					<input type="submit" value="Cancel" style="display: inline; margin-left: 10px;"
					       onclick="window.location='/tracking202/setup/aff_campaigns.php'; return false; "/>
					<? } ?>
				</td>
			</tr>
		</table>
		<? echo $error['aff_network_id']; ?>
		<? echo $error['aff_campaign_name']; ?>
		<? echo $error['aff_campaign_url']; ?>
		<? echo $error['aff_campaign_payout']; ?>
		<? echo $error['wrong_user']; ?>
		<? echo $error['cloaking_url']; ?>
	</form>


</td>
<td class="setup-right">
	<h2 class="green">My Campaigns</h2>

	<ul>
		<?  $_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$aff_network_result = AffNetworks_DAO::find_not_deleted_by_user_id($user_id);



		if ($aff_network_result->count(true) == 0) {
			?>
			<li>You have not added any networks.</li><?
		}

		while ($aff_network_row = $aff_network_result->getNext()) {
			$html['aff_network_name'] = htmlentities($aff_network_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
			$url['aff_network_id'] = urlencode($aff_network_row['aff_network_id']);

			printf('<li>%s</li>', $html['aff_network_name']);

			?>
			<ul style="margin-top: 0px;"><?

				//print out the individual accounts per each PPC network
				$_values['aff_network_id'] = $aff_network_row['aff_network_id'];

				$aff_network_id = $_values['aff_network_id'];
				$aff_campaign_result = AffCampaigns_DAO::find_by_aff_network_id($aff_network_id);
				while ($aff_campaign_row = $aff_campaign_result->getNext()) {



					$html['aff_campaign_name'] = htmlentities($aff_campaign_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
					$html['aff_campaign_payout'] = htmlentities($aff_campaign_row['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');
					$html['aff_campaign_url'] = htmlentities($aff_campaign_row['aff_campaign_url'], ENT_QUOTES, 'UTF-8');
					$html['aff_campaign_id'] = htmlentities($aff_campaign_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');

					//todo check this format change %s
					printf('<li>%s &middot; &#36;%01.2f - <a href="%s" target="_new" style="font-size: 9px;">link</a> - <a href="?edit_aff_campaign_id=%s" style="font-size: 9px;">edit</a> - <a href="?delete_aff_campaign_id=%s" style="font-size: 9px;">remove</a></li>', $html['aff_campaign_name'], $html['aff_campaign_payout'], $html['aff_campaign_url'], $html['aff_campaign_id'], $html['aff_campaign_id']);

				}

				?></ul><?

		} ?>
	</ul>
</td>
</tr>
</table>

<? template_bottom();