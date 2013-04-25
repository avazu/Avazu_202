<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


//if spy is enabled, run the query in a certain way.
if ($_GET['spy'] == 1) {
	//	//$from = time() - 5;
	//	$command = "-SELECT * FROM 202_clicks_spy AS 2c LEFT JOIN 202_clicks_advance AS 2ca USING (click_id) LEFT JOIN 202_clicks_site AS 2cs USING (click_id)";
	//	$db_table = "2c";
	//	$query = query($command, $db_table, false, true, true, ' ORDER BY 2c.click_id DESC ', false, 30, true);
} else {
	//	$command = "-SELECT * FROM 202_clicks AS 2c LEFT JOIN 202_clicks_advance AS 2ca USING (click_id) LEFT JOIN 202_clicks_site AS 2cs USING (click_id)";
	//	$db_table = "2c";
	//	$query = query($command, $db_table, true, true, true, '  ORDER BY 2c.click_id DESC ', $_POST['offset'], true, true);
}
//run query

$pref_time = true;
$pref_adv = true;
$pref_show = true;
$click_result = ClicksAdvance_DAO::get_history_clicks($pref_time, $pref_adv, $pref_show);


//获取结果分页数据
$count = $click_result->count(true);
$pref_limit = true;
$offset = (string)$_POST['offset'];
$query = ClicksAdvance_DAO::get_query_limit_and_pages($offset, true, $count);
DU::dump($query);
//DU::dump($click_result->count(true));
$click_result = $click_result->skip($query['skip'])->limit($query['limit']);
//DU::dump($click_result);
//DU::dump($click_result->count(true));
//html escape vars
$html['from'] = htmlentities($query['from'], ENT_QUOTES, 'UTF-8');
$html['to'] = htmlentities($query['to'], ENT_QUOTES, 'UTF-8');
$html['rows'] = htmlentities($query['rows'], ENT_QUOTES, 'UTF-8');

//if this click history, show the results
if ($_GET['spy'] != 1) {
	?>
<table cellspacing="0" cellpadding="0" style="width: 100%; font-size: 12px;">
	<tr>
		<td width="100%;">
			<a target="_new" href="/tracking202/visitors/download/">
				<strong>Download to excel</strong>
				<img src="/202-img/icons/16x16/page_white_excel.png" style="margin: 0px 0px -3px 3px;"/>

			</a>
		</td>
		<td>
			<? printf('<div class="results">Results <b>%s - %s</b> of <b>%s</b></div>', $html['from'], $html['to'], $html['rows']);  ?>
		</td>
	</tr>
</table>
<?
}

//set the timezone for the user, to display dates in their timezone
AUTH::set_timezone($_SESSION['user_timezone']);

//start displaying the data     
?>
<div class="data"><?


?>

<table cellpadding="3" cellspacing="0" class="item-table">
	<tr class="<? echo $html['row_class']; ?>" style="font-weight: bold; text-align: left;">
		<td style="width: 53px;">Subid</td>
		<td class="date">Date</td>
		<td class="ppc"></td>
		<td class="ppc"></td>
		<? if (geoLocationDatabaseInstalled() == true) {
		echo '<td class="flag"></td>';
	} ?>
		<td class="ppc"></td>
		<td class="filter"></td>
		<td class="ip">IP</td>
		<td class="aff">Offer / LP</td>
		<td class="referer_big">Referer</td>
		<td class="ad">Text Ad</td>
		<td class="referer"></td>
		<td class="landing"></td>
		<td class="outbound"></td>
		<td class="cloaking"></td>
		<td class="redirect"></td>
		<td class="keyword">
			<div>Keyword</div>
		</td>
	</tr>
</table>

<?


//If this is spy view, the last clicks in the last 5 seconds go into a hidden div, then it is made visible with a scriptialouc affect, so this div contains the clicks iwthin the last 5 seconds
if ($_GET['spy'] == 1) {
	$new = true;
	?><div id="m-newclicks" style="display: none;"><?
}

