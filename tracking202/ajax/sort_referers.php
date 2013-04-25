<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


//set the timezone for the user, for entering their dates.
AUTH::set_timezone($_SESSION['user_timezone']);

//show breakdown
runBreakdown(true);

//grab user time range preference    
$time = grab_timeframe();
$_values['to'] = $time['to'];
$_values['from'] = $time['from'];


//show real or filtered clicks
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$user_row = UsersPref_DAO::find_one_by_user_id4($user_id);




$breakdown = $user_row['user_pref_breakdown'];

$click_filtered = array(); //'';
if ($user_row['user_pref_show'] == 'all') {
}
if ($user_row['user_pref_show'] == 'real') {
	$click_filtered = array('click_filtered' => 0); //$click_filtered = " AND click_filtered='0' ";
}
if ($user_row['user_pref_show'] == 'filtered') {
	$click_filtered = array('click_filtered' => 1); //" AND click_filtered='1' ";
}
if ($user_row['user_pref_show'] == 'leads') {
	$click_filtered = array('click_lead' => 1); //" AND click_lead='1' ";
}

if ($user_row['user_cpc_or_cpv'] == 'cpv') {
	$cpv = true;
}
else {
	$cpv = false;
}


//only create a new list if there is no post
if (($_POST['order'] == '') and ($_POST['offset'] == '')) {

	//delete old referer list
	$_values['user_id'] = (int)$_SESSION['user_id'];

	$user_id = $_values['user_id'];
	$referer_result = SortReferers_DAO::remove_by_user_id($user_id);




	//lets build the new referer list
	//	$db_table = '2c';
	//					LEFT OUTER JOIN 202_site_urls AS 2su ON (2cs.click_referer_site_url_id = 2su.site_url_id)
	//					LEFT OUTER JOIN 202_site_domains AS 2sd ON (2sd.site_domain_id = 2su.site_domain_id)
	//			', $db_table, true, true, false, " $click_filtered GROUP BY 2sd.site_domain_id", false, false, true);

	$pref_time = true;
	$pref_adv = true;
	$pref_show = false;
	$key = 'referer_site_domain_id'; //todo fix add site domain id to each click(advance), otherwise need to do twice aggre
	$info_result = ClicksAdvance_DAO::aggre_run_grouped($pref_time, $pref_adv, $pref_show, $click_filtered, $key);


	//run query
	foreach ($info_result as $click_row) {
		$_values['referer_id'] = NameUtil::empty_to_0($click_row['referer_site_domain_id']);

		//get the stats
		$clicks = 0;
		$clicks = $click_row['clicks'];

		$total_clicks = $total_clicks + $clicks;

		//avg cpc and cost
		$avg_cpc = 0;
		$avg_cpc = $click_row['avg_cpc'];

		$cost = 0;
		$cost = $clicks * $avg_cpc;

		$total_cost = $total_cost + $cost;
		$total_avg_cpc = @round($total_cost / $total_clicks, 5);

		//leads
		$leads = 0;
		$leads = $click_row['leads'];

		$total_leads = $total_leads + $leads;

		//signup ratio
		$su_ratio - 0;
		$su_ratio = @round($leads / $clicks * 100, 2);

		$total_su_ratio = @round($total_leads / $total_clicks * 100, 2);

		//current payout
		$payout = 0;
		$payout = $info_row['click_payout'];

		//income
		$income = 0;
		$income = $click_row['income'];

		$total_income = $total_income + $income;

		//grab the EPC
		$epc = 0;
		$epc = @round($income / $clicks, 2);

		$total_epc = @round($total_income / $total_clicks, 2);

		//net income
		$net = 0;
		$net = $income - $cost;

		$total_net = $total_income - $total_cost;

		//roi
		$roi = 0;
		$roi = @round($net / $cost * 100);

		$total_roi = @round($total_net / $total_cost);

		//mysql escape vars
		$_values['sort_referer_clicks'] = $clicks;
		$_values['sort_referer_leads'] = $leads;
		$_values['sort_referer_su_ratio'] = $su_ratio;
		$_values['sort_referer_payout'] = $payout;
		$_values['sort_referer_avg_cpc'] = $avg_cpc;
		$_values['sort_referer_epc'] = $epc;
		$_values['sort_referer_income'] = $income;
		$_values['sort_referer_cost'] = $cost;
		$_values['sort_referer_net'] = $net;
		$_values['sort_referer_roi'] = $roi;

		//insert the data
		$referer_sort_result = SortReferers_DAO::create_by($_values);

	}

}


