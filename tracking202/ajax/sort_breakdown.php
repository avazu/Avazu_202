<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


//set the timezone for the user, for entering their dates.
AUTH::set_timezone($_SESSION['user_timezone']);

//show breakdown
runBreakdown(true);


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


//run the order by settings	
$html['order'] = htmlentities($_POST['order'], ENT_QUOTES, 'UTF-8');

$html['sort_breakdown_order'] = 'breakdown asc';
if ($_POST['order'] == 'breakdown asc') {
	$html['sort_breakdown_order'] = 'breakdown desc';
	$_values['order'] = 'sort_breakdown_from DESC';
} elseif ($_POST['order'] == 'breakdown desc') {
	$html['sort_breakdown_order'] = 'breakdown asc';
	$_values['order'] = 'sort_breakdown_from ASC';
}

$html['sort_breakdown_clicks_order'] = 'sort_breakdown_clicks asc';
if ($_POST['order'] == 'sort_breakdown_clicks asc') {
	$html['sort_breakdown_clicks_order'] = 'sort_breakdown_clicks desc';
	$_values['order'] = '`sort_breakdown_clicks` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_clicks desc') {
	$html['sort_breakdown_clicks_order'] = 'sort_breakdown_clicks asc';
	$_values['order'] = '`sort_breakdown_clicks` ASC';
}

$html['sort_breakdown_leads_order'] = 'sort_breakdown_leads asc';
if ($_POST['order'] == 'sort_breakdown_leads asc') {
	$html['sort_breakdown_leads_order'] = 'sort_breakdown_leads desc';
	$_values['order'] = '`sort_breakdown_leads` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_leads desc') {
	$html['sort_breakdown_leads_order'] = 'sort_breakdown_leads asc';
	$_values['order'] = '`sort_breakdown_leads` ASC';
}

$html['sort_breakdown_su_ratio_order'] = 'sort_breakdown_su_ratio asc';
if ($_POST['order'] == 'sort_breakdown_su_ratio asc') {
	$html['sort_breakdown_su_ratio_order'] = 'sort_breakdown_su_ratio desc';
	$_values['order'] = '`sort_breakdown_su_ratio` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_su_ratio desc') {
	$html['sort_breakdown_su_ratio_order'] = 'sort_breakdown_su_ratio asc';
	$_values['order'] = '`sort_breakdown_su_ratio` ASC';
}

$html['sort_breakdown_payout_order'] = 'sort_breakdown_payout asc';
if ($_POST['order'] == 'sort_breakdown_payout asc') {
	$html['sort_breakdown_payout_order'] = 'sort_breakdown_payout desc';
	$_values['order'] = '`sort_breakdown_payout` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_payout desc') {
	$html['sort_breakdown_payout_order'] = 'sort_breakdown_payout asc';
	$_values['order'] = '`sort_breakdown_payout` ASC';
}

$html['sort_breakdown_epc_order'] = 'sort_breakdown_epc asc';
if ($_POST['order'] == 'sort_breakdown_epc asc') {
	$html['sort_breakdown_epc_order'] = 'sort_breakdown_epc desc';
	$_values['order'] = '`sort_breakdown_epc` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_epc desc') {
	$html['sort_breakdown_epc_order'] = 'sort_breakdown_epc asc';
	$_values['order'] = '`sort_breakdown_epc` ASC';
}

$html['sort_breakdown_cpc_order'] = 'sort_breakdown_cpc asc';
if ($_POST['order'] == 'sort_breakdown_cpc asc') {
	$html['sort_breakdown_cpc_order'] = 'sort_breakdown_cpc desc';
	$_values['order'] = '`sort_breakdown_cpc` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_cpc desc') {
	$html['sort_breakdown_cpc_order'] = 'sort_breakdown_cpc asc';
	$_values['order'] = '`sort_breakdown_cpc` ASC';
}

$html['sort_breakdown_income_order'] = 'sort_breakdown_income asc';
if ($_POST['order'] == 'sort_breakdown_income asc') {
	$html['sort_breakdown_income_order'] = 'sort_breakdown_income desc';
	$_values['order'] = '`sort_breakdown_income` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_income desc') {
	$html['sort_breakdown_income_order'] = 'sort_breakdown_income asc';
	$_values['order'] = '`sort_breakdown_income` ASC';
}

