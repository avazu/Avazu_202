<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

if ($_GET['edit_text_ad_id']) {
	$editing = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST['text_ad_type'] == 0) {

		//text ad type
		$aff_campaign_id = trim($_POST['aff_campaign_id']);
		if (empty($aff_campaign_id)) {
			$error['aff_campaign_id'] = '<div class="error">What campaign is this advertisement for?</div>';
		}


		//check to see if they are the owners of this affiliate network
		$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
		$_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$aff_campaign_id = $_values['aff_campaign_id'];
		$aff_campaign_result = AffCampaigns_DAO::find_by_id_and_user_id($aff_campaign_id, $user_id);
		if ($aff_campaign_result->count(true) == 0) {


			$error['wrong_user'] = '<div class="error">You are not authorized to add an campaign to another users network</div>';
		}

	}

	if ($_POST['text_ad_type'] == 1) {
		$landing_page_id = trim($_POST['landing_page_id']);
		if (empty($landing_page_id)) {
			$error['landing_page_id'] = '<div class="error">Please select a landing page.</div>';
		}
	}


	$text_ad_name = trim($_POST['text_ad_name']);
	if (empty($text_ad_name)) {
		$error['text_ad_name'] = '<div class="error">Give this ad variation a nickname</div>';
	}

	$text_ad_headline = trim($_POST['text_ad_headline']);
	if (empty($text_ad_headline)) {
		$error['text_ad_headline'] = '<div class="error">What is your ad headline?</div>';
	}

	$text_ad_description = trim($_POST['text_ad_description']);
	if (empty($text_ad_description)) {
		$error['text_ad_description'] = '<div class="error">What is your ad description?</div>';
	}

	$text_ad_display_url = trim($_POST['text_ad_display_url']);
	if (empty($text_ad_display_url)) {
		$error['text_ad_display_url'] = '<div class="error">What is your ad display URL?</div>';
	}


	//if editing, check to make sure the own the campaign they are editing
	if ($editing == true) {
		$_values['text_ad_id'] = (int)$_POST['text_ad_id'];
		$_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$text_ad_id = $_values['text_ad_id'];
		$text_ad_result = TextAds_DAO::find_by_id_and_user_id($text_ad_id, $user_id);
		if ($text_ad_result->count(true) == 0) {


			$error['wrong_user'] .= '<div class="error">You are not authorized to modify another users campaign</div>';
		}
	}

	if (!$error) {
		$_values['text_ad_id'] = (int)$_POST['text_ad_id'];
		$_values['text_ad_type'] = (int)$_POST['text_ad_type'];
		$_values['landing_page_id'] = (int)$_POST['landing_page_id'];
		$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
		$_values['text_ad_name'] = (string)$_POST['text_ad_name'];
		$_values['text_ad_headline'] = (string)$_POST['text_ad_headline'];
		$_values['text_ad_description'] = (string)$_POST['text_ad_description'];
		$_values['text_ad_display_url'] = (string)$_POST['text_ad_display_url'];
		$_values['user_id'] = (int)$_SESSION['user_id'];
		$_values['text_ad_time'] = time();

		if ($editing != true) {
			$_values['text_ad_id'] = -1;
		}
		$text_ad_result = TextAds_DAO::upsert_by($_values);

		$add_success = true;

		//if the edit worked ok redirec them
		if ($editing == true) {
			header('location: /tracking202/setup/text_ads.php');

		}

		$editing = false;


	}
}

if (isset($_GET['delete_text_ad_id'])) {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['text_ad_id'] = (int)$_GET['delete_text_ad_id'];
	$_values['text_ad_time'] = time();


	$text_ad_time = $_values['text_ad_time'];
	$user_id = $_values['user_id'];
	$text_ad_id = $_values['text_ad_id'];
	$delete_result = TextAds_DAO::update_by_id_and_time_and_user_id($text_ad_id, $text_ad_time, $user_id);




	if ($delete_result) {
		$delete_success = true;
	}
}