$html['order'] = htmlentities($_POST['order'], ENT_QUOTES, 'UTF-8');

$html['sort_referer_referer_order'] = 'referer asc';
if ($_POST['order'] == 'referer asc') {
	$html['sort_referer_referer_order'] = 'referer desc';
	$_values['order'] = '`site_domain_host` DESC';
} elseif ($_POST['order'] == 'referer desc') {
	$html['sort_referer_referer_order'] = 'referer ASC';
	$_values['order'] = '`site_domain_host` ASC';
}

$html['sort_referer_clicks_order'] = 'sort_referer_clicks asc';
if ($_POST['order'] == 'sort_referer_clicks asc') {
	$html['sort_referer_clicks_order'] = 'sort_referer_clicks desc';
	$_values['order'] = '`sort_referer_clicks` DESC';
} elseif ($_POST['order'] == 'sort_referer_clicks desc') {
	$html['sort_referer_clicks_order'] = 'sort_referer_clicks asc';
	$_values['order'] = '`sort_referer_clicks` ASC';
}

$html['sort_referer_leads_order'] = 'sort_referer_leads asc';
if ($_POST['order'] == 'sort_referer_leads asc') {
	$html['sort_referer_leads_order'] = 'sort_referer_leads desc';
	$_values['order'] = '`sort_referer_leads` DESC';
} elseif ($_POST['order'] == 'sort_referer_leads desc') {
	$html['sort_referer_leads_order'] = 'sort_referer_leads asc';
	$_values['order'] = '`sort_referer_leads` ASC';
}

$html['sort_referer_su_ratio_order'] = 'sort_referer_su_ratio asc';
if ($_POST['order'] == 'sort_referer_su_ratio asc') {
	$html['sort_referer_su_ratio_order'] = 'sort_referer_su_ratio desc';
	$_values['order'] = '`sort_referer_su_ratio` DESC';
} elseif ($_POST['order'] == 'sort_referer_su_ratio desc') {
	$html['sort_referer_su_ratio_order'] = 'sort_referer_su_ratio asc';
	$_values['order'] = '`sort_referer_su_ratio` ASC';
}

$html['sort_referer_payout_order'] = 'sort_referer_payout asc';
if ($_POST['order'] == 'sort_referer_payout asc') {
	$html['sort_referer_payout_order'] = 'sort_referer_payout desc';
	$_values['order'] = '`sort_referer_payout` DESC';
} elseif ($_POST['order'] == 'sort_referer_payout desc') {
	$html['sort_referer_payout_order'] = 'sort_referer_payout asc';
	$_values['order'] = '`sort_referer_payout` ASC';
}

$html['sort_referer_epc_order'] = 'sort_referer_epc asc';
if ($_POST['order'] == 'sort_referer_epc asc') {
	$html['sort_referer_epc_order'] = 'sort_referer_epc desc';
	$_values['order'] = '`sort_referer_epc` DESC';
} elseif ($_POST['order'] == 'sort_referer_epc desc') {
	$html['sort_referer_epc_order'] = 'sort_referer_epc asc';
	$_values['order'] = '`sort_referer_epc` ASC';
}

$html['sort_referer_avg_cpc_order'] = 'sort_referer_avg_cpc asc';
if ($_POST['order'] == 'sort_referer_avg_cpc asc') {
	$html['sort_referer_avg_cpc_order'] = 'sort_referer_avg_cpc desc';
	$_values['order'] = '`sort_referer_avg_cpc` DESC';
} elseif ($_POST['order'] == 'sort_referer_avg_cpc desc') {
	$html['sort_referer_avg_cpc_order'] = 'sort_referer_avg_cpc asc';
	$_values['order'] = '`sort_referer_avg_cpc` ASC';
}

$html['sort_referer_income_order'] = 'sort_referer_income asc';
if ($_POST['order'] == 'sort_referer_income asc') {
	$html['sort_referer_income_order'] = 'sort_referer_income desc';
	$_values['order'] = '`sort_referer_income` DESC';
} elseif ($_POST['order'] == 'sort_referer_income desc') {
	$html['sort_referer_income_order'] = 'sort_referer_income asc';
	$_values['order'] = '`sort_referer_income` ASC';
}

