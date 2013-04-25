<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//make sure user is logged in or die
AUTH::require_user();

//start displaying the data     
header("Content-type: application/octet-stream");

# replace excelfile.xls with whatever you want the filename to default to
header("Content-Disposition: attachment; filename=T202_visitors_" . time() . ".xls");
header("Pragma: no-cache");
header("Expires: 0");


//get stuff

$pref_time = true;
$pref_adv = true;
$pref_show = true;
//$raw_order = 'click_id DESC';
$click_result = ClicksAdvance_DAO::get_history_clicks($pref_time, $pref_adv, $pref_show);


//获取结果分页数据
$count = $click_result->count(true); //todo fix how about $count=false?
$pref_limit = true;
$offset = (string)$_POST['offset'];
$query = ClicksAdvance_DAO::get_query_limit_and_pages(false, false, $count);
$click_result = $click_result->skip($query['skip'])->limit($query['limit']);


//html escape vars
$html['from'] = htmlentities($query['from'], ENT_QUOTES, 'UTF-8');
$html['to'] = htmlentities($query['to'], ENT_QUOTES, 'UTF-8');
$html['rows'] = htmlentities($query['rows'], ENT_QUOTES, 'UTF-8');

//set the timezone for the user, to display dates in their timezone
AUTH::set_timezone($_SESSION['user_timezone']);

echo   "Subid" . "\t" .
       "Date" . "\t" .
       "Browser" . "\t" .
       "OS" . "\t" .
       "PPC Network" . "\t" .
       "PPC account" . "\t" .
       "Click Real/Filtered" . "\t" .
       "IP Address" . "\t" .
       "Offer/LP" . "\t" .
       "Text Ad" . "\t" .
       "Referer" . "\t" .
       "Landing" . "\t" .
       "Outbound" . "\t" .
       "Cloaked Referer" . "\t" .
       "Redirect" . "\t" .
       "Keyword" . "\n";

//now display all the clicks
while ($click_row = $click_result->getNext()) {

	$_values['click_id'] = $click_row['click_id'];

	$click_id = $_values['click_id'];
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
	}

	if ($click_row['click_filtered'] == '1') {
		$click_filtered = 'filtered';
	} elseif ($click_row['click_lead'] == '1') {
		$click_filtered = 'conversion';
	} else {
		$click_filtered = 'real';
	}

	echo   $click_row['click_id'] . "\t" .
	       date('m/d/y g:ia', $click_row['click_time']) . "\t" .
	       $click_row['browser_name'] . "\t" .
	       $click_row['platform_name'] . "\t" .
	       $click_row['ppc_network_name'] . "\t" .
	       $click_row['ppc_account_name'] . "\t" .
	       $click_filtered . "\t" .
	       $click_row['ip_address'] . "\t" .
	       $click_row['aff_campaign_name'] . "\t" .
	       $click_row['text_ad_name'] . "\t" .
	       $html['referer'] . "\t" .
	       $html['landing'] . "\t" .
	       $html['outbound'] . "\t" .
	       $html['cloaking'] . "\t" .
	       $html['redirect'] . "\t" .
	       $click_row['keyword'] . "\n";

}
