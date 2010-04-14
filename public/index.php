<?php
/**
 * Elgg index page for web-based applications
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

/**
 * Start the Elgg engine
 */
define('externalpage',true);
require_once(dirname(__FILE__) . "/engine/start.php");

if (isset($_GET['debug'])) {
	$entities = get_entities_from_metadata('site_name', 'testing', 'object');
	foreach ($entities as $e) {
		echo $e->access_id . '<br/>';
	}
	exit;
}

if (!trigger_plugin_hook('index', 'system', null, FALSE)) {
	/**
	 * Check to see if user is logged in, if not display login form
	 **/

	if (isloggedin()) {
		forward('pg/dashboard/');
	}

	//Load the front page
	global $CONFIG;
	$title = elgg_view_title(elgg_echo('content:latest'));
	set_context('search');
	$content = elgg_list_registered_entities(array('limit' => 10, 'full_view' => FALSE, 'allowed_types' => array('object','group')));
	set_context('main');
	global $autofeed;
	$autofeed = FALSE;
	$content = elgg_view_layout('two_column_left_sidebar', '', $title . $content, elgg_view("account/forms/login"));
	page_draw(null, $content);
}