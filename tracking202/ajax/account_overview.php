<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

//set the timezone for this user.
AUTH::set_timezone($_SESSION['user_timezone']);

//start time
//chronometer();

//run the breakdown graph
runBreakdown(false);

//grab the users date range preferences
$time = grab_timeframe();
$_values['to'] = $time['to'];
$_values['from'] = $time['from'];


//show real or filtered clicks
$_values['user_id'] = (int)$_SESSION['user_id'];

$user_id = $_values['user_id'];
$user_row = UsersPref_DAO::find_one_by_user_id3($user_id);



$click_filtered = array(); //'';
if ($user_row['user_pref_show'] == 'all') {
}
if ($user_row['user_pref_show'] == 'real') {
	$click_filtered = array('click_filtered' => 0); //" AND click_filtered='0' ";
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
} ?>



<h3 class="green overview-spacer">Account Overview</h3>
<table cellpadding="0" cellspacing="1" class="m-stats">
<tr>
	<th colspan="2">Campaign / Advanced LP</th>
	<th>Clicks</th>
	<th>Leads</th>
	<th>S/U</th>
	<th>Payout</th>
	<th>EPC</th>
	<th>CPC</th>
	<th>Income</th>
	<th>Cost</th>
	<th>Net</th>
	<th>ROI</th>
</tr> <?

//TODO refactory aggre called out of the while loop
//grab the affiliate campaigns to display    

//ok, if x=1, show non ALP stuff, if x=2, show advanced landing page stuff

$from = $_values['from'];
$to = $_values['to'];