$html['sort_breakdown_cost_order'] = 'sort_breakdown_cost asc';
if ($_POST['order'] == 'sort_breakdown_cost asc') {
	$html['sort_breakdown_cost_order'] = 'sort_breakdown_cost desc';
	$_values['order'] = '`sort_breakdown_cost` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_cost desc') {
	$html['sort_breakdown_cost_order'] = 'sort_breakdown_cost asc';
	$_values['order'] = '`sort_breakdown_cost` ASC';
}

$html['sort_breakdown_net_order'] = 'sort_breakdown_net asc';
if ($_POST['order'] == 'sort_breakdown_net asc') {
	$html['sort_breakdown_net_order'] = 'sort_breakdown_net desc';
	$_values['order'] = '`sort_breakdown_net` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_net desc') {
	$html['sort_breakdown_net_order'] = 'sort_breakdown_net asc';
	$_values['order'] = '`sort_breakdown_net` ASC';
}

$html['sort_breakdown_roi_order'] = 'sort_breakdown_roi asc';
if ($_POST['order'] == 'sort_breakdown_roi asc') {
	$html['sort_breakdown_roi_order'] = 'sort_breakdown_roi desc';
	$_values['order'] = '`sort_breakdown_roi` DESC';
} elseif ($_POST['order'] == 'sort_breakdown_roi desc') {
	$html['sort_breakdown_roi_order'] = 'sort_breakdown_roi asc';
	$_values['order'] = '`sort_breakdown_roi` ASC';
}

if (empty($_values['order'])) {
	$_values['order'] = ' sort_breakdown_from ASC';
}
?>

<table cellpadding="0" cellspacing="1" class="m-stats">
	<tr>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_order']; ?>');">Time</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_clicks_order']; ?>');">Clicks</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_leads_order']; ?>');">Leads</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_su_ratio_order']; ?>');">Avg
			S/U</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_payout_order']; ?>');">Avg
			Payout</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_epc_order']; ?>');">Avg
			EPC</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_avg_cpc_order']; ?>');">Avg
			CPC</a></th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_income_order']; ?>');">Income</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_cost_order']; ?>');">Cost</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_net_order']; ?>');">Net</a>
		</th>
		<th><a class="onclick_color"
		       onclick="loadContent('/tracking202/ajax/sort_breakdown.php','','<? echo $html['sort_breakdown_roi_order']; ?>');">ROI</a>
		</th>
	</tr>

	<?
