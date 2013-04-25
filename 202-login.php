<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$_values['user_name'] = (string)$_POST['user_name'];

	$user_pass = salt_user_pass($_POST['user_pass']);
	$_values['user_pass'] = $user_pass;

	//check to see if this user exists


	$user_name = $_values['user_name'];
	$user_pass = $_values['user_pass'];
	$user_row = Users_DAO::find_one_by_name_and_pass($user_name, $user_pass);


	if (!$user_row) {
		$error['user'] = '<div class="error">Your username or password is incorrect.</div>';
	}

	//check tokens	
	/* ($_POST['token'] != $_SESSION['token']) {
		$error['token'] = '<div class="error">You must use theses forms to submit data.</div'; 
	}*/


	//RECORD THIS USER LOGIN, into user_logs
	$_values['login_server'] = serialize($_SERVER);
	$_values['login_session'] = serialize($_SESSION);
	$_values['login_error'] = serialize($error);
	$_values['ip_address'] = $_SERVER['REMOTE_ADDR'];

	$_values['login_time'] = time();

	if ($error) {
		$_values['login_success'] = 0;
	} else {
		$_values['login_success'] = 1;
	}

	//record everything that happend during this crime scene.

	$user_log_result = UsersLog_DAO::create_by($_values);




	if (!$error) {

		$ip_id = INDEXES::get_ip_id($_SERVER['HTTP_X_FORWARDED_FOR']);
		$_values['ip_id'] = $ip_id;

		//update this users last login_ip_address

		$ip_id = $_values['ip_id'];
		$user_name = $_values['user_name'];
		$user_pass = $_values['user_pass'];
		$user_result = Users_DAO::update_by_name_and_pass_and_ip_id($user_name, $user_pass, $ip_id);




		//regenerate session_id to prevent fixation
		//session_regenerate_id();     have to remove this because it wouldn't like IE8 users login

		//set session variables			
		$_SESSION['session_fingerprint'] = md5('session_fingerprint' . $_SERVER['HTTP_USER_AGENT'] . session_id());
		$_SESSION['session_time'] = time();
		$_SESSION['user_name'] = $user_row['user_name'];
		$_SESSION['user_id'] = $user_row['user_id'];
		$_SESSION['user_api_key'] = $user_row['user_api_key'];
		$_SESSION['user_stats202_app_key'] = $user_row['user_stats202_app_key'];
		$_SESSION['user_timezone'] = $user_row['user_timezone'];

		//redirect to account screen
		//just redirect to the tracking 202 screen to passby others
		header('location: /tracking202');
	}

	$html['user_name'] = htmlentities($_POST['user_name'], ENT_QUOTES, 'UTF-8');

}


info_top(); ?>
<form method="post" action="">
	<input type="hidden" name="token" value="<? echo $_SESSION['token']; ?>"/>
	<table cellspacing="0" cellpadding="5" style="margin: 0px auto;">
		<? if ($error['token']) {
		printf('<tr><td colspan="2">%s</td></tr>', $error['token']);
	} ?>
		<tr>
			<td>Username:</td>
			<td><input id="user_name" type="text" name="user_name" value="<? echo $html['user_name']; ?>"/></td>
		</tr>
		<? if ($error['user']) {
		printf('<tr><td colspan="2">%s</td></tr>', $error['user']);
	} ?>
		<tr>
			<td>Password:</td>
			<td>
				<input id="user_pass" type="password" name="user_pass"/>
				<span id="forgot_pass">(<a href="/202-lost-pass.php">I forgot my password/username</a>)</span>
			</td>
		</tr>
		<tr>
			<td/>
			<td><input id="submit" type="submit" value="Sign In"/></td>
		</tr>
	</table>
</form>
<? info_bottom(); ?>