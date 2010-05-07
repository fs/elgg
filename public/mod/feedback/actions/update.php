<?php

    /**
	 * Feedback - Update feedback action
	 * 
	 * @package Feedback
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Make sure we're logged in (send us to the front page if not)
	gatekeeper();

	// Get input data
	$guid = (int) get_input('guid');

	$feedback_vote = get_input('feedbackvote');

	$comments = get_input('feedback_comments');
	
	$feedback = get_entity($guid);
	if ($feedback->getSubtype() == "feedback") {

		$is_comment = false;
		
		// Update Comments
		if ($comments && strlen($comments) > 0) {
			$is_comment = true;
			$feedback->annotate('comment', serialize(array("comment" => $comments, "feedbackvote" => $feedback_vote)), ACCESS_LOGGED_IN);	
		}
			
		// Success message
		system_message(elgg_echo("feedback:update:success"));
		
		global $CONFIG;
		
		notify_user($feedback->owner_guid, $CONFIG->site->guid, elgg_echo('feedback:email:updatedsubject'), sprintf(elgg_echo('feedback:email:updatedbody'), $CONFIG->url . "pg/feedback/view/{$feedback->getGUID()}/"));
		
		// Add to river if setting is enabled
		if (get_plugin_setting('enableriver', 'feedback'))
				// Depending on which way the user voted, spice up the text on the river
				switch ($feedback_vote) {
					case 0:
						add_to_river('river/object/feedback/commentdislike', 'comment', get_loggedin_userid(), $feedback->getGUID());
						break;
					case 1:
						add_to_river('river/object/feedback/commentlike', 'comment', get_loggedin_userid(), $feedback->getGUID());
						break;
					case 2: 
						add_to_river('river/object/feedback/comment', 'comment', get_loggedin_userid(), $feedback->getGUID());
						break;
				}
					
		// Forward to the feedback page, filtered by current status
		forward("pg/feedback/view/{$feedback->getGUID()}/");
		
	} 
		
	// Register error
	register_error(elgg_echo("feedback:update:failure"));
	forward("mod/feedback/feedback.php");
		
?>