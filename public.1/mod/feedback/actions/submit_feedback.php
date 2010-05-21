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

	// start the elgg engine
		require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');

	// check if captcha functions are loaded
		if ( function_exists ( "captcha_verify_captcha" ) ) {
			// captcha verification
			$token = get_input('captcha_token');
			$input = get_input('captcha_input');
			if ( !$token || !captcha_verify_captcha($input, $token) ) {
				echo "<div id='feedbackError'>".elgg_echo('captcha:captchafail')."</div>";
				return;
			}
		}

	// Initialise a new ElggObject
		$feedback = new ElggObject();
		
	// Tell the system it's a feedback
		$feedback->subtype = "feedback";
	
	// Set the feedback's content
		$feedback->page = get_input('page');
		$feedback->mood = get_input('mood');
		$feedback->about = get_input('about');
		$feedback->id = get_input('id');
		$feedback->txt = get_input('txt');
		$feedback->title = get_input('title');
		$feedback->status = "submitted";
	
	// Set access id to logged in users
		$feedback->access_id = ACCESS_LOGGED_IN; 
	
	// save the feedback now
		$feedback->save();
		
	// Success message
		echo "<div id='feedbackSuccess'>".elgg_echo("feedback:submit:success")."</div>";

	// add to river if setting is enabled
	if (get_plugin_setting('enableriver', 'feedback'))
		add_to_river('river/object/feedback/create', 'create', get_loggedin_userid(), $feedback->getGUID());
		

	// now email if required
		$user_guids = array();
		for ( $idx=1; $idx<=5; $idx++ ) {
			$name = get_plugin_setting( 'user_'.$idx, 'feedback' );
			if ( !empty($name) ) { 
				if ( $user = get_user_by_username($name) ) { 
					$user_guids[] = $user->guid; 
				} 
			}
		}
		if ( count($user_guids) > 0 ) {	
			global $CONFIG;
			notify_user( 
				$user_guids, $CONFIG->site->guid, 
				sprintf(elgg_echo('feedback:email:subject'), $feedback->id), 
				sprintf(elgg_echo('feedback:email:body'), $feedback->txt)
			);
		}
?>
