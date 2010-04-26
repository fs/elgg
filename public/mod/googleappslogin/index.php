<?php	

// Load Elgg framework
require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

// Ensure only logged-in users can see this page
gatekeeper();

// Set the context to settings
set_context('settings');

// Get the form
global $SESSION;


$options = array(elgg_echo('googleappslogin:settings:yes')=>'yes',
		elgg_echo('googleappslogin:settings:no')=>'no'
);

$access_types = array(
		'private' => '0',
		'logged-in' => '1',
		'public' => '22'
);

$user = $_SESSION['user'];
$subtype = $user->getSubtype();

if ($user->connect == 1) {
	$subtype = 'googleapps';
	$user->google = 1;
}

googleapps_sync_sites();





$body = elgg_view('googleappslogin/googlesites/form');

// Insert it into the correct canvas layout
$body = elgg_view_layout('two_column_left_sidebar','',$body);

// Draw the page
page_draw(elgg_echo('googleappslogin:google_sites_settings'),$body);