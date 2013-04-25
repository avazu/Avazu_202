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

	//delete old text_ad list
	$_values['user_id'] = (int)$_SESSION['user_id'];

	$user_id = $_values['user_id'];
	$text_ad_result = SortTextAds_DAO::remove_by_user_id($user_id);



	//lets build the new text_ad list
	//					LEFT OUTER JOIN 202_text_ads AS 2ta ON (2ta.text_ad_id = 2ca.text_ad_id)
	//			', $db_table, true, true, false, " $click_filtered GROUP BY 2ta.text_ad_id", false, false, true);

	$pref_time = true;
	$pref_adv = true;
	$pref_show = false;
	$key = 'text_ad_id';
	$info_result = ClicksAdvance_DAO::aggre_run_grouped($pref_time, $pref_adv, $pref_show, $click_filtered, $key);


	//run query
	foreach ($info_result as $click_row) {
		$_values['text_ad_id'] = $click_row['text_ad_id'];

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
		$_values['sort_text_ad_clicks'] = $clicks;
		$_values['sort_text_ad_leads'] = $leads;
		$_values['sort_text_ad_su_ratio'] = $su_ratio;
		$_values['sort_text_ad_payout'] = $payout;
		$_values['sort_text_ad_avg_cpc'] = $avg_cpc;
		$_values['sort_text_ad_epc'] = $epc;
		$_values['sort_text_ad_income'] = $income;
		$_values['sort_text_ad_cost'] = $cost;
		$_values['sort_text_ad_net'] = $net;
		$_values['sort_text_ad_roi'] = $roi;

		//insert the data

		$text_ad_sort_result = SortTextAds_DAO::create_by($_values);




	}

}


$html['order'] = htmlentities($_POST['order'], ENT_QUOTES, 'UTF-8');

$html['sort_text_ad_text_ad_order'] = 'text_ad asc';
if ($_POST['order'] == 'text_ad asc') {
	$html['sort_text_ad_text_ad_order'] = 'text_ad desc';
	$_values['order'] = '`text_ad_name` DESC';
} elseif ($_POST['order'] == 'text_ad desc') {
	$html['sort_text_ad_text_ad_order'] = 'text_ad ASC';
	$_values['order'] = '`text_ad_name` ASC';
}

$html['sort_text_ad_clicks_order'] = 'sort_text_ad_clicks asc';
if ($_POST['order'] == 'sort_text_ad_clicks asc') {
	$html['sort_text_ad_clicks_order'] = 'sort_text_ad_clicks desc';
	$_values['order'] = '`sort_text_ad_clicks` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_clicks desc') {
	$html['sort_text_ad_clicks_order'] = 'sort_text_ad_clicks asc';
	$_values['order'] = '`sort_text_ad_clicks` ASC';
}

$html['sort_text_ad_leads_order'] = 'sort_text_ad_leads asc';
if ($_POST['order'] == 'sort_text_ad_leads asc') {
	$html['sort_text_ad_leads_order'] = 'sort_text_ad_leads desc';
	$_values['order'] = '`sort_text_ad_leads` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_leads desc') {
	$html['sort_text_ad_leads_order'] = 'sort_text_ad_leads asc';
	$_values['order'] = '`sort_text_ad_leads` ASC';
}

$html['sort_text_ad_su_ratio_order'] = 'sort_text_ad_su_ratio asc';
if ($_POST['order'] == 'sort_text_ad_su_ratio asc') {
	$html['sort_text_ad_su_ratio_order'] = 'sort_text_ad_su_ratio desc';
	$_values['order'] = '`sort_text_ad_su_ratio` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_su_ratio desc') {
	$html['sort_text_ad_su_ratio_order'] = 'sort_text_ad_su_ratio asc';
	$_values['order'] = '`sort_text_ad_su_ratio` ASC';
}

$html['sort_text_ad_payout_order'] = 'sort_text_ad_payout asc';
if ($_POST['order'] == 'sort_text_ad_payout asc') {
	$html['sort_text_ad_payout_order'] = 'sort_text_ad_payout desc';
	$_values['order'] = '`sort_text_ad_payout` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_payout desc') {
	$html['sort_text_ad_payout_order'] = 'sort_text_ad_payout asc';
	$_values['order'] = '`sort_text_ad_payout` ASC';
}

