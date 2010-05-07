<?php 
	/**
	 * Resource Request create/edit form 
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	$request = get_entity($vars['guid']);
	
	if ($request) {
	
		$form_body .= "<div id='reject_comment' class=\"contentWrapper\" >";
		$form_body .= "<label>" . elgg_echo("resources:label:commentrequired") . "</label>";
		$form_body .= "<p class='longtext_editarea'><br /><div id='request_form_message'></div>" . elgg_view('input/plaintext',array('internalname' => 'reject_textarea', 'internalid' => 'reject_textarea')) . "</p>";
		$form_body .= elgg_view('input/submit', array('value' => elgg_echo("save"))) . "</p>";
		$form_body .= "</div>";
		
		echo elgg_view('input/form', array('action' => "{$vars['url']}action/resources/setstatus.php?request_guid=" . $vars['guid'] ."&s=" . RESOURCE_REQUEST_STATUS_NOTAPPROVED, 'body' => $form_body, 'internalid' => 'resource_request_reject_form'));

	} 
?>