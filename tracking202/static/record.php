<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//lets find out if this is an advance or simple landing page, so we can include the appropriate script for each
$landing_page_id_public = (int)$_GET['lpip'];
$tracker_row = LandingPages_DAO::find_one_by_id_public2($landing_page_id_public);



if (!$tracker_row) {
	die();
}

if ($tracker_row['landing_page_type'] == 0) {
	include_once($_SERVER['DOCUMENT_ROOT'] . '/tracking202/static/record_simple.php');
	die();
} elseif ($tracker_row['landing_page_type'] == 1) {
	include_once($_SERVER['DOCUMENT_ROOT'] . '/tracking202/static/record_adv.php');
	die();
}