$html['sort_text_ad_epc_order'] = 'sort_text_ad_epc asc';
if ($_POST['order'] == 'sort_text_ad_epc asc') {
	$html['sort_text_ad_epc_order'] = 'sort_text_ad_epc desc';
	$_values['order'] = '`sort_text_ad_epc` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_epc desc') {
	$html['sort_text_ad_epc_order'] = 'sort_text_ad_epc asc';
	$_values['order'] = '`sort_text_ad_epc` ASC';
}

$html['sort_text_ad_avg_cpc_order'] = 'sort_text_ad_avg_cpc asc';
if ($_POST['order'] == 'sort_text_ad_avg_cpc asc') {
	$html['sort_text_ad_avg_cpc_order'] = 'sort_text_ad_avg_cpc desc';
	$_values['order'] = '`sort_text_ad_avg_cpc` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_avg_cpc desc') {
	$html['sort_text_ad_avg_cpc_order'] = 'sort_text_ad_avg_cpc asc';
	$_values['order'] = '`sort_text_ad_avg_cpc` ASC';
}

$html['sort_text_ad_income_order'] = 'sort_text_ad_income asc';
if ($_POST['order'] == 'sort_text_ad_income asc') {
	$html['sort_text_ad_income_order'] = 'sort_text_ad_income desc';
	$_values['order'] = '`sort_text_ad_income` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_income desc') {
	$html['sort_text_ad_income_order'] = 'sort_text_ad_income asc';
	$_values['order'] = '`sort_text_ad_income` ASC';
}

$html['sort_text_ad_cost_order'] = 'sort_text_ad_cost asc';
if ($_POST['order'] == 'sort_text_ad_cost asc') {
	$html['sort_text_ad_cost_order'] = 'sort_text_ad_cost desc';
	$_values['order'] = '`sort_text_ad_cost` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_cost desc') {
	$html['sort_text_ad_cost_order'] = 'sort_text_ad_cost asc';
	$_values['order'] = '`sort_text_ad_cost` ASC';
}

$html['sort_text_ad_net_order'] = 'sort_text_ad_net asc';
if ($_POST['order'] == 'sort_text_ad_net asc') {
	$html['sort_text_ad_net_order'] = 'sort_text_ad_net desc';
	$_values['order'] = '`sort_text_ad_net` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_net desc') {
	$html['sort_text_ad_net_order'] = 'sort_text_ad_net asc';
	$_values['order'] = '`sort_text_ad_net` ASC';
}

$html['sort_text_ad_roi_order'] = 'sort_text_ad_roi asc';
if ($_POST['order'] == 'sort_text_ad_roi asc') {
	$html['sort_text_ad_roi_order'] = 'sort_text_ad_roi desc';
	$_values['order'] = '`sort_text_ad_roi` DESC';
} elseif ($_POST['order'] == 'sort_text_ad_roi desc') {
	$html['sort_text_ad_roi_order'] = 'sort_text_ad_roi asc';
	$_values['order'] = '`sort_text_ad_roi` ASC';
}


if (empty($_values['order'])) {
	$_values['order'] = ' sort_text_ad_clicks DESC';
}
//$db_table = '202_sort_text_ads';
//
//$query = query('SELECT * FROM 202_sort_text_ads LEFT JOIN 202_text_ads USING (text_ad_id)', $db_table, false, false, false, $_values['order'], $_POST['offset'], true, true);
//$text_ad_sql = $query['click_sql'];
//$text_ad_result = mysql_query($text_ad_sql) or record_mysql_error($text_ad_sql);

$pref_time = false;
$pref_adv = false;
$pref_show = false;
$raw_order = $_values['order'];
$text_ad_result = ClicksAdvance_DAO::get_sort_things(SortTextAds_DAO::_coll, $pref_time, $pref_adv, $pref_show, $raw_order);


//获取结果分页数据
$count = $text_ad_result->count(true);
$pref_limit = true;
$offset = (string)$_POST['offset'];
$query = ClicksAdvance_DAO::get_query_limit_and_pages($offset, $pref_limit, $count);
$text_ad_result = $text_ad_result->skip($query['skip'])->limit($query['limit']);

$html['from'] = htmlentities($query['from'], ENT_QUOTES, 'UTF-8');
$html['to'] = htmlentities($query['to'], ENT_QUOTES, 'UTF-8');
$html['rows'] = htmlentities($query['rows'], ENT_QUOTES, 'UTF-8');  ?>