$html['sort_referer_cost_order'] = 'sort_referer_cost asc';
if ($_POST['order'] == 'sort_referer_cost asc') {
	$html['sort_referer_cost_order'] = 'sort_referer_cost desc';
	$_values['order'] = '`sort_referer_cost` DESC';
} elseif ($_POST['order'] == 'sort_referer_cost desc') {
	$html['sort_referer_cost_order'] = 'sort_referer_cost asc';
	$_values['order'] = '`sort_referer_cost` ASC';
}

$html['sort_referer_net_order'] = 'sort_referer_net asc';
if ($_POST['order'] == 'sort_referer_net asc') {
	$html['sort_referer_net_order'] = 'sort_referer_net desc';
	$_values['order'] = '`sort_referer_net` DESC';
} elseif ($_POST['order'] == 'sort_referer_net desc') {
	$html['sort_referer_net_order'] = 'sort_referer_net asc';
	$_values['order'] = '`sort_referer_net` ASC';
}

$html['sort_referer_roi_order'] = 'sort_referer_roi asc';
if ($_POST['order'] == 'sort_referer_roi asc') {
	$html['sort_referer_roi_order'] = 'sort_referer_roi desc';
	$_values['order'] = '`sort_referer_roi` DESC';
} elseif ($_POST['order'] == 'sort_referer_roi desc') {
	$html['sort_referer_roi_order'] = 'sort_referer_roi asc';
	$_values['order'] = '`sort_referer_roi` ASC';
}


if (empty($_values['order'])) {
	$_values['order'] = ' sort_referer_clicks DESC';
}
//$db_table = '202_sort_referers';
//
//$query = query('SELECT * FROM 202_sort_referers LEFT JOIN 202_site_domains ON (202_sort_referers.referer_id=202_site_domains.site_domain_id)', $db_table, false, false, false, $_values['order'], $_POST['offset'], true, true);
//$referer_sql = $query['click_sql'];
//$referer_result = mysql_query($referer_sql) or record_mysql_error($referer_sql);

$pref_time = false;
$pref_adv = false;
$pref_show = false;
$raw_order = $_values['order'];
$referer_result = ClicksAdvance_DAO::get_sort_things(SortReferers_DAO::_coll, $pref_time, $pref_adv, $pref_show, $raw_order);


//获取结果分页数据
$count = $referer_result->count(true);
$pref_limit = true;
$offset = (string)$_POST['offset'];
$query = ClicksAdvance_DAO::get_query_limit_and_pages($offset, $pref_limit, $count);
$referer_result = $referer_result->skip($query['skip'])->limit($query['limit']);

$html['from'] = htmlentities($query['from'], ENT_QUOTES, 'UTF-8');
$html['to'] = htmlentities($query['to'], ENT_QUOTES, 'UTF-8');
$html['rows'] = htmlentities($query['rows'], ENT_QUOTES, 'UTF-8');
?>

<table cellspacing="0" cellpadding="0" style="width: 100%; font-size: 12px;">
	<tr>
		<td width="100%;">
			<a target="_new" href="/tracking202/analyze/referers_download.php">
				<strong>Download to excel</strong>
				<img src="/202-img/icons/16x16/page_white_excel.png" style="margin: 0px 0px -3px 3px;"/>

			</a>
		</td>
		<td>
			<? printf('<div class="results">Results <b>%s - %s</b> of <b>%s</b></div>', $html['from'], $html['to'], $html['rows']); ?>
		</td>
	</tr>
</table>

