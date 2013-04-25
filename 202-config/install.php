<?php

//include mysql settings
include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');


//check to see if this is already installed, if so dob't do anything
if (is_installed() == true) {

	_die("<h2>Already Installed</h2>
			  You appear to have already installed Prosper202. To reinstall please clear your old database tables first.");

}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check email
	if (check_email_address($_POST['user_email']) == false) {
		$error['user_email'] = '<div class="error">Please enter a valid email address</div>';
	}

	//check username
	if ($_POST['user_name'] == '') {
		$error['user_name'] = '<div class="error">You must type in your desired username</div>';
	}
	if (!ctype_alnum($_POST['user_name'])) {
		$error['user_name'] .= '<div class="error">Your username may only contain alphanumeric characters</div>';
	}
	if ((strlen($_POST['user_name']) < 4) OR (strlen($_POST['user_name']) > 20)) {
		$error['user_name'] .= '<div class="error">Your username must be between 4 and 20 characters long</div>';
	}

	//check password
	if ($_POST['user_pass'] == '') {
		$error['user_pass'] = '<div class="error">You must type in your desired password</div>';
	}
	if ($_POST['user_pass'] == '') {
		$error['user_pass'] .= '<div class="error">You must type verify your password</div>';
	}
	if ((strlen($_POST['user_pass']) < 6) OR (strlen($_POST['user_pass']) > 35)) {
		$error['user_pass'] .= '<div class="error">Your passwords must be at least 6 characters long</div>';
	}
	if ($_POST['user_pass'] != $_POST['verify_user_pass']) {
		$error['user_pass'] .= '<div class="error">Your passwords did not match, please try again</div>';
	}

	//print_r_html($error); 
	//if no error occured, lets create the user account
	if (!$error) {

		//no error, so now setup all of the mysql database structures
		INSTALL::install_databases();

		$_values['user_email'] = (string)$_POST['user_email'];
		$_values['user_name'] = (string)$_POST['user_name'];
		$_values['user_timezone'] = (string)$_POST['user_timezone'];
		$_values['user_time_register'] = time();

		//md5 the user pass with salt
		$user_pass = salt_user_pass($_POST['user_pass']);
		$_values['user_pass'] = $user_pass;

		//insert this user

		$user_result = Users_DAO::create_by($_values);
		//print_r($user_result);
		$user_id = $user_result['user_id'];
		//die($user_id);



		$_values['user_id'] = $user_id;

		//update user preference table   

		$user_id = $_values['user_id'];
		$user_result = UsersPref_DAO::create_by_user_id($user_id);



		//if this worked, show them the succes screen
		$success = true;

	}


	$html['user_email'] = htmlentities($_POST['user_email'], ENT_QUOTES, 'UTF-8');
	$html['user_name'] = htmlentities($_POST['user_name'], ENT_QUOTES, 'UTF-8');
	$html['user_pass'] = htmlentities($_POST['user_pass'], ENT_QUOTES, 'UTF-8');
}


//only show install setup, if it, of course, isn't install already.

