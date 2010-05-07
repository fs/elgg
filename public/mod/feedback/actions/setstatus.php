<?php
	/**
	 * Feedback - Set resource request status action
	 * 
	 * @package Feedback
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Only admins can update status
	admin_gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid = get_input('feedback_guid');
	$status = get_input('s');
	$full = get_input('full', false);

	$feedback = get_entity($guid);
	
	$canedit = isadminloggedin(); 
	
	// Get status array and flip for easy search
	$status_array = get_status_types();
	$status_flipped = array_flip($status_array);
	
	if ($feedback->getSubtype() == "feedback" && $canedit && in_array($status, $status_flipped)) {
		
		$feedback->status = $status_array[$status];
		
		// Save
		if (!$feedback->save()) {
			register_error(elgg_echo("feedback:error:status"));		
			forward("mod/feedback/feedback.php");
		}

		add_to_river('river/object/feedback/update', 'update', get_loggedin_userid(), $feedback->getGUID());
		
		// Save successful
		system_message(elgg_echo('resources:success:edit'));
		// Forward
		if ($full) {
				forward("pg/feedback/view/{$feedback->guid}");
		} else {
			forward("mod/feedback/feedback.php?status=".$status_array[$status]);
		}
	}
?>