<table cellpadding="0" cellspacing="1" class="m-stats">
	<tr>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_referer_order']; ?>');">Referer</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_clicks_order']; ?>');">Clicks</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_leads_order']; ?>');">Leads</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_su_ratio_order']; ?>');">S/U</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_payout_order']; ?>');">Payout</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_epc_order']; ?>');">EPC</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_avg_cpc_order']; ?>');">Avg
			CPC</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_income_order']; ?>');">Income</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_cost_order']; ?>');">Cost</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_net_order']; ?>');">Net</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_referers.php','','<? echo $html['sort_referer_roi_order']; ?>');">ROI</a>
		</th>
	</tr>

	<?

	while ($referer_row = $referer_result->getNext()) {
		$referer_row['site_domain_host'] = NameCatcher::get('site_domain_host', $referer_row['referer_id']);

		if (!$referer_row['site_domain_host']) {
			$html['referer'] = '[no referer]';
		} else {
			$html['referer'] = htmlentities($referer_row['site_domain_host'], ENT_QUOTES, 'UTF-8');
			//shorten referer
			/*if (strlen($html['referer']) > 25) {
			   $html['referer'] = substr($html['referer'],0,25) . '...';
		   }*/
			$html['site_domain_host'] = htmlentities($referer_row['site_domain_host']);
			$html['referer'] = '<a target="_new" title="' . $html['site_domain_host'] . '" href="http://' . $html['site_domain_host'] . '">' . $html['referer'] . '</a>';
		}

		error_reporting(0);

		$html['sort_referer_clicks'] = htmlentities($referer_row['sort_referer_clicks'], ENT_QUOTES, 'UTF-8');
		$html['sort_referer_leads'] = htmlentities($referer_row['sort_referer_leads'], ENT_QUOTES, 'UTF-8');
		$html['sort_referer_su_ratio'] = htmlentities($referer_row['sort_referer_su_ratio'] . '%', ENT_QUOTES, 'UTF-8');
		$html['sort_referer_payout'] = htmlentities(dollar_format($referer_row['sort_referer_payout']), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_epc'] = htmlentities(dollar_format($referer_row['sort_referer_epc']), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_avg_cpc'] = htmlentities(dollar_format($referer_row['sort_referer_avg_cpc'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_income'] = htmlentities(dollar_format($referer_row['sort_referer_income']), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_cost'] = htmlentities(dollar_format($referer_row['sort_referer_cost'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_net'] = htmlentities(dollar_format($referer_row['sort_referer_net'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_referer_roi'] = htmlentities($referer_row['sort_referer_roi'] . '%', ENT_QUOTES, 'UTF-8');

		error_reporting(6135); ?>

		<tr>
			<td class="m-row2  m-row2-fade"><? echo $html['referer']; ?></td>
			<td class="m-row1"><? echo $html['sort_referer_clicks']; ?></td>
			<td class="m-row1"><? echo $html['sort_referer_leads']; ?></td>
			<td class="m-row1"><? echo $html['sort_referer_su_ratio']; ?></td>
			<td class="m-row1"><? echo $html['sort_referer_payout']; ?></td>
			<td class="m-row3"><? echo $html['sort_referer_epc']; ?></td>
			<td class="m-row3"><? echo $html['sort_referer_avg_cpc']; ?></td>
			<td class="m-row4 "><? echo $html['sort_referer_income']; ?></td>
			<td class="m-row4 ">(<? echo $html['sort_referer_cost']; ?>)</td>
			<td class="<? if ($referer_row['sort_referer_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($referer_row['sort_referer_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_referer_net']; ?></td>
			<td class="<? if ($referer_row['sort_referer_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($referer_row['sort_referer_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_referer_roi']; ?></td>
		</tr>
		<? } ?>
</table>

<? if ($query['pages'] > 2) { ?>
<div class="offset">   <?
	if ($query['offset'] > 0) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_referers.php\',\'%s\',\'%s\');">First</a> ', $i, $html['order']);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_referers.php\',\'%s\',\'%s\');">Prev</a> ', $query['offset'] - 1, $html['order']);
	}

	if ($query['pages'] > 1) {
		for ($i = 0; $i < $query['pages'] - 1; $i++) {
			if (($i >= $query['offset'] - 10) and ($i < $query['offset'] + 11)) {
				if ($query['offset'] == $i) {
					$class = 'class="link_selected"';
				} else {
					$class = 'class="onclick_color"';
				}
				printf(' <a %s onclick="loadContent(\'/tracking202/ajax/sort_referers.php\',\'%s\',\'%s\');">%s</a> ', $class, $i, $html['order'], $i + 1);
				unset($class);
			}
		}
	}

	if ($query['offset'] < $query['pages'] - 2) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_referers.php\',\'%s\',\'%s\');"">Next</a> ', $query['offset'] + 1, $html['order']);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_referers.php\',\'%s\',\'%s\');">Last</a> ', $query['pages'] - 2, $html['order']);
	} ?>
</div>
<? } ?>
