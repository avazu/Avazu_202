<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

$_values['chart_id'] = (int)$_GET['chart_id'];

$chart_id = $_values['chart_id'];
$chart_row = Charts_DAO::get($chart_id);


echo $chart_row['chart_xml']; 
