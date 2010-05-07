<?php
	/**
	 * Elgg comments add form
	 *
	 * @package Elgg
	 * @author Curverider Ltd <info@elgg.com>
	 * @link http://elgg.com/
	 *
	 * @uses $vars['entity']
	 */


	if (isset($vars['entity']) && isloggedin()) {
		$form_body .= "<div class=\"contentWrapper\">";
		$form_body .= "<p class='longtext_editarea'><label>".elgg_echo("generic_comments:text")."<br /><div id='request_form_message'></div>" . elgg_view('input/longtext',array('internalname' => 'generic_comment')) . "</label></p>";
		if (isresourceadminloggedin()) {
			$form_body .= elgg_view('input/checkboxes', array('internalname' => 'comment_view_level', 'internalid' => 'comment_view_level', 'options' => array(elgg_echo("resources:label:publiccomment") => RESOURCE_REQUEST_COMMENT_PUBLIC)));
		} else {
			// If we're a regular user, just pass the defaults, public comment and don't notify
			$form_body .= elgg_view('input/hidden', array('internalname' => 'comment_view_level', 'value' => RESOURCE_REQUEST_COMMENT_PUBLIC));
		}
		$form_body .= "<p>" . elgg_view('input/hidden', array('internalname' => 'entity_guid', 'value' => $vars['entity']->getGUID()));
		$form_body .= elgg_view('input/submit', array('value' => elgg_echo("save"))) . "</p></div>";

	echo  elgg_view('input/form', array('internalid' => 'request_update', 'body' => $form_body, 'action' => "{$vars['url']}action/resources/comment"));

}