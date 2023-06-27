<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( ) );

dvwaDatabaseConnect();

if( isset( $_POST[ 'Login' ] ) ) {
// Anti-CSRF
if (array_key_exists ("session_token", $_SESSION)) {
	$session_token = $_SESSION[ 'session_token' ];
} else {
	$session_token = "";
}

checkToken( $_REQUEST[ 'user_token' ], $session_token, 'login.php' );

$user = $_POST[ 'username' ];
$user = stripslashes( $user );
$user = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $user ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

$pass = $_POST[ 'password' ];
$pass = stripslashes( $pass );
$pass = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $pass ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
$pass = md5( $pass );

$query = ("SELECT table_schema, table_name, create_time
			FROM information_schema.tables
			WHERE table_schema='{$_DVWA['db_database']}' AND table_name='users'
			LIMIT 1");
$result = @mysqli_query($GLOBALS["___mysqli_ston"],  $query );
if( mysqli_num_rows( $result ) != 1 ) {
	dvwaMessagePush( "First time using DVWA.<br />Need to run 'setup.php'." );
	dvwaRedirect( DVWA_WEB_PAGE_TO_ROOT . 'setup.php' );
}

$query  = "SELECT * FROM `users` WHERE user='$user' AND password='$pass';";
$result = @mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '.<br />Try <a href="setup.php">installing again</a>.</pre>' );
if( $result && mysqli_num_rows( $result ) == 1 ) {    // Login Successful...
	dvwaMessagePush( "You have logged in as '{$user}'" );
	dvwaLogin( $user );
	dvwaRedirect( DVWA_WEB_PAGE_TO_ROOT . 'index.php' );
}

// Login failed
dvwaMessagePush( 'Login failed' );
dvwaRedirect( 'login.php' );
}

$messagesHtml = messagesPopAllToHtml();

Header( 'Cache-Control: no-cache, must-revalidate');    // HTTP/1.1
Header( 'Content-Type: text/html;charset=utf-8' );      // TODO- proper XHTML headers...
Header( 'Expires: Tue, 23 Jun 2009 12:00:00 GMT' );     // Date in the past

// Anti-CSRF
generateSessionToken();

echo "<!DOCTYPE html>

<html lang=\"en-GB\">

<head>

	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

	<title>TB:: Login Page</title>

	<link rel=\"stylesheet\" type=\"text/css\" href=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/css/login.css\" />

</head>

<body>

<div id=\"wrapper\">

<div id=\"header\">

<br />
<div class=\"topnav\">
<img src=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/images/login_logo1.png\" width=\"70\" height=\"70\" style=\"float:none\"/>
<a href=\"#about\">About</a>
<a href=\"#howto\">How To</a>
<a href=\"#solutions\">Solutions</a>
<a href=\"#help\">Help</a>
</div>

<br />

</div> <!--<div id=\"header\">-->

<div id=\"content\">

<form action=\"login.php\" method=\"post\">

<fieldset>

		<label for=\"user\">Username or Access card</label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"username\">

		<label for=\"pass\">Password</label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\">

		<label><input style=\"display:inline;\" type=\"checkbox\" name=\"RememberMe\" id=\"RememberMe\" >Remember Me<br /></label>
		<p style=\"display:inline;\"><br><br><br><br><br><br><br><br><br><input style=\"display:inline;\" type=\"submit\" value=\"Login\" name=\"Login\"></p>
		<p>" . dvwaExternalLinkUrlGet( 'http://127.0.0.1/DVWA', 'Forgot Password' ) . "</p>
	</fieldset>

	" . tokenField() . "

	</form>

	<br />

	{$messagesHtml}

	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />

	<!-- <img src=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/images/RandomStorm.png\" /> -->
	</div > <!--<div id=\"content\">-->

	<div id=\"footer\">

	<p> TB Banking Website </p>

	</div> <!--<div id=\"footer\"> -->

	</div> <!--<div id=\"wrapper\"> -->

	</body>

</html>";

?>
