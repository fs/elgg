<?php

    /**
     * Elgg Feedback plugin
     * Feedback interface for Elgg sites
     * 
     * @package Feedback
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Prashant Juvekar
     * @copyright Prashant Juvekar
     * @link http://www.linkedin.com/in/prashantjuvekar
     */

	// Make sure we're logged in (send us to the front page if not)
		admin_gatekeeper();

	// Get input data
		$guid = (int) get_input('guid');
		
	// Make sure we actually have permission to edit
		$feedback = get_entity($guid);
		if ($feedback->getSubtype() == 'feedback' && $feedback->canEdit()) {
			// Delete it!
				$feedback->delete();
			// Success message
				system_message(elgg_echo("feedback:delete:success"));
			// Forward to the main blog page
				forward("mod/feedback/feedback.php");
		}
		
?>