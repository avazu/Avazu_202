<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//make sure user is logged in
AUTH::require_user();


//show real or filtered clicks
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$user_row = UsersPref_DAO::find_one_by_user_id4($user_id);




$breakdown = $user_row['user_pref_breakdown'];

if ($user_row['user_cpc_or_cpv'] == 'cpv') {
	$cpv = true;
}
else {
	$cpv = false;
}


//keywords already set in the table, just just download them
if (empty($_values['order'])) {
	$_values['order'] = ' sort_landing_page_clicks DESC';
}

//$db_table = '202_sort_landing_pages';
//$query = query('SELECT * FROM 202_sort_landing_pages LEFT JOIN 202_landing_pages USING (landing_page_id)', $db_table, false, false, false, $_values['order'], false, false, true);
//$keyword_sql = $query['click_sql'];
//$keyword_result = mysql_query($keyword_sql) or record_mysql_error($keyword_sql);

$pref_time = false;
$pref_adv = false;
$pref_show = false;
$raw_order = $_values['order'];
$landing_page_result = ClicksAdvance_DAO::get_sort_things(SortLandingPages_DAO::_coll, $pref_time, $pref_adv, $pref_show, $raw_order);


//获取结果分页数据
//$count = $landing_page_result->count(true);
//$pref_limit = false;
//$offset = false;
//$query = ClicksAdvance_DAO::get_query_limit_and_pages(false, false, $count);

header("Content-type: application/octet-stream");

# replace excelfile.xls with whatever you want the filename to default to
header("Content-Disposition: attachment; filename=T202_landing_pages_" . time() . ".xls");
header("Pragma: no-cache");
header("Expires: 0");


echo "Landing Page" . "\t" . "Clicks" . "\t" . "Click Throughs" . "\t" . "CTR" . "\t" . "Leads" . "\t" . "S/U" . "\t" . "Payout" . "\t" . "EPC" . "\t" . "Avg CPC" . "\t" . "Income" . "\t" . "Cost" . "\t" . "Net" . "\t" . "ROI" . "\n";


while ($landing_page_row = $landing_page_result->getNext()) {


	if (!$landing_page_row['landing_page_nickname']) {
		$landing_page_row['landing_page_nickname'] = '[direct link]';
	}

	echo
					$landing_page_row['landing_page_nickname'] . "\t" .
					$landing_page_row['sort_landing_page_clicks'] . "\t" .
					$landing_page_row['sort_landing_page_click_throughs'] . '%' . "\t" .
					$landing_page_row['sort_landing_page_ctr'] . "\t" .
					$landing_page_row['sort_landing_page_leads'] . "\t" .
					$landing_page_row['sort_landing_page_su_ratio'] . '%' . "\t" .
					dollar_format($landing_page_row['sort_landing_page_payout']) . "\t" .
					dollar_format($landing_page_row['sort_landing_page_epc']) . "\t" .
					dollar_format($landing_page_row['sort_landing_page_avg_cpc'], $cpv) . "\t" .
					dollar_format($landing_page_row['sort_landing_page_income']) . "\t" .
					dollar_format($landing_page_row['sort_landing_page_cost'], $cpv) . "\t" .
					dollar_format($landing_page_row['sort_landing_page_net'], $cpv) . "\t" .
					$landing_page_row['sort_landing_page_roi'] . '%' . "\n";

}
