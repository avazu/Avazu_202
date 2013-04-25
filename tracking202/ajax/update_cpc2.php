<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();


AUTH::set_timezone($_SESSION['user_timezone']);

//check variables

$from = explode('/', $_POST['from']);
$from_month = $from[0];
$from_day = $from[1];
$from_year = $from[2];

$to = explode('/', $_POST['to']);
$to_month = $to[0];
$to_day = $to[1];
$to_year = $to[2];

//if from or to, validate, and if validated, set it accordingly

if ((!$_POST['from']) and (!$_POST['to'])) {
	$error['time'] = '<div class="error">Please enter in the dates from and to like this <strong>mm/dd/yyyy</strong></div>';
}
$clean['from'] = mktime(0, 0, 0, $from_month, $from_day, $from_year);
$html['from'] = date('m/d/y g:ia', $clean['from']);

$clean['to'] = mktime(23, 59, 59, $to_month, $to_day, $to_year);
$html['to'] = date('m/d/y g:ia', $clean['to']);

$_values['from'] = $clean['from'];
$_values['to'] = $clean['to'];

//set mysql variables
$_values['user_id'] = (int)$_SESSION['user_id'];

//check affiliate network id, that you own
if ($_POST['aff_network_id']) {
	$_values['aff_network_id'] = (int)$_POST['aff_network_id'];

	$aff_network_id = $_values['aff_network_id'];
	$user_id = $_values['user_id'];
	$aff_network_row = AffNetworks_DAO::find_one_by_id_and_user_id($aff_network_id, $user_id);




	if (!$aff_network_row) {
		$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
	} else {
		$html['aff_network_name'] = htmlentities($aff_network_row['aff_network_name'], ENT_QUOTES, 'UTF-8');
	}
} else {
	$html['aff_network_name'] = 'ALL your affiliate networks';
}

//check aff_campaign id, that you own
if ($_POST['aff_campaign_id']) {
	$_values['aff_campaign_id'] = (int)$_POST['aff_campaign_id'];

	$aff_campaign_id = $_values['aff_campaign_id'];
	$user_id = $_values['user_id'];
	$aff_campaign_row = AffCampaigns_DAO::find_one_by_id_and_user_id($aff_campaign_id, $user_id);




	if (!$aff_campaign_row) {
		$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
	} else {
		$html['aff_campaign_name'] = htmlentities($aff_campaign_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');
	}
} else {
	$html['aff_campaign_name'] = 'ALL your affiliate campaigns in these affiliate networks';
}

//check text_ad id, that you own
if ($_POST['text_ad_id']) {
	$_values['text_ad_id'] = (int)$_POST['text_ad_id'];

	$text_ad_id = $_values['text_ad_id'];
	$user_id = $_values['user_id'];
	$text_ad_row = TextAds_DAO::find_one_by_id_and_user_id($text_ad_id, $user_id);




	if (!$text_ad_row) {
		$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
	} else {
		$html['text_ad_name'] = htmlentities($text_ad_row['text_ad_name'], ENT_QUOTES, 'UTF-8');
	}
} else {
	$html['text_ad_name'] = 'ALL your text ads in these affiliate campaigns';
}

//check method of promotion, that you own
if ($_POST['method_of_promotion']) {
	if ($_POST['method_of_promotion'] == 'landingpage') {
		$html['method_of_promotion'] = 'Landing pages';
		//ITS ON 5 NOT EQULA To 5, because 5 IS IN OUR DB, AS A SITE WITH NO URL!!!! AS THE SITE_URL_ID
		$_values['method_of_promotion'] = array('click_landing_site_url_id' => array('$ne' => 0)); //' AND click_landing_site_url_id!=\'0\' ';
	} else {
		$html['method_of_promotion'] = 'Direct links';
		$_values['method_of_promotion'] = array('click_landing_site_url_id' => 0); //' AND click_landing_site_url_id=\'0\' ';
	}
} else {
	$html['method_of_promotion'] = 'BOTH direct links and landing pages';
}

//check landing_page id, that you own
if (($_POST['method_of_promotion'] == 'landingpage') or ($_POST['tracker_type'] == 1)) {
	if ($_POST['landing_page_id']) {
		$_values['landing_page_id'] = (int)$_POST['landing_page_id'];

		$landing_page_id = $_values['landing_page_id'];
		$user_id = $_values['user_id'];
		$landing_page_row = LandingPages_DAO::find_one_by_id_and_user_id($landing_page_id, $user_id);




		if (!$landing_page_row) {
			$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
		} else {
			$html['landing_page_name'] = htmlentities($landing_page_row['landing_page_nickname'], ENT_QUOTES, 'UTF-8');
		}
	} else {
		$html['landing_page_name'] = 'ALL your landing pages in these affiliate campaigns';
	}
} else {
	$html['landing_page_name'] = 'n/a';
}

//check affiliate network id, that you own
if ($_POST['ppc_network_id']) {
	$_values['ppc_network_id'] = (int)$_POST['ppc_network_id'];

	$ppc_network_id = $_values['ppc_network_id'];
	$user_id = $_values['user_id'];
	$ppc_network_row = PpcNetworks_DAO::find_one_by_id_and_user_id($ppc_network_id, $user_id);




	if (!$ppc_network_row) {
		$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
	} else {
		$html['ppc_network_name'] = htmlentities($ppc_network_row['ppc_network_name'], ENT_QUOTES, 'UTF-8');
	}
} else {
	$html['ppc_network_name'] = 'ALL your PPC networks';
}

//check ppc_account id, that you own
if ($_POST['ppc_account_id']) {
	$_values['ppc_account_id'] = (int)$_POST['ppc_account_id'];

	$ppc_account_id = $_values['ppc_account_id'];
	$user_id = $_values['user_id'];
	$ppc_account_row = PpcAccounts_DAO::find_one_by_id_and_user_id($ppc_account_id, $user_id);




	if (!$ppc_account_row) {
		$error['user'] = '<div class="error">You can not modify other peoples cpc history.</div>';
	} else {
		$html['ppc_account_name'] = htmlentities($ppc_account_row['ppc_account_name'], ENT_QUOTES, 'UTF-8');
	}
} else {
	$html['ppc_account_name'] = 'ALL your PPC accounts in these PPC networks';
}

if ((!is_numeric($_POST['cpc_dollars'])) or (!is_numeric($_POST['cpc_cents']))) {
	$error['cpc'] = '<div class="error">You did not input a numeric max CPC.</div>';
} else {
	$click_cpc = $_POST['cpc_dollars'] . '.' . $_POST['cpc_cents'];
	$html['click_cpc'] = htmlentities(dollar_format($click_cpc), ENT_QUOTES, 'UTF-8');
	$_values['click_cpc'] = $click_cpc;
}


//echo error
echo $error['time'] . $error['user'];

//if there was an error terminate, or else just continue to run
if ($error) {
	die();
}

// update regular clicks

$click_cpc = $_values['click_cpc'];
$user_id = $_values['user_id'];
//TODO check the query is right
$result = ClicksAdvance_DAO::update_by_cpc_and_user_id($click_cpc, $user_id, $_values);



echo '<p style="text-align: center; font-weight: bold;">' . $result . ' clicks updated.</p>';