<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

//run script   
$_values['landing_page_id_public'] = (int)$_GET['lpip'];


$landing_page_id_public = $_values['landing_page_id_public'];
$tracker_row = LandingPages_DAO::find_aff_campaign_by_id_public($landing_page_id_public);




if (!$tracker_row) {
	die();
}
//DONT ESCAPE THE DESITNATIONL URL IT TOTALLY SCREWS UP
$html['aff_campaign_name'] = htmlentities($tracker_row['aff_campaign_name'], ENT_QUOTES, 'UTF-8');

//modify the redirect site url to go through another cloaked link
$redirect_site_url = rotateTrackerUrl($tracker_row);
$redirect_site_url = replaceTrackerPlaceholders($redirect_site_url, $click_id);
?>

<html>
<head>
	<title><? echo $html['aff_campaign_name']; ?></title>
	<meta name="robots" content="noindex">
	<meta http-equiv="refresh" content="1; url=<? echo $redirect_site_url; ?>">
</head>
<body>

<form name="form1" id="form1" method="get" action="/tracking202/redirect/cl2.php">
	<input type="hidden" name="q" value="<? echo $redirect_site_url; ?>"/>
</form>
<script type="text/javascript">
	document.form1.submit();
</script>


<div style="padding: 30px; text-align: center;">
	You are being automatically redirected.<br/><br/>
	Page Stuck? <a href="<? echo $redirect_site_url; ?>">Click Here</a>.
</div>
</body>
</html> 