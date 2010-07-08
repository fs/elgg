<?php	

// Load Elgg framework
require_once($_SERVER['DOCUMENT_ROOT'] . '/engine/start.php');

// Ensure only logged-in users can see this page
gatekeeper();

// Set the context to settings
set_context('settings');

// Get the form
global $SESSION;


$body = elgg_view('googleappslogin/googlesites/form');

// Insert it into the correct canvas layout
$body = elgg_view_layout('one_column_with_sidebar', $body);

// Draw the page
page_draw(elgg_echo('googleappslogin:google_sites_settings'),$body);
