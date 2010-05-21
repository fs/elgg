<?php
	/**
	 * Resources Admin Page
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

	// Admins only
	resource_admin_gatekeeper();
	
	// if username or owner_guid was not set as input variable, we need to set page owner
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid)
			set_page_owner($page_owner_guid);
	}	

	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);
	$status = get_input('status', null);

	$title = elgg_echo('resources:title:admin');
	
	// create content for main column
	$content = elgg_view_title($title);
	
	$context = get_context();
	set_context('search');
	
	$content .= elgg_view('resources/nav');
	if ($status && in_array($status, array_flip(get_resource_status_array()))) {
		$list = list_entities_from_metadata('request_status', $status, 'object', 'resourcerequest', 0, $limit, false, false);
	} else {
		$list = elgg_list_entities(array('types' => 'object', 'subtypes' => 'resourcerequest', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
	}
	
	if (strlen($list) > 1) {
		$content .= $list;
	} else {
		$content .= elgg_view('resources/noresults');
	}

	set_context($context);
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>