//if there is no clicks to display let them know :(
if ($click_result->count(true) == 0) {
	?>
	<div style="text-align: center; font-size: 14px; border-bottom: 1px rgb(234,234,234) solid; padding: 10px;">You have
		no data to display with your above filters currently.
	</div><?
	if ($_GET['spy'] == 1) {
		?>
		<div style="text-align: center; font-size: 14px; border-bottom: 1px rgb(234,234,234) solid; padding: 10px;">The
			spy view only shows clicks activity within the past 24 hours.
		</div><?
	}
}

//now display all the clicks
while ($click_row = $click_result->getNext()) {

	$_values['click_id'] = $click_row['click_id'];

	//  if ($_GET['spy'] == 1) {
	//    $clicks_tbl = "202_clicks_spy";
	//  } else {
	//    $clicks_tbl = "202_clicks";
	//  }

	$click_id = $_values['click_id'];
	//todo fix location is not considered,
	//  so no if (geoLocationDatabaseInstalled() == true) {
	$click_row2 = ClicksAdvance_DAO::get_click_with_all_related_things($click_id);
	$click_row = array_merge($click_row, $click_row2);

	$_values['click_referer_site_url_id'] = $click_row['click_referer_site_url_id'];

	$click_referer_site_url_id = $_values['click_referer_site_url_id'];
	$site_url_row = SiteUrls_DAO::get($click_referer_site_url_id);
	$html['referer'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');


	$html['referer'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');
	$html['referer_host'] = htmlentities($site_url_row['site_domain_host'], ENT_QUOTES, 'UTF-8');

	$_values['click_landing_site_url_id'] = $click_row['click_landing_site_url_id'];

	$click_landing_site_url_id = $_values['click_landing_site_url_id'];
	$site_url_row = SiteUrls_DAO::get($click_landing_site_url_id);
	$html['landing'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');


	$html['landing'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');
	$html['landing_host'] = htmlentities($site_url_row['site_domain_host'], ENT_QUOTES, 'UTF-8');

	$_values['click_outbound_site_url_id'] = $click_row['click_outbound_site_url_id'];

	$click_outbound_site_url_id = $_values['click_outbound_site_url_id'];
	$site_url_row = SiteUrls_DAO::get($click_outbound_site_url_id);
	$html['outbound'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');


	$html['outbound'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');
	$html['outbound_host'] = htmlentities($site_url_row['site_domain_host'], ENT_QUOTES, 'UTF-8');

	//this is alittle different
	if ($click_row['click_cloaking']) {

		//if not a landing page
		if (!$click_row['click_alp']) {
			$html['cloaking'] = htmlentities('http://' . $_SERVER['SERVER_NAME'] . '/tracking202/redirect/cl.php?pci=' . $click_row['click_id_public']);
			$html['cloaking_host'] = htmlentities($_SERVER['SERVER_NAME']);
		} else {
			//advanced lander
			$html['cloaking'] = htmlentities('http://' . $_SERVER['SERVER_NAME'] . '/tracking202/redirect/off.php?acip=' . $click_row['aff_campaign_id_public'] . '&pci=' . $click_row['click_id_public']);
			$html['cloaking_host'] = htmlentities($_SERVER['SERVER_NAME']);
		}
	} else {
		$html['cloaking'] = '';
		$html['cloaking_host'] = '';
	}

	$_values['click_redirect_site_url_id'] = $click_row['click_redirect_site_url_id'];

	$click_redirect_site_url_id = $_values['click_redirect_site_url_id'];
	$site_url_row = SiteUrls_DAO::get($click_redirect_site_url_id);



	$html['redirect'] = htmlentities($site_url_row['site_url_address'], ENT_QUOTES, 'UTF-8');
	$html['redirect_host'] = htmlentities($site_url_row['site_domain_host'], ENT_QUOTES, 'UTF-8');


	$html['click_id'] = htmlentities($click_row['click_id'], ENT_QUOTES, 'UTF-8');
	$html['click_time'] = date('m/d/y g:ia', $click_row['click_time']);
	$html['aff_campaign_id'] = htmlentities($click_row['aff_campaign_id'], ENT_QUOTES, 'UTF-8');
	$html['landing_page_nickname'] = htmlentities($click_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');
	$html['ppc_account_id'] = htmlentities($click_row['ppc_account_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_id'] = htmlentities($click_row['text_ad_id'], ENT_QUOTES, 'UTF-8');
	$html['text_ad_name'] = htmlentities($click_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
	$html['aff_campaign_name'] = htmlentities($click_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
	$html['aff_network_name'] = htmlentities($click_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
	$html['ppc_network_name'] = htmlentities($click_row['ppc_network_name'], ENT_QUOTES, 'UTF-8');
	$html['ppc_account_name'] = htmlentities($click_row['ppc_account_name'], ENT_QUOTES, 'UTF-8');
	$html['ip_address'] = htmlentities($click_row['ip_address'], ENT_QUOTES, 'UTF-8');
	$html['click_cpc'] = htmlentities(dollar_format($click_row['click_cpc']), ENT_QUOTES, 'UTF-8');
	$html['keyword'] = htmlentities($click_row['keyword'], ENT_QUOTES, 'UTF-8');
	$html['click_lead'] = htmlentities($click_row['click_lead'], ENT_QUOTES, 'UTF-8');
	$html['click_filtered'] = htmlentities($click_row['click_filtered'], ENT_QUOTES, 'UTF-8');

	//rotate colors
	$html['row_class'] = 'item';
	if ($x == 0) {
		$html['row_class'] = 'item alt';
		$x = 1;
	} else {
		$x--;
	}

	$ppc_network_icon = pcc_network_icon($click_row['ppc_network_name'], $click_row['ppc_account_name']);

	if (geoLocationDatabaseInstalled() == true) {
		$html['location'] = '';
		if ($click_row['location_country_name']) {
			if ($click_row['location_country_name']) {
				$origin = $click_row['location_country_name'];
			}
			if (($click_row['location_region_code']) and (!is_numeric($click_row['location_region_code']))) {
				$origin = $click_row['location_region_code'] . ', ' . $origin;
			}
			if ($click_row['location_city_name']) {
				$origin = $click_row['location_city_name'] . ', ' . $origin;
			}

			$html['origin'] = htmlentities($origin, ENT_QUOTES, 'UTF-8');
			$html['location'] = '<img title="' . $html['origin'] . '" src="http://' . $_SERVER['SERVER_NAME'] . '/202-img/flags/' . strtolower($click_row['location_country_code']) . '.png"/>';
		}
	}

	$html['browser_id'] = '';
	if ($click_row['browser_id']) {
		$html['browser_id'] = '<img title="' . $click_row['browser_name'] . '" src="/202-img/icons/browsers/' . $click_row['browser_id'] . '.png"/>';
	}

	$html['platform_id'] = '';
	if ($click_row['platform_id']) {
		$html['platform_id'] = '<img title="' . $click_row['platform_name'] . '" src="/202-img/icons/platforms/' . $click_row['platform_id'] . '.gif"/>';

	}

	//if this is an advance landing page, make the offer name, the landing page name
	if ($click_row['click_alp'] == 1) {
		$html['aff_campaign_name'] = $html['landing_page_nickname'];
	}


	//before it ends, if this click is past 5 seconds, set true to $endofnewclicks
	$diff = time() - $click_row['click_time'];
	if (($diff > 5) and ($new == true)) {
		$new = false; ?>
			 </div>        
		<? } ?>

<table cellpadding="0" cellspacing="0" class="item-table">
	<tr class="<? echo $html['row_class']; ?>">
		<td style="width: 53px;"
		    id="<? echo $html['click_id']; ?>"><? printf('<a href="#%s">%s</a>', $html['click_id'], $html['click_id']); ?></td>
		<td class="date"><? echo $html['click_time']; ?></td>
		<td class="ppc"><? echo $html['browser_id']; ?></td>
		<td class="ppc"><? echo $html['platform_id']; ?></td>
		<? if (geoLocationDatabaseInstalled() == true) {
		echo '<td class="flag">' . $html['location'] . '</td>';
	} ?>
		<td class="ppc"><? echo $ppc_network_icon; ?></td>
		<td class="filter">
			<? if ($click_row['click_filtered'] == '1') { ?>
			<img style="margin-right: auto;" src="/202-img/icons/16x16/delete.png" alt="Filtered Out Click"
			     title="filtered out click"/>
			<? } elseif ($click_row['click_lead'] == '1') { ?>
			<img style="margin-right: auto;" src="/202-img/icons/16x16/money_dollar.png" alt="Converted Click"
			     title="converted click"/>
			<? } else { ?>
			<img style="margin-right: auto;" src="/202-img/icons/16x16/add.png" alt="Real Click" title="real click"/>
			<? } ?>
		</td>
		<td class="ip"><? echo $html['ip_address']; ?></td>
		<td class="aff"><? echo $html['aff_campaign_name']; ?></td>
		<td class="referer_big"><? printf('<a href="%s" target="_new" title="Referer">%s</a>', $html['referer'], $html['referer_host']); ?></td>
		<td class="ad"><? echo $html['text_ad_name']; ?></td>
		<td class="referer"><? if ($html['referer'] != '') {
			printf('<a href="%s" target="_new"><img src="/202-img/icons/16x16/control_end_blue.png" alt="Referer" title="Referer: %s"/></a>', $html['referer'], $html['referer']);
		} ?></td>
		<td class="landing"><? if ($html['landing'] != '') {
			printf('<a href="%s" target="_new"><img src="/202-img/icons/16x16/control_pause_blue.png" alt="Landing"  title="Landing Page: %s"/></a>', $html['landing'], $html['landing']);
		} ?></td>
		<td class="outbound"><? if (($html['outbound'] != '') and ($click_row['click_out'] == 1)) {
			printf('<a href="%s" target="_new"><img src="/202-img/icons/16x16/control_play_blue.png" alt="Outbound" title="Outbound: %s"/></a>', $html['outbound'], $html['outbound']);
		} ?></td>
		<td class="cloaking"><? if (($html['cloaking'] != '') and ($click_row['click_out'] == 1)) {
			printf('<a href="%s" target="_new"><img src="/202-img/icons/16x16/control_equalizer_blue.png" alt="Cloaking" title="Cloaked Referer: %s"/></a>', $html['cloaking'], $html['cloaking']);
		} ?></td>
		<td class="redirect"><? if (($html['redirect'] != '') and ($click_row['click_out'] == 1)) {
			printf('<a href="%s" target="_new"><img src="/202-img/icons/16x16/control_fastforward_blue.png" alt="Redirection" title="Redirect: %s"/></a>', $html['redirect'], $html['redirect']);
		} ?></td>
		<td class="keyword">
			<div><span title="<? echo $html['keyword']; ?>"><? echo $html['keyword']; ?></span></div>
		</td>
	</tr>
</table>
	<? } ?>
</div>

<? if (($query['pages'] > 2) and ($_GET['spy'] != 1)) { ?>
<div class="offset">   <?
	if ($query['offset'] > 0) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/click_history.php\',\'%s\');">First</a> ', $i);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/click_history.php\',\'%s\');">Prev</a> ', $query['offset'] - 1);
	}

	if ($query['pages'] > 1) {
		for ($i = 0; $i < $query['pages'] - 1; $i++) {
			if (($i >= $query['offset'] - 10) and ($i < $query['offset'] + 11)) {
				if ($query['offset'] == $i) {
					$class = 'class="link_selected"';
				} else {
					$class = 'class="onclick_color"';
				}
				printf(' <a %s onclick="loadContent(\'/tracking202/ajax/click_history.php\',\'%s\');">%s</a> ', $class, $i, $i + 1);
				unset($class);
			}
		}
	}

	if ($query['offset'] < $query['pages'] - 2) {
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/click_history.php\',\'%s\');"">Next</a> ', $query['offset'] + 1);
		printf(' <a class="onclick_color" onclick="loadContent(\'/tracking202/ajax/click_history.php\',\'%s\');">Last</a> ', $query['pages'] - 2);
	} ?>
</div>
<? }