//grab breakdown report

	$user_id = $_values['user_id'];
	$order = $_values['order'];
	$breakdown_result = SortBreakdowns_DAO::find_by_order_and_user_id($order, $user_id);

	while ($breakdown_row = $breakdown_result->getNext()) {

		DU::dump($breakdown_row, __FILE__);

		//also harvest a total stats
		$stats_total['clicks'] = $stats_total['clicks'] + $breakdown_row['sort_breakdown_clicks'];
		$stats_total['leads'] = $stats_total['leads'] + $breakdown_row['sort_breakdown_leads'];
		$stats_total['payout'] = $stats_total['payout'] + $breakdown_row['sort_breakdown_payout'];
		$stats_total['income'] = $stats_total['income'] + $breakdown_row['sort_breakdown_income'];
		$stats_total['cost'] = $stats_total['cost'] + $breakdown_row['sort_breakdown_cost'];
		$stats_total['net'] = $stats_total['net'] + $breakdown_row['sort_breakdown_net'];

		if ($breakdown == 'hour') {
			$html['sort_breakdown_time'] = date('M d, Y \a\t g:ia', $breakdown_row['sort_breakdown_from']);
		} elseif ($breakdown == 'day') {
			$html['sort_breakdown_time'] = date('M d, Y', $breakdown_row['sort_breakdown_from']);
		} elseif ($breakdown == 'month') {
			$html['sort_breakdown_time'] = date('M Y', $breakdown_row['sort_breakdown_from']);
		} elseif ($breakdown == 'year') {
			$html['sort_breakdown_time'] = date('Y', $breakdown_row['sort_breakdown_from']);
		}

		//echo "breakdown = $breakdown";
		DU::dump($breakdown_row['sort_breakdown_from']);
		DU::dump($html);

		$html['sort_breakdown_clicks'] = htmlentities($breakdown_row['sort_breakdown_clicks'], ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_leads'] = htmlentities($breakdown_row['sort_breakdown_leads'], ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_su_ratio'] = htmlentities($breakdown_row['sort_breakdown_su_ratio'] . '%', ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_payout'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_payout']), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_epc'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_epc']), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_avg_cpc'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_avg_cpc'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_income'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_income']), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_cost'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_cost'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_net'] = htmlentities(dollar_format($breakdown_row['sort_breakdown_net'], $cpv), ENT_QUOTES, 'UTF-8');
		$html['sort_breakdown_roi'] = htmlentities($breakdown_row['sort_breakdown_roi'] . '%', ENT_QUOTES, 'UTF-8'); ?>

		<tr>
			<td class="m-row2 m-row2-fade"><? echo $html['sort_breakdown_time']; ?></td>
			<td class="m-row1"><? echo $html['sort_breakdown_clicks']; ?></td>
			<td class="m-row1"><? echo $html['sort_breakdown_leads']; ?></td>
			<td class="m-row1"><? echo $html['sort_breakdown_su_ratio']; ?></td>
			<td class="m-row1"><? echo $html['sort_breakdown_payout']; ?></td>
			<td class="m-row3"><? echo $html['sort_breakdown_epc']; ?></td>
			<td class="m-row3"><? echo $html['sort_breakdown_avg_cpc']; ?></td>
			<td class="m-row4 "><? echo $html['sort_breakdown_income']; ?></td>
			<td class="m-row4 ">(<? echo $html['sort_breakdown_cost']; ?>)</td>
			<td class="<? if ($breakdown_row['sort_breakdown_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($breakdown_row['sort_breakdown_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_breakdown_net']; ?></td>
			<td class="<? if ($breakdown_row['sort_breakdown_net'] > 0) {
				echo 'm-row_pos';
			} elseif ($breakdown_row['sort_breakdown_net'] < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['sort_breakdown_roi']; ?></td>
		</tr>
		<? } error_reporting(0); ?>

	<?  $rows = count($breakdown_result);
	$html['clicks'] = htmlentities($stats_total['clicks'], ENT_QUOTES, 'UTF-8');
	$html['leads'] = htmlentities($stats_total['leads'], ENT_QUOTES, 'UTF-8');
	$html['su_ratio'] = htmlentities(round($stats_total['leads'] / $stats_total['clicks'] * 100, 2) . '%', ENT_QUOTES, 'UTF-8');
	$html['payout'] = htmlentities(dollar_format(($stats_total['payout'] / $rows)), ENT_QUOTES, 'UTF-8');
	$html['epc'] = htmlentities(dollar_format(($stats_total['income'] / $stats_total['clicks'])), ENT_QUOTES, 'UTF-8');
	$html['cpc'] = htmlentities(dollar_format(($stats_total['cost'] / $stats_total['clicks']), $cpv), ENT_QUOTES, 'UTF-8');
	$html['income'] = htmlentities(dollar_format(($stats_total['income']), $cpv), ENT_QUOTES, 'UTF-8');
	$html['cost'] = htmlentities(dollar_format(($stats_total['cost']), $cpv), ENT_QUOTES, 'UTF-8');
	$html['net'] = htmlentities(dollar_format(($stats_total['income'] - $stats_total['cost']), $cpv), ENT_QUOTES, 'UTF-8');
	$html['roi'] = htmlentities(round((($stats_total['income'] - $stats_total['cost']) / $stats_total['cost'] * 100), 2) . '%', ENT_QUOTES, 'UTF-8');

	error_reporting(6135); ?>

	<tr>
		<td class="m-row2 m-row-bottom"><strong>Totals for report</strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['clicks']; ?></strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['leads']; ?></strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['su_ratio']; ?></strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['payout']; ?></strong></td>
		<td class="m-row3 m-row-bottom"><strong><? echo $html['epc']; ?></strong></td>
		<td class="m-row3 m-row-bottom"><strong><? echo $html['cpc']; ?></strong></td>
		<td class="m-row4 m-row-bottom "><strong><? echo $html['income']; ?></strong></td>
		<td class="m-row4 m-row-bottom "><strong>(<? echo $html['cost']; ?>)</strong></td>
		<td class=" m-row-bottom <? if ($stats_total['net'] > 0) {
			echo 'm-row_pos';
		} elseif ($stats_total['net'] < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><strong><? echo $html['net']; ?></strong></td>
		<td class=" m-row-bottom <? if ($stats_total['net'] > 0) {
			echo 'm-row_pos';
		} elseif ($stats_total['net'] < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><strong><? echo $html['roi']; ?></strong></td>
	</tr>
</table>

