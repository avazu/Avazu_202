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


//ips already set in the table, just just download them
if (empty($_values['order'])) {
	$_values['order'] = ' sort_ip_clicks DESC';
}
//$db_table = '202_sort_ips';
//
//$query = query('SELECT * FROM 202_sort_ips LEFT JOIN 202_ips USING (ip_id)', $db_table, false, false, false, $_values['order'], false, false, true);

$pref_time = false;
$pref_adv = false;
$pref_show = false;
$raw_order = $_values['order'];
$ip_result = ClicksAdvance_DAO::get_sort_things(SortIps_DAO::_coll, $pref_time, $pref_adv, $pref_show, $raw_order);


//获取结果分页数据
//$count = $ip_result->count(true);
//$pref_limit = false;
//$offset = false;
//$query = ClicksAdvance_DAO::get_query_limit_and_pages(false, false, $count);

header("Content-type: application/octet-stream");

# replace excelfile.xls with whatever you want the filename to default to
header("Content-Disposition: attachment; filename=T202_ips_" . time() . ".xls");
header("Pragma: no-cache");
header("Expires: 0");


echo "ip" . "\t" . "Clicks" . "\t" . "Leads" . "\t" . "S/U" . "\t" . "Payout" . "\t" . "EPC" . "\t" . "Avg CPC" . "\t" . "Income" . "\t" . "Cost" . "\t" . "Net" . "\t" . "ROI" . "\n";


while ($ip_row = $ip_result->getNext()) {

	if (!$ip_row['ip_address']) {
		$ip_row['ip_address'] = '[no ip]';
	}

	echo
					$ip_row['ip_address'] . "\t" .
					$ip_row['sort_ip_clicks'] . "\t" .
					$ip_row['sort_ip_leads'] . "\t" .
					$ip_row['sort_ip_su_ratio'] . '%' . "\t" .
					dollar_format($ip_row['sort_ip_payout']) . "\t" .
					dollar_format($ip_row['sort_ip_epc']) . "\t" .
					dollar_format($ip_row['sort_ip_avg_cpc'], $cpv) . "\t" .
					dollar_format($ip_row['sort_ip_income']) . "\t" .
					dollar_format($ip_row['sort_ip_cost'], $cpv) . "\t" .
					dollar_format($ip_row['sort_ip_net'], $cpv) . "\t" .
					$ip_row['sort_ip_roi'] . '%' . "\n";

}
