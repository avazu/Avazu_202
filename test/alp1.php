<html>
<head>
<?php

  // -------------------------------------------------------------------
  //
  // Tracking202 PHP Redirection, created on Thu Mar, 2011
  //
  // This PHP code is to be used for the following setup:
  // aff n2 - campaign 1 on http://go.jj.com/alp1.php
  //
  // -------------------------------------------------------------------

  $tracking202outbound = 'http://go.jj.com/tracking202/redirect/off.php?acip=723&pci='.$_COOKIE['tracking202pci'];

  //header('location: '.$tracking202outbound);

?>
	<title></title>
</head>
<?php printf($_COOKIE['tracking202pci'])?>
<a href="http://go.jj.com/tracking202/redirect/off.php?acip=723&pci=<?php printf($_COOKIE['tracking202pci'])?>">test buy</a>

<script src="http://go.jj.com/tracking202/static/landing.php?lpip=624" type="text/javascript"></script>
<body>