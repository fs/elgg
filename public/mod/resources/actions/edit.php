<?php
	/**
	 * Resources - Edit resource request action
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Only admins can delete
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid 			= get_input('request_guid');
	$title 			= get_input('request_title');
	$description 	= get_input('request_description');
	$type 			= get_input('request_type');
	$tags 			= string_to_tag_array(get_input('request_tags'));
	
	$request = get_entity($guid);
	
	if ($request->getSubtype() == "resourcerequest" && $request->canEdit()) {
		
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
		
		$request->title 		= $title;
		$request->description 	= $description;
		$request->request_type	= $type;
		$request->tags			= $tags;
		
		// Save
		if (!$request->save()) {
			register_error(elgg_echo("resources:error:create"));		
			forward($_SERVER['HTTP_REFERER']);
		}

		// Clear cached info
		remove_metadata($_SESSION['user']->guid,'resource_request_title');
		remove_metadata($_SESSION['user']->guid,'resource_request_description');
		remove_metadata($_SESSION['user']->guid,'resource_request_type');
		remove_metadata($_SESSION['user']->guid,'resource_request_tags');

		// Save successful, forward to index
		system_message(elgg_echo('resources:success:edit'));
		forward('pg/resources');	
	}
	
	register_error(elgg_echo("resources:error:edit"));		
	forward($_SERVER['HTTP_REFERER']);

?>