<table cellspacing="0" cellpadding="0" style="width: 100%; font-size: 12px;">
	<tr>
		<td width="100%;">
			<a target="_new" href="/tracking202/analyze/text_ads_download.php">
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
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_text_ad_order']; ?>');">Text
			Ad</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_clicks_order']; ?>');">Clicks</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_leads_order']; ?>');">Leads</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_su_ratio_order']; ?>');">S/U</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_payout_order']; ?>');">Payout</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_epc_order']; ?>');">EPC</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_avg_cpc_order']; ?>');">Avg
			CPC</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_income_order']; ?>');">Income</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_cost_order']; ?>');">Cost</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_net_order']; ?>');">Net</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_text_ads.php','','<? echo $html['sort_text_ad_roi_order']; ?>');">ROI</a>
		</th>
	</tr>

	<?

	while ($text_ad_row = $text_ad_result->getNext()) {
		$text_ad_row['text_ad_name'] = NameCatcher::get('text_ad_name', $text_ad_row['text_ad_id']);

		if (!$text_ad_row['text_ad_name']) {
			$html['text_ad'] = '[no text_ad]';
		} else {
			$html['text_ad'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
			//shorten text_ad
			/*if (strlen($html['text_ad']) > 25) {
	$html['text_ad'] = substr($html['text_ad'],0,25) . '...';
}*/
		}

		error_reporting(0);

		$html['sort_text_ad_clicks'] = htmlentities($text_ad_row['sort_text_ad_clicks'], ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_leads'] = htmlentities($text_ad_row['sort_text_ad_leads'], ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_su_ratio'] = htmlentities($text_ad_row['sort_text_ad_su_ratio'] . '%', ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_payout'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_payout']), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_epc'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_epc']), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_avg_cpc'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_avg_cpc'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_income'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_income']), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_cost'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_cost'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_net'] = htmlentities(dollar_format($text_ad_row['sort_text_ad_net'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_text_ad_roi'] = htmlentities($text_ad_row['sort_text_ad_roi'] . '%', ENT_QUOTES, 'UTF-8');

		error_reporting(6135); ?>

		<tr>
			<td class="m-row2  m-row2-fade"><? echo $html['text_ad']; ?></td>
			<td class="m-row1"><? echo $html['sort_text_ad_clicks']; ?></td>
			<td class="m-row1"><? echo $html['sort_text_ad_leads']; ?></td>
			<td class="m-row1"><? echo $html['sort_text_ad_su_ratio']; ?></td>
			<td class="m-row1"><? echo $html['sort_text_ad_payout']; ?></td>
			<td class="m-row3"><? echo $html['sort_text_ad_epc']; ?></td>
			<td class="m-row3"><? echo $html['sort_text_ad_avg_cpc']; ?></td>
			<td class="m-row4 "><? echo $html['sort_text_ad_income']; ?></td>
			<td class="m-row4 ">(<? echo $html['sort_text_ad_cost']; ?>)</td>
			<td class="<? if ($text_ad_row['sort_text_ad_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($text_ad_row['sort_text_ad_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_text_ad_net']; ?></td>
			<td class="<? if ($text_ad_row['sort_text_ad_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($text_ad_row['sort_text_ad_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_text_ad_roi']; ?></td>
		</tr>
		<? } ?>
</table>

<? if ($query['pages'] > 2) { ?>
<div class="offset">   <?
	if ($query['offset'] > 0) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_text_ads.php\',\'%s\',\'%s\');">First</a> ', $i, $html['order']);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_text_ads.php\',\'%s\',\'%s\');">Prev</a> ', $query['offset'] - 1, $html['order']);
	}

	if ($query['pages'] > 1) {
		for ($i = 0; $i < $query['pages'] - 1; $i++) {
			if (($i >= $query['offset'] - 10) and ($i < $query['offset'] + 11)) {
				if ($query['offset'] == $i) {
					$class = 'class="link_selected"';
				} else {
					$class = 'class="onclick_color"';
				}
				printf(' <a %s onclick="loadContent(\'/tracking202/ajax/sort_text_ads.php\',\'%s\',\'%s\');">%s</a> ', $class, $i, $html['order'], $i + 1);
				unset($class);
			}
		}
	}

	if ($query['offset'] < $query['pages'] - 2) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_text_ads.php\',\'%s\',\'%s\');"">Next</a> ', $query['offset'] + 1, $html['order']);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/sort_text_ads.php\',\'%s\',\'%s\');">Last</a> ', $query['pages'] - 2, $html['order']);
	} ?>
</div>
<? } ?>
