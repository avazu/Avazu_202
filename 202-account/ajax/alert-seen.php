<?php


include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

$_values['prosper_alert_id'] = (int)$_POST['prosper_alert_id'];


$prosper_alert_id = $_values['prosper_alert_id'];
$alert_sql = Alerts_DAO::create_by_prosper_id($prosper_alert_id);