if ($_GET['edit_text_ad_id']) {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['text_ad_id'] = (int)$_GET['edit_text_ad_id'];


	$text_ad_id = $_values['text_ad_id'];
	$user_id = $_values['user_id'];
	$text_ad_row = TextAds_DAO::find_one_by_id_and_user_id($text_ad_id, $user_id);




	$_values['aff_campaign_id'] = $text_ad_row['aff_campaign_id'];
	$html['landing_page_id'] = htmlentities($text_ad_row['landing_page_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_type'] = htmlentities($text_ad_row['text_ad_type'], ENT_QUOTES, 'UTF-8');
	$html['aff_campaign_id'] = htmlentities($text_ad_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_id'] = htmlentities($_GET['edit_text_ad_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_headline'] = htmlentities($text_ad_row['text_ad_headline'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_description'] = htmlentities($text_ad_row['text_ad_description'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_display_url'] = htmlentities($text_ad_row['text_ad_display_url'], ENT_QUOTES, 'UTF-8');


} elseif ($_GET['copy_text_ad_id']) {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['text_ad_id'] = (int)$_GET['copy_text_ad_id'];


	$text_ad_id = $_values['text_ad_id'];
	$user_id = $_values['user_id'];
	$text_ad_row = TextAds_DAO::find_one_by_id_and_user_id($text_ad_id, $user_id);




	$html['text_ad_type'] = htmlentities($text_ad_row['text_ad_type'], ENT_QUOTES, 'UTF-8');
	$html['landing_page_id'] = htmlentities($text_ad_row['landing_page_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_headline'] = htmlentities($text_ad_row['text_ad_headline'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_description'] = htmlentities($text_ad_row['text_ad_description'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_display_url'] = htmlentities($text_ad_row['text_ad_display_url'], ENT_QUOTES, 'UTF-8');


} elseif (($_SERVER['REQUEST_METHOD'] == 'POST') and ($add_success != true)) {

	$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];
	$html['aff_campaign_id'] = htmlentities($_POST['aff_campaign_id'], ENT_QUOTES, 'UTF-8');

	$html['text_ad_type'] = htmlentities($_POST['text_ad_type'], ENT_QUOTES, 'UTF-8');
	$html['landing_page_id'] = htmlentities($_POST['landing_page_id'], ENT_QUOTES, 'UTF-8');
	$html['aff_network_id'] = htmlentities($_POST['aff_network_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_id'] = htmlentities($_POST['text_ad_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_name'] = htmlentities($_POST['text_ad_name'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_headline'] = htmlentities($_POST['text_ad_headline'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_description'] = htmlentities($_POST['text_ad_description'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_display_url'] = htmlentities($_POST['text_ad_display_url'], ENT_QUOTES, 'UTF-8');

}

if ((($editing == true) or ($add_success != true)) and ($_values['aff_campaign_id'])) {
	//now grab the affiliate network id, per that aff campaign id

	$aff_campaign_id = $_values['aff_campaign_id'];
	$aff_campaign_row = AffCampaigns_DAO::get($aff_campaign_id);




	$_values['aff_network_id'] = $aff_campaign_row['aff_network_id'];

	$aff_network_id = $_values['aff_network_id'];
	$aff_network_row = AffNetworks_DAO::get($aff_network_id);




	$html['aff_network_id'] = htmlentities($aff_network_row['aff_network_id'], ENT_QUOTES, 'UTF-8');
}

template_top('Text Ads Setup', NULL, NULL, NULL);  ?>

<div id="info">
	<h2>Text Ad Setup (optional)</h2>
	Here is where you enter in your text ad information. If you have too many text-ads and do not want to enter them
	all, you can skip this step.
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
		<input name="text_ad_id" type="hidden" value="<? echo $html['text_ad_id']; ?>"/>
		<table>
			<tr valign="top">
				<td colspan="2">
					<h2 class="green">Add Your Text Ads</h2>

					<p style="text-align: justify;">Here you can add different text ads you might use with your PPC
						marketing.</p>
				</td>
			</tr>

			<tr>
				<td/>
				<br/></tr>

			<tr valign="top">
				<td class="left_caption">Text Ad For</td>
				<td>
					<input type="radio" name="text_ad_type"
					       value="0" <? if ($html['text_ad_type'] == '0' or !$html['text_ad_type']) {
						echo ' CHECKED ';
					}  ?> onClick="text_ad_select(this.value);"> Direct Link Setup, or Simple Landing Page Setup<br/>
					<input type="radio" name="text_ad_type" value="1" <? if ($html['text_ad_type'] == '1') {
						echo ' CHECKED ';
					} ?> onClick="text_ad_select(this.value);"> Advanced Landing Page Setup
					<? echo $error['landing_page_type']; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<hr/>
				</td>
			</tr>
			<tr id="lp_landing_page" <? if (($html['text_ad_type'] == '0') or (!$html['text_ad_type'])) {
				echo ' style="display:none;"';
			} ?>>
				<td class="left_caption">Landing Page</td>
				<td>
					<img id="landing_page_div_loading" style="display: none;" src="/202-img/loader-small.gif"/>

					<div id="landing_page_div" style="display: none;"></div>
					<? echo $error['landing_page_id']; ?>
				</td>
			</tr>

			<tr id="lp_aff_network" <? if ($html['text_ad_type'] == '1') {
				echo ' style="display:none;"';
			} ?>>
				<td class="left_caption">Aff Network</td>
				<td>
					<img id="aff_network_id_div_loading" src="/202-img/loader-small.gif"/>

					<div id="aff_network_id_div" style="display: inline;"></div>
				</td>
			</tr>
			<tr id="lp_aff_campaign" <? if ($html['text_ad_type'] == '1') {
				echo ' style="display:none;"';
			} ?>>
				<td class="left_caption">Aff Campaign</td>
				<td>
					<img id="aff_campaign_id_div_loading" src="/202-img/loader-small.gif" style="display: none;"/>

					<div id="aff_campaign_id_div" style="display: inline;"></div>
					<? echo $error['aff_campaign_id']; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="left_caption">Ad Nickname <a class="onclick_color"
				                                        onclick="alert('The ad nickname is the nickname we store for you, this is used for when you have several ads, you can quickly find the ones you are looking for by assigning each ad a unique nickname.');">?</a>
				</td>
				<td>
					<input type="text" name="text_ad_name" value="<? echo $html['text_ad_name']; ?>"
					       style="width: 200px;"/>
					<? echo $error['text_ad_name']; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="left_caption">Ad Preview</td>
				<td>
					<table class="ad_copy" cellspacing="0" cellpadding="3">
						<tr>
							<td valign="bottom">
								<div id="preview_headline" class="ad_copy_headline"><? if ($html['text_ad_headline']) {
									echo $html['text_ad_headline'];
								} else {
									echo 'Luxury Cruise to Mars';
								} ?></div>
								<div id="preview_description"
								     class="ad_copy_description"><? if ($html['text_ad_description']) {
									echo $html['text_ad_description'];
								} else {
									echo 'Visit the Red Planet in style. Low-gravity fun for everyone!';
								} ?></div>
								<div id="preview_display_url"
								     class="ad_copy_display_url"><? if ($html['text_ad_display_url']) {
									echo $html['text_ad_display_url'];
								} else {
									echo 'www.example.com';
								} ?></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr valign="top">
				<td class="left_caption">Ad Headline</td>
				<td>
					<input type="text" name="text_ad_headline" style="width: 200px;"
					       onkeyup="document.getElementById('preview_headline').innerHTML=this.value; if (document.getElementById('preview_headline').innerHTML=='') { document.getElementById('preview_headline').innerHTML='Luxury Cruise to Mars'; }"
					       onchange="document.getElementById('preview_headline').innerHTML=this.value; if (document.getElementById('preview_headline').innerHTML=='') { document.getElementById('preview_headline').innerHTML='Luxury Cruise to Mars'; }"
					       value="<? echo $html['text_ad_headline']; ?>"/>
					<? echo $error['text_ad_headline']; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="left_caption">Ad Description</td>
				<td>
					<textarea name="text_ad_description" style="width: 200px; height: 50px;"
					          onkeyup="document.getElementById('preview_description').innerHTML=this.value; if (document.getElementById('preview_description').innerHTML=='') { document.getElementById('preview_description').innerHTML='Visit the Red Planet in style. Low-gravity fun for everyone!'; }"
					          onchange="document.getElementById('preview_description').innerHTML=this.value; if (document.getElementById('preview_description').innerHTML=='') { document.getElementById('preview_description').innerHTML='Visit the Red Planet in style. Low-gravity fun for everyone!'; }"><? echo $html['text_ad_description']; ?></textarea>
					<? echo $error['text_ad_description']; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="left_caption">Display URL</td>
				<td>
					<input type="text" name="text_ad_display_url" style="width: 200px; display: inline;"
					       onkeyup="document.getElementById('preview_display_url').innerHTML=this.value; if (document.getElementById('preview_display_url').innerHTML=='') { document.getElementById('preview_display_url').innerHTML='www.example.com'; }"
					       onchange="document.getElementById('preview_display_url').innerHTML=this.value; if (document.getElementById('preview_display_url').innerHTML=='') { document.getElementById('preview_display_url').innerHTML='www.example.com'; }"
					       value="<? echo $html['text_ad_display_url']; ?>"/>
					<? echo $error['text_ad_display_url']; ?>
				</td>
			</tr>
			<tr valign="top">
				<td/>
				<td>
					<input type="submit" value="<? if ($editing == true) {
						echo 'Edit';
					} else {
						echo 'Add';
					} ?>"/>
					<? if ($editing == true or $_GET['copy_text_ad_id'] != '') { ?>
					<button style="display: inline; margin-left: 10px;"
					        onclick="window.location='/tracking202/setup/text_ads.php'; return false; ">Cancel
					</button>
					<? } ?>
				</td>
			</tr>
		</table>
	</form>
	<? echo $error['text_ad_id']; ?>
	<? echo $error['wrong_user']; ?>

</td>
<td class="setup-right">
	<h2 class="green">Advanced Landing Page Text Ads</h2>
	<ul>
		<? $_values['user_id'] = (int)$_SESSION['user_id'];

		$user_id = $_values['user_id'];
		$landing_page_result = LandingPages_DAO::find_by_user_id1($user_id);
		while ($landing_page_row = $landing_page_result->getNext()) {


			$html['landing_page_nickname'] = htmlentities($landing_page_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');

			printf('<li>%s</li>', $html['landing_page_nickname']);

			?>
			<ul style="margin-top: 0px;"><?

				$_values['landing_page_id'] = $landing_page_row['landing_page_id'];

				$landing_page_id = $_values['landing_page_id'];
				$text_ad_result = TextAds_DAO::find_by_landing_page_id($landing_page_id);
				while ($text_ad_row = $text_ad_result->getNext()) {



					$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
					$html['text_ad_id'] = htmlentities($text_ad_row['text_ad_id'], ENT_QUOTES, 'UTF-8');

					printf('<li>%s - <a href="?copy_text_ad_id=%s" style="font-size: 9px;">copy</a> - <a href="?edit_text_ad_id=%s" style="font-size: 9px;">edit</a> - <a href="?delete_text_ad_id=%s" style="font-size: 9px;">remove</a></li>', $html['text_ad_name'], $html['text_ad_id'], $html['text_ad_id'], $html['text_ad_id']);


				}

				?></ul>
			<? } ?>

	</ul>
	<br/><br/>

	<h2 class="green">Direct Link/Simple Landing Page Text Ads</h2>
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

					printf('<li>%s &middot; &#36;%01.2f</li>', $html['aff_campaign_name'], $html['aff_campaign_payout']);

					?>
					<ul style="margin-top: 0px;"><?

						$_values['aff_campaign_id'] = $aff_campaign_row['aff_campaign_id'];

						$aff_campaign_id = $_values['aff_campaign_id'];
						$text_ad_result = TextAds_DAO::find_by_aff_campaign_id($aff_campaign_id);
						while ($text_ad_row = $text_ad_result->getNext()) {



							$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
							$html['text_ad_id'] = htmlentities($text_ad_row['text_ad_id'], ENT_QUOTES, 'UTF-8');

							printf('<li>%s - <a href="?copy_text_ad_id=%s" style="font-size: 9px;">copy</a> - <a href="?edit_text_ad_id=%s" style="font-size: 9px;">edit</a> - <a href="?delete_text_ad_id=%s" style="font-size: 9px;">remove</a></li>', $html['text_ad_name'], $html['text_ad_id'], $html['text_ad_id'], $html['text_ad_id']);


						}

						?></ul><?
				}

				?></ul><?

		} ?>
	</ul>
</td>
</tr>
</table>

<!-- open up the ajax aff network -->
<script type="text/javascript">

	load_landing_page(0, <? echo $html['landing_page_id']; if (!$html['landing_page_id']) {
		echo 0;
	} ?>, 'advlandingpage');

	load_aff_network_id('<? echo $html['aff_network_id']; ?>');
	<? if ($html['aff_network_id'] != '') { ?>
	load_aff_campaign_id('<? echo $html['aff_network_id']; ?>', '<? echo $html['aff_campaign_id']; ?>');
		<? } ?>

	function text_ad_select(text_ad_type) {
		if (text_ad_type == '0') {
			$('lp_landing_page').style.display = 'none';
			load_landing_page(0, 0, '');
			$('lp_aff_network').style.display = 'table-row';
			$('lp_aff_campaign').style.display = 'table-row';
		} else if (text_ad_type == '1') {
			$('lp_landing_page').style.display = 'table-row';
			load_landing_page(0, 0, 'advlandingpage');
			$('lp_aff_network').style.display = 'none';
			$('lp_aff_campaign').style.display = 'none';
			load_aff_network_id(0);
			load_aff_campaign_id(0, 0);
		}
	}

</script>



<? template_bottom();