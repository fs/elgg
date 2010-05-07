<?php
	/**
	 * Resources - Set resource request status action
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// start the elgg engine
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid = get_input('request_guid');
	$status = get_input('s');

	$request = get_entity($guid);
	
	$canedit = isresourceadminloggedin(); 
	
	// Get status array and flip for easy search
	$status_array = get_resource_status_array();
	$status_flipped = array_flip($status_array);
	
	if ($request->getSubtype() == "resourcerequest" && $canedit && in_array($status, $status_flipped)) {
		
		$request->request_status = $status;
		
		// Save
		if (!$request->save()) {
			//register_error(elgg_echo("resources:error:create"));		
			echo false;
			die();
		}

		// Clear cached info
		remove_metadata($_SESSION['user']->guid,'resource_request_title');
		remove_metadata($_SESSION['user']->guid,'resource_request_description');
		remove_metadata($_SESSION['user']->guid,'resource_request_type');
		remove_metadata($_SESSION['user']->guid,'resource_request_tags');

		$user = get_loggedin_user();

		notify_user($request->owner_guid,
					$user->guid,
					elgg_echo('resources:email:subject'),
					sprintf(
						elgg_echo('resources:email:body'),
						$request->title,
						strtolower($status_array[$status]), 
						$user->name,
						$request->getURL()
					)
				);

		// Save successful, forward to index
		//system_message(elgg_echo('resources:success:edit'));
		
		// Forward to the admin page
		//forward("pg/resources/view/$guid");
		echo $status_array[$status];
	}
?>