for ($x = 0; $x < 2; $x++) {

	$from = $_values['from'];
	$to = $_values['to'];

	if ($x == 0) {
		//select regular setup
		$info_result = SummaryOverview_DAO::find_aff_campaigns_by($from, $to, $user_id, 0);

	} else {

		$alp_counter = 1;

		//select advanced landing page setup
		$info_result = SummaryOverview_DAO::find_landing_pages_by($from, $to, $user_id);

	}

	while ($info_row = $info_result->getNext()) {

		DU::dump($info_row, __FILE__);
		//mysql escape the vars
		$_values['aff_campaign_id'] = $info_row['aff_campaign_id'];
		$_values['landing_page_id'] = $info_row['landing_page_id'];

		//grab the variables
		$key = "";
		if ($x == 0) {
			//      $click_sql = "
			//					$click_filtered
			//					AND 2c.aff_campaign_id='" . $_values['aff_campaign_id'] . "'
			//					AND 2c.click_alp=0";
			$user_pref_show = array('aff_campaign_id' => $_values['aff_campaign_id'],
			                        'click_alp' => 0);
			$key = 'aff_campaign_id';
		}
		else {
			//      $click_sql = "
			//					$click_filtered
			//					AND 2c.landing_page_id='" . $_values['landing_page_id'] . "'
			//					AND 2c.click_alp=1
			//			";

			$user_pref_show = array('landing_page_id' => $_values['landing_page_id'],
			                        'click_alp' => 1);
			$key = 'landing_page_id';
		}

		//echo "aggre_by_user_pref_show_and_others";
		//DU::dump($info_row);
		//DU::dump($user_pref_show);
		//DU::dump($click_filtered);
		$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others($key, $click_filtered, $user_pref_show, $_values);
		//echo "click row aggred= ";
		DU::dump($click_row);


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
		$payout = $info_row['aff_campaign_payout'];

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
		$roi = @@round($net / $cost * 100);

		$total_roi = @round($total_net / $total_cost * 100);

		//html escape vars
		$html['clicks'] = htmlentities($clicks, ENT_QUOTES, 'UTF-8');
		$html['leads'] = htmlentities($leads, ENT_QUOTES, 'UTF-8');
		$html['su_ratio'] = htmlentities($su_ratio . '%', ENT_QUOTES, 'UTF-8');
		$html['payout'] = htmlentities(dollar_format($payout), ENT_QUOTES, 'UTF-8');
		$html['epc'] = htmlentities(dollar_format($epc), ENT_QUOTES, 'UTF-8');
		$html['avg_cpc'] = htmlentities(dollar_format($avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
		$html['income'] = htmlentities(dollar_format($income), ENT_QUOTES, 'UTF-8');
		$html['cost'] = htmlentities(dollar_format($cost, $cpv), ENT_QUOTES, 'UTF-8');
		$html['net'] = htmlentities(dollar_format($net, $cpv), ENT_QUOTES, 'UTF-8');
		$html['roi'] = htmlentities($roi . '%', ENT_QUOTES, 'UTF-8');

		$html['aff_campaign_id'] = htmlentities($info_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
		//todo _id not right for other id
		$html['aff_campaign_name'] = htmlentities(NameCatcher::get('aff_campaign_name', $info_row['aff_campaign_id']) . $info_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
		$html['aff_campaign_payout'] = htmlentities($info_row['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');
		$html['aff_network_name'] = htmlentities($info_row['aff_network_name'], ENT_QUOTES, 'UTF-8');

		$html['landing_page_id'] = htmlentities($info_row['landing_page_id'], ENT_QUOTES, 'UTF-8');
		$html['landing_page_nickname'] = htmlentities($info_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');


		//shorten campaign name
		if (strlen($html['aff_campaign_name']) > 20) {
			$html['aff_campaign_name'] = substr($html['aff_campaign_name'], 0, 20) . '...';
		}

		if (strlen($html['landing_page_nickname']) > 20) {
			$html['landing_page_nickname'] = substr($html['landing_page_nickname'], 0, 20) . '...';
		}
		?>

		<? if ($alp_counter == 1) {
			$alp_counter++; /*?>
		<tr>
			<td colspan="12"><hr/></td>
		</tr>
	<?*/
		} ?>

	<tr>
		<? if ($x == 0) { ?>
		<td class="m-row2"><? echo $html['aff_network_name']; ?></td>
		<td class="m-row2 m-row2-fade"><? printf('<a href="#aff_%s" style="color: #000000;">%s</a>', $html['aff_campaign_id'], $html['aff_campaign_name']); ?></td>
		<? } else { ?>
		<td class="m-row2">Advanced LP</td>
		<td class="m-row2 m-row2-fade"><? printf('<a href="#lp_%s" style="color: #000000;">%s</a>', $html['landing_page_id'], $html['landing_page_nickname']); ?></td>
		<? } ?>
		<td class="m-row1"><? echo $html['clicks']; ?></td>
		<td class="m-row1"><? echo $html['leads']; ?></td>
		<td class="m-row1"><? echo  $html['su_ratio']; ?></td>
		<td class="m-row1"><? if ($x == 0) {
			echo $html['payout'];
		} ?></td>
		<td class="m-row3"><? echo $html['epc']; ?></td>
		<td class="m-row3"><? echo $html['avg_cpc']; ?></td>
		<td class="m-row4 "><? echo $html['income']; ?></td>
		<td class="m-row4 ">(<? echo $html['cost']; ?>)</td>
		<td class="<? if ($net > 0) {
			echo 'm-row_pos';
		} elseif ($net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><? echo $html['net']; ?></td>
		<td class="<? if ($net > 0) {
			echo 'm-row_pos';
		} elseif ($net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><? echo $html['roi']; ?></td>
	</tr>

		<? //OK NOW if this is an advanced landing page, u just showed the stats, but go through again and gata all the data now for the individual ones
		if ($x == 1) {

			$landing_page_id = $_values['landing_page_id'];
			$info_result2 = SummaryOverview_DAO::find_aff_campaigns_by($from, $to, $user_id, $landing_page_id);

			while ($info_row2 = $info_result2->getNext()) {

				//mysql escape the vars
				$_values['aff_campaign_id'] = $info_row2['aff_campaign_id'];

				//grab the variables

				//        $click_sql = "
				//					  $click_filtered
				//						AND aff_campaign_id='" . $_values['aff_campaign_id'] . "'
				//						AND landing_page_id='" . $_values['landing_page_id'] . "'
				//						AND 2c.click_alp=1
				//				";
				/*-- func #96 -> 93--*/

				$user_pref_show = array('landing_page_id' => $_values['landing_page_id'],
				                        'aff_campaign_id' => $_values['aff_campaign_id'],
				                        'click_alp' => 1);
				$key = 'aff_campaign_id';
				$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others($key, $click_filtered, $user_pref_show, $_values);


				//get the stats
				$clicks = 0;
				$clicks = $click_row['clicks'];

				//avg cpc and cost
				$avg_cpc = 0;
				$avg_cpc = $click_row['avg_cpc'];

				$cost = 0;
				$cost = $clicks * $avg_cpc;

				//leads
				$leads = 0;
				$leads = $click_row['leads'];

				//signup ratio
				$su_ratio - 0;
				$su_ratio = @round($leads / $clicks * 100, 2);

				//current payout
				$payout = 0;
				$payout = $info_row2['aff_campaign_payout'];

				//income
				$income = 0;
				$income = $click_row['income'];

				//grab the EPC
				$epc = 0;
				$epc = @round($income / $clicks, 2);

				//net income
				$net = 0;
				$net = $income - $cost;

				//roi
				$roi = 0;
				$roi = @@round($net / $cost * 100);

				//html escape vars
				$html['clicks'] = htmlentities($clicks, ENT_QUOTES, 'UTF-8');
				$html['leads'] = htmlentities($leads, ENT_QUOTES, 'UTF-8');
				$html['su_ratio'] = htmlentities($su_ratio . '%', ENT_QUOTES, 'UTF-8');
				$html['payout'] = htmlentities(dollar_format($payout), ENT_QUOTES, 'UTF-8');
				$html['epc'] = htmlentities(dollar_format($epc), ENT_QUOTES, 'UTF-8');
				$html['avg_cpc'] = htmlentities(dollar_format($avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
				$html['income'] = htmlentities(dollar_format($income), ENT_QUOTES, 'UTF-8');
				$html['cost'] = htmlentities(dollar_format($cost, $cpv), ENT_QUOTES, 'UTF-8');
				$html['net'] = htmlentities(dollar_format($net, $cpv), ENT_QUOTES, 'UTF-8');
				$html['roi'] = htmlentities($roi . '%', ENT_QUOTES, 'UTF-8');

				$html['aff_campaign_id'] = htmlentities($info_row2['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
				$html['aff_campaign_name'] = htmlentities($info_row2['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
				$html['aff_campaign_payout'] = htmlentities($info_row2['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');
				$html['aff_network_name'] = htmlentities($info_row2['aff_network_name'], ENT_QUOTES, 'UTF-8');

				$html['landing_page_id'] = htmlentities($info_row2['landing_page_id'], ENT_QUOTES, 'UTF-8');
				$html['landing_page_nickname'] = htmlentities($info_row2['landing_page_nickname'], ENT_QUOTES, 'UTF-8');


				//shorten campaign name
				if (strlen($html['aff_campaign_name']) > 20) {
					$html['aff_campaign_name'] = substr($html['aff_campaign_name'], 0, 20) . '...';
				}

				if (strlen($html['landing_page_nickname']) > 20) {
					$html['landing_page_nickname'] = substr($html['landing_page_nickname'], 0, 20) . '...';
				}

				?>
			<tr>
				<td class="m-row2" style="padding-left: 20px;"> - <? echo $html['aff_network_name']; ?></td>
				<td class="m-row2 m-row2-fade" style="padding-left: 20px;">
					- <? echo $html['aff_campaign_name']; ?></td>
				<td class="m-row1"><? echo $html['clicks']; ?></td>
				<td class="m-row1"><? echo $html['leads']; ?></td>
				<td class="m-row1"><? echo  $html['su_ratio']; ?></td>
				<td class="m-row1"><? {
					echo $html['payout'];
				} ?></td>
				<td class="m-row3"><? echo $html['epc']; ?></td>
				<td class="m-row3"></td>
				<td class="m-row4 "><? echo $html['income']; ?></td>
				<td class="m-row4 "></td>
				<td class="m-row_zero"></td>
				<td class="m-row_zero"></td>
			</tr><?

			}
		} //alp detail lines
	}
	//while info_row
} //for 0..1


$html['total_clicks'] = htmlentities($total_clicks, ENT_QUOTES, 'UTF-8');
$html['total_leads'] = htmlentities($total_leads, ENT_QUOTES, 'UTF-8');
$html['total_su_ratio'] = htmlentities($total_su_ratio . '%', ENT_QUOTES, 'UTF-8');
$html['total_epc'] = htmlentities(dollar_format($total_epc), ENT_QUOTES, 'UTF-8');
$html['total_avg_cpc'] = htmlentities(dollar_format($total_avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
$html['total_income'] = htmlentities(dollar_format($total_income), ENT_QUOTES, 'UTF-8');
$html['total_cost'] = htmlentities(dollar_format($total_cost, $cpv), ENT_QUOTES, 'UTF-8');
$html['total_net'] = htmlentities(dollar_format($total_net, $cpv), ENT_QUOTES, 'UTF-8');
$html['total_roi'] = htmlentities($total_roi . '%', ENT_QUOTES, 'UTF-8');  ?>

<tr>
	<td class="m-row2 m-row-bottom " colspan="2"><strong>Totals for report</strong></td>
	<td class="m-row1 m-row-bottom"><strong><? echo $html['total_clicks']; ?></strong></td>
	<td class="m-row1 m-row-bottom"><strong><? echo $html['total_leads']; ?></strong></td>
	<td class="m-row1 m-row-bottom"><strong><? echo $html['total_su_ratio']; ?></strong></td>
	<td class="m-row1 m-row-bottom"/>
	<td class="m-row3 m-row-bottom"><strong><? echo $html['total_epc']; ?></strong></td>
	<td class="m-row3 m-row-bottom"><strong><? echo $html['total_avg_cpc']; ?></strong></td>
	<td class="m-row4 m-row-bottom"><strong><? echo $html['total_income']; ?></strong></td>
	<td class="m-row4 m-row-bottom"><strong>(<? echo $html['total_cost']; ?>)</strong></td>
	<td class="<? if ($total_net > 0) {
		echo 'm-row_pos';
	} elseif ($total_net < 0) {
		echo 'm-row_neg';
	} else {
		echo 'm-row_zero';
	} ?> m-row-bottom"><strong><? echo $html['total_net']; ?></strong></td>
	<td class="<? if ($total_net > 0) {
		echo 'm-row_pos';
	} elseif ($total_net < 0) {
		echo 'm-row_neg';
	} else {
		echo 'm-row_zero';
	} ?> m-row-bottom"><strong><? echo $html['total_roi']; ?></strong></td>
</tr>
</table> <?











/*  BELOW IS ALMOST THE EXACT SAME CODE 
	AS THE ABOVE, BUT IT DOES IT PER EACH 
	AFFILIATE CAMPAIGN AND BREAKS IT DOWN 
	PER PPC ACCOUNT */


//ok, if x=1, show non ALP stuff, if x=2, show advanced landing page stuff
for ($x = 0; $x < 2; $x++) {

	$_values['user_id'] = (int)$_SESSION['user_id'];

	$user_id = $_values['user_id'];
	$from = $_values['from'];
	$to = $_values['to'];
	if ($x == 0) {

		//select regular setup
		$info_result = SummaryOverview_DAO::find_aff_campaigns_by($from, $to, $user_id, 0);


	} else {

		$alp_counter = 1;

		//select advanced landing page setup
		$info_result = SummaryOverview_DAO::find_landing_pages_by($from, $to, $user_id);

	}
	while ($info_row = $info_result->getNext()) {

		$total_clicks = 0;
		$total_leads = 0;
		$total_su_ratio = 0;
		$total_epc = 0;
		$total_avg_cpc = 0;
		$total_income = 0;
		$total_cost = 0;
		$total_net = 0;
		$total_roi = 0;

		//html escape variables
		$html['aff_campaign_id'] = htmlentities($info_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
		$html['aff_campaign_name'] = htmlentities($info_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');

		$html['landing_page_id'] = htmlentities($info_row['landing_page_id'], ENT_QUOTES, 'UTF-8');
		$html['landing_page_nickname'] = htmlentities($info_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');  ?>

	<? if ($x == 0) { ?><h3 class="green overview-spacer"
	                        id="aff_<? echo $html['aff_campaign_id']; ?>"><? echo $html['aff_campaign_name']; ?> <span
						style="font-size: 65%; color: grey; font-weight: normal;">[direct link &amp; simple lp]</span></h3>
		<? } else { ?><h3 class="green overview-spacer"
	                    id="lp_<? echo $html['landing_page_id']; ?>"><? echo $html['landing_page_nickname']; ?> <span
						style="font-size: 65%; color: grey; font-weight: normal;">[adv lp]</span></h3><? } ?>
	<table cellpadding="0" cellspacing="1" class="m-stats">
	<tr class="stats-grey">
		<th colspan="2">PPC Account</th>
		<th>Clicks</th>
		<th>Leads</th>
		<th>S/U</th>
		<th>Payout</th>
		<th>EPC</th>
		<th>Avg CPC</th>
		<th>Income</th>
		<th>Cost</th>
		<th>Net</th>
		<th>ROI</th>
	</tr> <?

		//ON THE FIRST RUN, GET THE TOTAL OF NO PPC ACCOUNTS, and then FOR THE INDIV PPC ACCOUNTS
		//mysql escape the vars
		$_values['aff_campaign_id'] = $info_row['aff_campaign_id'];
		$_values['landing_page_id'] = $info_row['landing_page_id'];


		//grab the variables
		if ($x == 0) {
			//      $click_sql = "
			//                   $click_filtered
			//                   AND aff_campaign_id='" . $_values['aff_campaign_id'] . "'
			//                   AND ppc_account_id='0'
			//                   AND 2c.click_alp=0
			//               ";
			//$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others3($user_pref_show, $_values);


			$user_pref_show = array('aff_campaign_id' => $_values['aff_campaign_id'],
			                        'ppc_account_id' => 0,
			                        'click_alp' => 0);
			$key = 'aff_campaign_id';


		} else {
			//      $click_sql = "
			//                   $click_filtered
			//                   AND landing_page_id='" . $_values['landing_page_id'] . "'
			//                   AND ppc_account_id='0'
			//                   AND 2c.click_alp=1
			//               ";
			//$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others4($user_pref_show, $_values);


			$user_pref_show = array('landing_page_id' => $_values['landing_page_id'],
			                        'ppc_account_id' => 0,
			                        'click_alp' => 1);
			$key = 'landing_page_id';
		}

		$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others($key, $click_filtered,
		                                                                   $user_pref_show, $_values);


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
		$payout = $info_row['aff_campaign_payout'];

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

		$total_roi = @round($total_net / $total_cost * 100);


		//html escape vars
		$html['clicks'] = htmlentities($clicks, ENT_QUOTES, 'UTF-8');
		$html['leads'] = htmlentities($leads, ENT_QUOTES, 'UTF-8');
		$html['su_ratio'] = htmlentities($su_ratio . '%', ENT_QUOTES, 'UTF-8');
		$html['payout'] = htmlentities(dollar_format($payout), ENT_QUOTES, 'UTF-8');
		$html['epc'] = htmlentities(dollar_format($epc), ENT_QUOTES, 'UTF-8');
		$html['avg_cpc'] = htmlentities(dollar_format($avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
		$html['income'] = htmlentities(dollar_format($income), ENT_QUOTES, 'UTF-8');
		$html['cost'] = htmlentities(dollar_format($cost, $cpv), ENT_QUOTES, 'UTF-8');
		$html['net'] = htmlentities(dollar_format($net, $cpv), ENT_QUOTES, 'UTF-8');
		$html['roi'] = htmlentities($roi . '%', ENT_QUOTES, 'UTF-8');

		$html['ppc_account_name'] = htmlentities($info_row2['ppc_account_name'], ENT_QUOTES, 'UTF-8');
		$html['aff_campaign_payout'] = htmlentities($info_row['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');

		$ppc_network_icon = pcc_network_icon($info_row2['ppc_network_name'], $info_row2['ppc_account_name']);

		//shorten campaign name
		if (strlen($html['ppc_account_name']) > 20) {
			$html['ppc_account_name'] = substr($html['ppc_account_name'], 0, 20) . '...';
		} ?>

		<? if ($clicks > 0) { ?>

	<tr>
		<td class="m-row2 m-row-small grey"><? echo $ppc_network_icon; ?></td>
		<td class="m-row2 m-row2-fade ">[no ppc referer]</td>
		<td class="m-row1"><? echo $html['clicks']; ?></td>
		<td class="m-row1"><? echo $html['leads']; ?></td>
		<td class="m-row1"><? echo  $html['su_ratio']; ?></td>
		<td class="m-row1"><? echo $html['payout']; ?></td>
		<td class="m-row3"><? echo $html['epc']; ?></td>
		<td class="m-row3"><? echo $html['avg_cpc']; ?></td>
		<td class="m-row4 "><? echo $html['income']; ?></td>
		<td class="m-row4 ">(<? echo $html['cost']; ?>)</td>
		<td class="<? if ($net > 0) {
			echo 'm-row_pos';
		} elseif ($net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><? echo $html['net']; ?></td>
		<td class="<? if ($net > 0) {
			echo 'm-row_pos';
		} elseif ($net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?>"><? echo $html['roi']; ?></td>
	</tr>

		<?
	}

		//mysql escape the variables
		$_values['aff_campaign_id'] = $info_row['aff_campaign_id'];
		//echo "info row =";
		DU::dump($info_row);

		$from = $_values['from'];
		$to = $_values['to'];
		//grab the ppc accounts to display
		if ($x == 0) {
			//normal campaign
			/*--call func #99--*/
			$aff_campaign_id = $_values['aff_campaign_id'];
			$info_result2 = SummaryOverview_DAO::find_nomal_ppc_accounts_by_aff_campaign_id_and_from_and_to($from, $to, $aff_campaign_id);

		} else {
			//advance landing page
			$landing_page_id = $info_row['landing_page_id'];
			/*--call func #100--*/
			$info_result2 = SummaryOverview_DAO::find_alp_ppc_accounts_by($from, $to, $user_id, $landing_page_id);
		}

		while ($info_row2 = $info_result2->getNext()) {
			//echo "info row2 =";
			DU::dump($info_row2);
			//mysql escape the vars
			//todo check above find before while can't give back know aff c id or lp id
			$_values['aff_campaign_id'] = $x == 0? $aff_campaign_id : $info_row2['aff_campaign_id'];
			$_values['landing_page_id'] = $x == 0? $info_row2['landing_page_id'] : $landing_page_id;
			$_values['ppc_account_id'] = $info_row2['ppc_account_id'];

			//grab the variables
			if ($x == 0) {

				//        $click_sql = "
				//                     $click_filtered
				//                     AND aff_campaign_id='" . $_values['aff_campaign_id'] . "'
				//                     AND ppc_account_id='" . $_values['ppc_account_id'] . "'
				//                     AND 2c.click_alp=0
				//                 ";
				//$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others5($user_pref_show, $_values);

				$user_pref_show = array('aff_campaign_id' => $_values['aff_campaign_id'],
				                        'ppc_account_id' => $_values['ppc_account_id'],
				                        'click_alp' => 0);
				$key = 'ppc_account_id';
			} else {
				//        $click_sql = "
				//                     $click_filtered
				//                     AND aff_campaign_id='" . $_values['aff_campaign_id'] . "'
				//                     AND ppc_account_id='" . $_values['ppc_account_id'] . "'
				//                     AND 2c.click_alp=1
				//                 ";
				//$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others6($user_pref_show, $_values);

				$user_pref_show = array('aff_campaign_id' => 0, //$_values['aff_campaign_id'],
				                        'ppc_account_id' => $_values['ppc_account_id'],
				                        'click_alp' => 1);
				$key = 'ppc_account_id';
			}

			$click_row = ClicksAdvance_DAO::aggre_by_user_pref_show_and_others($key, $click_filtered,
			                                                                   $user_pref_show, $_values);


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
			$total_avg_cpc = @round($total_cost / $total_clicks, 2);

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
			$payout = $info_row['aff_campaign_payout'];

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

			$total_roi = @round($total_net / $total_cost * 100);


			//html escape vars
			$html['clicks'] = htmlentities($clicks, ENT_QUOTES, 'UTF-8');
			$html['leads'] = htmlentities($leads, ENT_QUOTES, 'UTF-8');
			$html['su_ratio'] = htmlentities($su_ratio . '%', ENT_QUOTES, 'UTF-8');
			$html['payout'] = htmlentities(dollar_format($payout), ENT_QUOTES, 'UTF-8');
			$html['epc'] = htmlentities(dollar_format($epc), ENT_QUOTES, 'UTF-8');
			$html['avg_cpc'] = htmlentities(dollar_format($avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
			$html['income'] = htmlentities(dollar_format($income), ENT_QUOTES, 'UTF-8');
			$html['cost'] = htmlentities(dollar_format($cost, $cpv), ENT_QUOTES, 'UTF-8');
			$html['net'] = htmlentities(dollar_format($net, $cpv), ENT_QUOTES, 'UTF-8');
			$html['roi'] = htmlentities($roi . '%', ENT_QUOTES, 'UTF-8');

			$html['ppc_account_name'] = htmlentities($info_row2['ppc_account_name'], ENT_QUOTES, 'UTF-8');
			$html['aff_campaign_payout'] = htmlentities($info_row['aff_campaign_payout'], ENT_QUOTES, 'UTF-8');

			$ppc_network_icon = pcc_network_icon($info_row2['ppc_network_name'], $info_row2['ppc_account_name']);

			//shorten campaign name
			if (strlen($html['ppc_account_name']) > 20) {
				$html['ppc_account_name'] = substr($html['ppc_account_name'], 0, 20) . '...';
			} ?>

		<tr>
			<td class="m-row2 m-row-small grey"><? echo $ppc_network_icon; ?></td>
			<td class="m-row2 m-row2-fade "><? echo $html['ppc_account_name']; ?></td>
			<td class="m-row1"><? echo $html['clicks']; ?></td>
			<td class="m-row1"><? echo $html['leads']; ?></td>
			<td class="m-row1"><? echo  $html['su_ratio']; ?></td>
			<td class="m-row1"><? echo $html['payout']; ?></td>
			<td class="m-row3"><? echo $html['epc']; ?></td>
			<td class="m-row3"><? echo $html['avg_cpc']; ?></td>
			<td class="m-row4 "><? echo $html['income']; ?></td>
			<td class="m-row4 ">(<? echo $html['cost']; ?>)</td>
			<td class="<? if ($net > 0) {
				echo 'm-row_pos';
			} elseif ($net < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['net']; ?></td>
			<td class="<? if ($net > 0) {
				echo 'm-row_pos';
			} elseif ($net < 0) {
				echo 'm-row_neg';
			} else {
				echo 'm-row_zero';
			} ?>"><? echo $html['roi']; ?></td>
		</tr>

			<?
		}

		$html['total_clicks'] = htmlentities($total_clicks, ENT_QUOTES, 'UTF-8');
		$html['total_leads'] = htmlentities($total_leads, ENT_QUOTES, 'UTF-8');
		$html['total_su_ratio'] = htmlentities($total_su_ratio . '%', ENT_QUOTES, 'UTF-8');
		$html['total_epc'] = htmlentities(dollar_format($total_epc), ENT_QUOTES, 'UTF-8');
		$html['total_avg_cpc'] = htmlentities(dollar_format($total_avg_cpc, $cpv), ENT_QUOTES, 'UTF-8');
		$html['total_income'] = htmlentities(dollar_format($total_income), ENT_QUOTES, 'UTF-8');
		$html['total_cost'] = htmlentities(dollar_format($total_cost, $cpv), ENT_QUOTES, 'UTF-8');
		$html['total_net'] = htmlentities(dollar_format($total_net, $cpv), ENT_QUOTES, 'UTF-8');
		$html['total_roi'] = htmlentities($total_roi . '%', ENT_QUOTES, 'UTF-8');  ?>

	<tr>
		<td class="m-row2 m-row-bottom" colspan="2"><strong>Totals for report</strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['total_clicks']; ?></strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['total_leads']; ?></strong></td>
		<td class="m-row1 m-row-bottom"><strong><? echo $html['total_su_ratio']; ?></strong></td>
		<td class="m-row1 m-row-bottom"/>
		<td class="m-row3 m-row-bottom"><strong><? echo $html['total_epc']; ?></strong></td>
		<td class="m-row3 m-row-bottom"><strong><? echo $html['total_avg_cpc']; ?></strong></td>
		<td class="m-row4 m-row-bottom"><strong><? echo $html['total_income']; ?></strong></td>
		<td class="m-row4 m-row-bottom"><strong>(<? echo $html['total_cost']; ?>)</strong></td>
		<td class="<? if ($total_net > 0) {
			echo 'm-row_pos';
		} elseif ($total_net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?> m-row-bottom"><strong><? echo $html['total_net']; ?></strong></td>
		<td class="<? if ($total_net > 0) {
			echo 'm-row_pos';
		} elseif ($total_net < 0) {
			echo 'm-row_neg';
		} else {
			echo 'm-row_zero';
		} ?> m-row-bottom"><strong><? echo $html['total_roi']; ?></strong></td>
	</tr>
	</table>
	<?
	}

}?>

