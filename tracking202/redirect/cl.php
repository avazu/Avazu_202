<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//run script   
$_values['click_id_public'] = (int)$_GET['pci'];


$click_id_public = $_values['click_id_public'];
$tracker_row = ClicksAdvance_DAO::find_one_by_id_public($click_id_public);




if (!$tracker_row) {
	$action_site_url = "/202-404.php";
	$redirect_site_url = "/202-404.php";
} else {
	$action_site_url = "/tracking202/redirect/cl2.php";
	//modify the redirect site url to go through another cloaked link
	$redirect_site_url = $tracker_row['site_url_address'];
}

$html['aff_campaign_name'] = $tracker_row['aff_campaign_name'];
?>

<html>
<head>
	<title><? echo $html['aff_campaign_name']; ?></title>
	<meta name="robots" content="noindex">
	<meta http-equiv="refresh" content="0; url=<? echo $redirect_site_url; ?>">
</head>
<body>

<form name="form1" id="form1" method="get" action="<?php echo $action_site_url; ?>">
	<input type="hidden" name="q" value="<? echo $redirect_site_url; ?>"/>
</form>
<script type="text/javascript">
	document.form1.submit();
</script>


<div style="padding: 30px; text-align: center;">
	You are being automatically redirected to <? echo $html['aff_campaign_name']; ?>.<br/><br/>
	Page Stuck? <a href="<? echo $redirect_site_url; ?>">Click Here</a>.
</div>
</body>
</html> 