<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

//set the timezone for this user.
AUTH::set_timezone($_SESSION['user_timezone']);

//grab the users date range preferences
$time = grab_timeframe();
$_values['to'] = $time['to'];
$_values['from'] = $time['from'];


//show real or filtered clicks
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$user_row = UsersPref_DAO::get($user_id);




$html['user_pref_group_1'] = htmlentities($user_row['user_pref_group_1'], ENT_QUOTES, 'UTF-8');
$html['user_pref_group_2'] = htmlentities($user_row['user_pref_group_2'], ENT_QUOTES, 'UTF-8');
$html['user_pref_group_3'] = htmlentities($user_row['user_pref_group_3'], ENT_QUOTES, 'UTF-8');
$html['user_pref_group_4'] = htmlentities($user_row['user_pref_group_4'], ENT_QUOTES, 'UTF-8');

if ($user_row['user_cpc_or_cpv'] == 'cpv') {
	$cpv = true;
} else {
	$cpv = false;
}

$summary_form = new ReportSummaryForm();
$summary_form->setDetails(array($user_row['user_pref_group_1'], $user_row['user_pref_group_2'], $user_row['user_pref_group_3'], $user_row['user_pref_group_4']));
$summary_form->setDetailsSort(array(ReportBasicForm::SORT_NAME));
$summary_form->setDisplayType(array(ReportBasicForm::DISPLAY_TYPE_TABLE));
$summary_form->setStartTime($_values['from']);
$summary_form->setEndTime($_values['to']);

?>

<h3 class="green overview-spacer">Group Overview</h3>
<div>
	<img src="/202-img/icons/16x16/page_white_excel.png" style="margin: 0px 0px -3px 3px;"/>
	<a target="_new" href="/tracking202/overview/group_overview_download.php">
		<strong>Download to excel</strong>
	</a>
</div>
<?

$_values['user_id'] = (int)$_SESSION['user_id'];

$info_result = $summary_form->run_goup_overview_report($user_row);
//echo "info result for group overview: ";
//print_r($info_result);
//while ($row = $info_result->getNext()) {
//	$summary_form->addReportData($row);
//}
foreach ($info_result as $row) {
	$summary_form->addReportData($row);
}

echo $summary_form->getHtmlReportResults('summary report');
?>