if ($success != true) {

	info_top(); ?>

<h2>Welcome</h2>
<p>Welcome to the five minute Prosper202 installation process! You may want to browse the <a
				href="http://prosper202.com/apps/docs/">ReadMe documentation</a> at your leisure. Otherwise, just fill in the
	information below and you'll be on your way to using the most powerful internet marketing applications in the world.
</p>

<h2>System Configuration</h2>
<table cellspacing="0" cellpadding="5">
	<tr>
		<th style="text-align: right;">PHP Version:</th>
		<td><? echo phpversion(); ?></td>
	</tr>
	<tr>
		<th style="text-align: right;">MongoDB Version:</th>
		<td>
			<? $mongodb_version = db_version();
			$html['mongodb_version'] = htmlentities($mongodb_version, ENT_QUOTES, 'UTF-8');
			echo $html['mongodb_version']; ?>
		</td>
	</tr>
</table>

<?

	$phpversion = substr(phpversion(), 0, 1);
	if ($phpversion < 5) {
		$error['phpversion'] = '<br/><div class="error">Prosper202 requires PHP 4, or newer.</div>';
		echo $error['phpversion'];
		info_bottom();
		die();
	}

	$mongodb_version = substr($mongodb_version, 0, 3);
	if ($mongodb_version != "1.8") {
		$error['mongodb_version'] = '<br/><div class="error">Prosper202 requires MongoDB 1.8, or newer.</div>';
		echo $error['mongodb_version'];
		info_bottom();
		die();
	}
	?>





<h2>Create your account</h2>
Please provide the following information. Don't worry, you can always change these settings later.

<br/><br/><br/>
<form method="post">
	<table cellspacing="0" cellpadding="5" class="config" style="margin: 0px auto;">
		<tr>
			<th>Your Email</th>
			<td><input class="field" type="text" name="user_email" value="<? echo $html['user_email']; ?>"
			           tabindex="1"/></td>
		</tr>
		<? if ($error['user_email']) {
		printf('<tr><td colspan="2">%s</td></tr>', $error['user_email']);
	} ?>
		<tr>
			<th>Time Zone</th>
			<td>
				<select name="user_timezone">
					<option <? if ($html['user_timezone'] == '-11') {
						echo 'selected=""';
					} ?> value="-11">-1100 : Samoa
					</option>
					<option <? if ($html['user_timezone'] == '-10') {
						echo 'selected=""';
					} ?> value="-10">-1000 : Alaska, Hawai'i
					</option>
					<option <? if ($html['user_timezone'] == '-9') {
						echo 'selected=""';
					} ?> value="-9">-0900 :
					</option>
					<option <? if (($html['user_timezone'] == '-8') or (empty($html['user_timezone']))) {
						echo 'selected=""';
					} ?>  value="-8">-0800 : US Pacific
					</option>
					<option <? if ($html['user_timezone'] == '-7') {
						echo 'selected=""';
					} ?> value="-7">-0700 : US Mountain
					</option>
					<option <? if ($html['user_timezone'] == '-6') {
						echo 'selected=""';
					} ?> value="-6">-0600 : US Central
					</option>
					<option <? if ($html['user_timezone'] == '-5') {
						echo 'selected=""';
					} ?> value="-5">-0500 : US Eastern
					</option>
					<option <? if ($html['user_timezone'] == '-4') {
						echo 'selected=""';
					} ?> value="-4">-0400 : Atlantic
					</option>
					<option <? if ($html['user_timezone'] == '-3.5') {
						echo 'selected=""';
					} ?> value="-3.5">-0350 : Newfoundland
					</option>
					<option <? if ($html['user_timezone'] == '-3') {
						echo 'selected=""';
					} ?> value="-3">-0300 : Brazil, Argentina
					</option>
					<option <? if ($html['user_timezone'] == '-2') {
						echo 'selected=""';
					} ?> value="-2">-0200 : Mid Atlantic
					</option>
					<option <? if ($html['user_timezone'] == '0') {
						echo 'selected=""';
					} ?> value="0">+0000 : London, Dublin
					</option>
					<option <? if ($html['user_timezone'] == '1') {
						echo 'selected=""';
					} ?> value="1">+0100 : Paris, Berlin, Amsterdam, Madrid
					</option>
					<option <? if ($html['user_timezone'] == ' 2') {
						echo 'selected=""';
					} ?> value="2">+0200 : Athens, Istanbul, Helsinki
					</option>
					<option <? if ($html['user_timezone'] == '3') {
						echo 'selected=""';
					} ?> value="3">+0300 : Kuwait, Moscow
					</option>
					<option <? if ($html['user_timezone'] == '3.5') {
						echo 'selected=""';
					} ?> value="3.5">+0350 : Tehran
					</option>
					<option <? if ($html['user_timezone'] == '5.5') {
						echo 'selected=""';
					} ?> value="5.5">+0530 : India
					</option>
					<option <? if ($html['user_timezone'] == '7') {
						echo 'selected=""';
					} ?> value="7">+0700 : Bangkok
					</option>
					<option <? if ($html['user_timezone'] == '7.5') {
						echo 'selected=""';
					} ?> value="7">+0700 :
					</option>
					<option <? if ($html['user_timezone'] == '8') {
						echo 'selected=""';
					} ?> value="8">+0800 : Hong Kong
					</option>
					<option <? if ($html['user_timezone'] == '9') {
						echo 'selected=""';
					} ?> value="9">+0900 : Tokyo
					</option>
					<option <? if ($html['user_timezone'] == '9.5') {
						echo 'selected=""';
					} ?> value="9.5">+0950 : Darwin
					</option>
					<option <? if ($html['user_timezone'] == '10') {
						echo 'selected=""';
					} ?> value="10">+1000 : Sydney
					</option>
					<option <? if ($html['user_timezone'] == '11') {
						echo 'selected=""';
					} ?> value="11">+1100 : Magadan
					</option>
					<option <? if ($html['user_timezone'] == '12') {
						echo 'selected=""';
					} ?> value="12">+1200 : Wellington
					</option>
				</select>
			</td>
		</tr>
		<tr>
			<td/>
		</tr>
		<tr>
			<th>Username</th>
			<td><input class="field" type="text" name="user_name" value="<? echo $html['user_name']; ?>" tabindex="2"/>
			</td>
		</tr>
		<? if ($error['user_name']) {
		printf('<tr><td colspan="2">%s</td></tr>', $error['user_name']);
	} ?>
		<tr>
			<th>Password</th>
			<td><input class="field" type="password" name="user_pass" tabindex="3"/></td>
		</tr>
		<? if ($error['user_pass']) {
		printf('<tr><td colspan="2">%s</td></tr>', $error['user_pass']);
	} ?>
		<tr>
			<th>Verify Pass</th>
			<td><input class="field" type="password" name="verify_user_pass" tabindex="4"/></td>
		</tr>
		<tr>
			<td colspan="2"><i>Double-check your email address before continuing.</i></td>
		</tr>
		<tr>
			<td/>
			<td><br/><input id="submit" type="submit" value="Install Prosper202 &raquo;" style="font-size: 1.2em;"/>
			</td>
		</tr>
	</table>
</form>

<?  info_bottom();

}


//if success is equal to true, and this campaign did complete
if ($success == true) {

	info_top(); ?>

<h2>Success!</h2>
<p>Prosper202 has been installed. Now you can <a href="/202-login.php">log in</a> with the <strong>username</strong>
	"<code><? echo $html['user_name']; ?></code>" and <strong>password</strong>
	"<code><? echo $html['user_pass']; ?></code>".</p>
<dl>
	<dt>Username</dt>
	<dd><code><? echo $html['user_name']; ?></code></dd>
	<dt>Password</dt>
	<dd><code><? echo $html['user_pass']; ?></code></dd>
	<dt>Login address</dt>
	<dd><code><? printf('<a href="/202-login.php">%s/202-login.php</a>', $_SERVER['SERVER_NAME']); ?></code></dd>
</dl>
<p>Were you expecting more steps? Sorry thats it!</p>

<? info_bottom();

}
