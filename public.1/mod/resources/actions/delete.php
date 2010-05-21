<?php
	/**
	 * Resources - Delete resource request action
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Only resource admins can delete
	resource_admin_gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid = get_input('request_guid');

	$request = get_entity($guid);
	
	$candelete = isresourceadminloggedin();
	
	if ($request->getSubtype() == "resourcerequest" && $candelete) {
		
		// Delete it!
		$rowsaffected = $request->delete();
		
		if ($rowsaffected > 0) {
			// Success message
			system_message(elgg_echo("resources:success:delete"));
			
		} else {
			register_error(elgg_echo("resources:error:delete"));
		}
		
		// Forward to the admin page
		forward("pg/resources/admin");
	}
?>