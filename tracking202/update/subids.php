<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$_values['user_id'] = (int)$_SESSION['user_id'];
	$_values['click_update_type'] = 'upload';
	$_values['click_update_time'] = time();

	$subids = (string)$_POST['subids'];
	$subids = trim($subids);
	$subids = explode("\r", $subids);
	$subids = str_replace("\n", '', $subids);

	foreach ($subids as $key => $click_id) {
		$_values['click_id'] = (int)$click_id;


		$click_id = $_values['click_id'];
		$user_id = $_values['user_id'];
		$click_row = ClicksAdvance_DAO::find_one_by_id_and_user_id($click_id, $user_id);

		$_values['click_id'] = $click_row['click_id'];

		if (is_numeric($_values['click_id'])) {

			$click_id = $_values['click_id'];
			$user_id = $_values['user_id'];
			$update_result = ClicksAdvance_DAO::update_by_id_and_user_id1($click_id, $user_id);

			//$update_result = ClicksSpy_DAO::update_by_click_id_and_user_id1($click_id, $user_id);
		}
	}

	$success = true;

	//this deletes all this users cached data to the old result sets, we want new stuff because they just updated old clicks
	//memcache_delete_user_keys();
}

//show the template
template_top('Update Subids'); ?>

<div id="info">
	<h2>Update Your Subids</h2>
	Here is where you can update your income for tracking202, by importing your subids from your affiliate marketing
	reports.
</div>

<div style="margin: 15px 30px; padding: 20px; border: 1px solid rgb(175,175,175); font-size: 1.5em; text-align: center; background: rgb(244,244,244); ">
	<img src="/202-img/icons/16x16/new.png" style="margin: 0px 6px -4px 0px;" title="new" alt="new"/>You can now
	automatically update your subids by setting up your <a href="/stats202/postback/">Stats202</a> Postback URL.
</div>

<? if ($success == true) { ?>
<div class="success">
	<div><h3>Your submission was successful</h3>Your account income now reflects the subids from the commisisons you
		just uploaded.
	</div>
</div>
<? } ?>
<div id="m-content">
	<form method="post" action="">
		<table cellpadding="0" cellspacing="1" class="m-stats">
			<tr>
				<th>Subids</th>
			</tr>
			<tr valign="top">
				<td><textarea name="subids"
				              style="height: 200px; width: 100%; margin: 0px auto;"><? echo $_POST['subids']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="m-row-bottom">
					<input type="submit" value="Update Subids"/>
				</td>
			</tr>
		</table>
	</form>
</div>
<? template_bottom();