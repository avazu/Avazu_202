<html>
<head>
	<?php

// -------------------------------------------------------------------
//
// Tracking202 PHP Redirection, created on Wed Mar, 2011
//
// This PHP code is to be used for the following landing page.
// http://go.jj.com/affn1c1-lp1.php
//
// -------------------------------------------------------------------

	if (isset($_COOKIE['tracking202outbound'])) {
		$tracking202outbound = $_COOKIE['tracking202outbound'];
	} else {
		$tracking202outbound = 'http://go.jj.com/tracking202/redirect/lp.php?lpip=311&pci=' . $_COOKIE['tracking202pci'];
	}

//header('location: ' . $tracking202outbound);
	?>
	<title></title>
</head>
<?php printf($_COOKIE['tracking202pci'])?>
<a href="http://go.jj.com/tracking202/redirect/lp.php?lpip=311&pci=<?php printf($_COOKIE['tracking202pci'])?>">test buy</a>

<script src="http://go.jj.com/tracking202/static/landing.php?lpip=311" type="text/javascript"></script>
<body>