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

	require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

	// Logged in users only 
	gatekeeper();
	
	// Get GUID
	$guid = get_input('feedback_guid');
	$feedback = get_entity($guid);

	// if username or owner_guid was not set as input variable, we need to set page owner
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid)
			set_page_owner($page_owner_guid);
	}
	
	$area1 = '';
	$area2 = elgg_view_title(elgg_echo('feedback:viewtitle'));
	
	$context = get_context();
	set_context('search');
	
	$area2 .= elgg_view_entity($feedback, true);
		
	set_context($context);

	page_draw(
		elgg_echo('feedback:admin:title'),
		elgg_view_layout('two_column_left_sidebar',$area1,$area2)
	);

?>