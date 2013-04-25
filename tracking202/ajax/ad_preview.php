<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$_values['text_ad_id'] = (int)$_POST['text_ad_id'];
	$_values['user_id'] = (int)$_SESSION['user_id'];


	$text_ad_id = $_values['text_ad_id'];
	$user_id = $_values['user_id'];
	$text_ad_row = TextAds_DAO::find_one_by_id_and_user_id($text_ad_id, $user_id);



	if ($text_ad_row) {
		$html['text_ad_headline'] = htmlentities($text_ad_row['text_ad_headline'], ENT_QUOTES, 'UTF-8');
		$html['text_ad_description'] = htmlentities($text_ad_row['text_ad_description'], ENT_QUOTES, 'UTF-8');
		$html['text_ad_display_url'] = htmlentities($text_ad_row['text_ad_display_url'], ENT_QUOTES, 'UTF-8'); ?>

	<table id="ad_preview" class="ad_copy" cellspacing="0" cellpadding="3">
		<tr>
			<td valign="bottom" style="white-space: normal;">
				<div class="ad_copy_headline"><? echo $html['text_ad_headline']; ?></div>
				<div class="ad_copy_description"><? echo $html['text_ad_description']; ?></div>
				<div class="ad_copy_display_url"><? echo $html['text_ad_display_url']; ?></div>
			</td>
		</tr>
	</table>

	<?
	}
} ?>  
 