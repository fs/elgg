<?php
	/**
	 * Feedback - Edit form
	 * 
	 * @package Feedback
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	$content = '<div class="contentWrapper">';
	
	// Build form for submitting new status/comment
	$form_body .= "<div style='width: 97%; padding-left: 10px;'><label>" . elgg_echo('feedback:comments:new') . "<br />";

	// Build list of options for status dropdown
	$options = array();
	foreach (get_status_types() as $type) {
		$options[$type] = elgg_echo('feedback:status:' . $type);
	}

	$form_body .= elgg_view('input/longtext', array('internalname' => 'feedback_comments', "js" => ' style="width: 96%;"'));
		
	if ($vars['entity']->owner_guid != get_loggedin_userid() && !has_user_voted(get_loggedin_userid(), $vars['entity']->guid)) {
		$form_body .= elgg_view('input/radio', array('value' => 1, 'internalname'=>'feedbackvote', 'options'=>array(elgg_echo("feedback:update:like") =>1, elgg_echo("feedback:update:dislike") =>0)));
	} else {
		// Set value to non true/false
		$form_body .= elgg_view('input/hidden', array('value' => 2, 'internalname' => 'feedbackvote'));
	}

	$form_body .= elgg_view('input/hidden', array('value' => $vars['entity']->guid, 'internalname' => "guid"))  . "<br />";
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo("feedback:update:submit"), 'internalname' => 'submit'));


	$content .= elgg_view('input/form', array('body' => $form_body, 'action' => $vars['url'] . 'action/feedback/update', 'internalname' => 'comment', 'internalid' => 'comment'));
	
	$content .= "</label></div></div>";
	
	echo $content;
?>