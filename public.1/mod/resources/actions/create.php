<?php
	/**
	 * Resources - Create resource request action
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$title 			= get_input('request_title');
	$description 	= get_input('request_description');
	$type 			= get_input('request_type');
	$tags 			= string_to_tag_array(get_input('request_tags'));
	
	// Cache to session
	$_SESSION['user']->resource_request_title = $title;
	$_SESSION['user']->resource_request_description = $description;
	$_SESSION['user']->resource_request_type = $type;
	$_SESSION['user']->resource_request_tags = $tags;
	
	// Process
	if (empty($title)) {
		register_error(elgg_echo('resources:error:titleblank'));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$request = new ElggObject();
	$request->subtype 		= "resourcerequest";
	$request->title 		= $title;
	$request->description 	= $description;
	$request->request_type 	= $type;
	$request->access_id 	= ACCESS_LOGGED_IN;
	$request->tags 			= $tags;
	$request->request_status = RESOURCE_REQUEST_STATUS_OPEN;
	
	// Save
	if (!$request->save()) {
		register_error(elgg_echo("resources:error:create"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	// Clear Cached info
	remove_metadata($_SESSION['user']->guid,'resource_request_title');
	remove_metadata($_SESSION['user']->guid,'resource_request_description');
	remove_metadata($_SESSION['user']->guid,'resource_request_type');
	remove_metadata($_SESSION['user']->guid,'resource_request_tags');

	// Save successful, forward to index
	system_message(elgg_echo('resources:success:create'));
	forward('pg/resources');
?>