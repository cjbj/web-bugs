<?php

// Obtain common includes
require_once '../include/prepend.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if (empty($id)) {
	die('invalid bug id');
}

if (!isset($_POST['score'])) die("missing parameter score");
$score = (int) $_POST['score'];
if ($score < -2 || $score > 2) {
	die("invalid score: $score");
}

if (!isset($_POST['reproduced'])) die("missing parameter reproduced");
$reproduced = (int) $_POST['reproduced'];

$samever = isset($_POST['samever']) ? (int) $_POST['samever'] : 0;
$sameos = isset($_POST['sameos']) ? (int) $_POST['sameos'] : 0;

if (!$dbh->prepare("SELECT id FROM bugdb WHERE id= ? LIMIT 1")->execute(array($id))->fetchOne()) {
	response_header('No such bug.');
	display_bug_error("No such bug #{$id}");
	response_footer();
	exit;
}

// Figure out which IP the user is coming from avoiding RFC 1918 space
function get_real_ip ()
{
	$ip = false;

	// User is behind a proxy and check that we discard RFC1918 IP
	// addresses if they are behind a proxy then only figure out which
	// IP belongs to the user. Might not need any more hacking if
	// there is a squid reverse proxy infront of apache.
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) { array_unshift($ips, $ip); $ip = false; }
		for ($i = 0; $i < count($ips); $i++) {
			 // Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16
			 // -- jim kill me later with my regexp pattern below.
			if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i]) &&
				preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

$ip = ip2long(get_real_ip());
// TODO: check if ip address has been banned. hopefully this will never need to be implemented.

// add the vote
$dbh->prepare("
	INSERT INTO bugdb_votes (bug,ip,score,reproduced,tried,sameos,samever)
	VALUES (
		{$id}, {$ip}, {$score}, " .
		($reproduced == 1 ? "1," : "0,") .
		($reproduced != 2 ? "1," : "0,") .
		($reproduced ? "$sameos," : "NULL,") .
		($reproduced ? "$samever" : "NULL") .
	')'
)->execute();

// redirect to the bug page (which will display the success message)
header("Location: bug.php?id=$id&thanks=6");
exit;
