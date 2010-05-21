<?php
	/**
	 * View Request Page 
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// include the Elgg engine
	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php"; 

	// Logged in users only
	gatekeeper();

	$request_guid = get_input('request_guid');
	
	// if username or owner_guid was not set as input variable, we need to set page owner
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid)
			set_page_owner($page_owner_guid);
	}	
	
	$request = get_entity($request_guid);
	
	// Make sure only original authors and resource admins can view the request
	if (!$request->canEdit()) {
		register_error(elgg_echo("resources:error:noaccess"));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$title = $request->title;
	
	// create content for main column
 	$content = elgg_view_entity($request, true);
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>