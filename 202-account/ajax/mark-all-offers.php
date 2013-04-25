<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

AUTH::require_user();

//get the new offer count

$json = getUrl(TRACKING202_RSS_URL . '/cleervoyance/offers?type=json');
$json = json_decode($json, true);

//this grabs all of the current offers avaliable
$offers = $json['offers'];

if ($offers) {
	foreach ($offers as $offer) {

		//now check to see if they are recent or not
		$_values['user_id'] = (int)$_SESSION['user_id'];
		$_values['offer_id'] = $offer['id'];


		$user_id = $_values['user_id'];
		$offer_id = $_values['offer_id'];
		$row = Offers_DAO::find_one_by_id_and_user_id($offer_id, $user_id);

		if (!$row) {
			//mark it as seen now


			$user_id = $_values['user_id'];
			$offer_id = $_values['offer_id'];
			$result = Offers_DAO::create_by_id_and_user_id($offer_id, $user_id);




		}
	}
}

//mark all offers as seen
$_SESSION['new_offers'] = 0;
die();