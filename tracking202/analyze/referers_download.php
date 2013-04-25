<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

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
	$_values['order'] = ' sort_referer_clicks DESC';
}
//$db_table = '202_sort_referers';
//
//$query = query('SELECT * FROM 202_sort_referers LEFT JOIN 202_site_domains ON (202_sort_referers.referer_id=202_site_domains.site_domain_id)', $db_table, false, false, false, $_values['order'], false, false, true);
//$keyword_sql = $query['click_sql'];
//$keyword_result = mysql_query($keyword_sql) or record_mysql_error($keyword_sql);

$pref_time = false;
$pref_adv = false;
$pref_show = false;
$raw_order = $_values['order'];
$referer_result = ClicksAdvance_DAO::get_sort_things(SortReferers_DAO::_coll, $pref_time, $pref_adv, $pref_show, $raw_order);


//获取结果分页数据
//$count = $referer_result->count(true);
//$pref_limit = false;
//$offset = false;
//$query = ClicksAdvance_DAO::get_query_limit_and_pages(false, false, $count);

header("Content-type: application/octet-stream");

# replace excelfile.xls with whatever you want the filename to default to
header("Content-Disposition: attachment; filename=T202_referers_" . time() . ".xls");
header("Pragma: no-cache");
header("Expires: 0");


echo "Refering Domain" . "\t" . "Clicks" . "\t" . "Leads" . "\t" . "S/U" . "\t" . "Payout" . "\t" . "EPC" . "\t" . "Avg CPC" . "\t" . "Income" . "\t" . "Cost" . "\t" . "Net" . "\t" . "ROI" . "\n";


while ($referer_row = $referer_result->getNext()) {


	if (!$referer_row['site_domain_host']) {
		$referer_row['site_domain_host'] = '[no referer]';
	}

	echo
					$referer_row['site_domain_host'] . "\t" .
					$referer_row['sort_referer_clicks'] . "\t" .
					$referer_row['sort_referer_leads'] . "\t" .
					$referer_row['sort_referer_su_ratio'] . '%' . "\t" .
					dollar_format($referer_row['sort_referer_payout']) . "\t" .
					dollar_format($referer_row['sort_referer_epc']) . "\t" .
					dollar_format($referer_row['sort_referer_avg_cpc'], $cpv) . "\t" .
					dollar_format($referer_row['sort_referer_income']) . "\t" .
					dollar_format($referer_row['sort_referer_cost'], $cpv) . "\t" .
					dollar_format($referer_row['sort_referer_net'], $cpv) . "\t" .
					$referer_row['sort_referer_roi'] . '%' . "\n";

}
