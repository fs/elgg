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
	
	$js = <<<EOT
					<script type="text/javascript">
						function feedback_toggle_comments(guid) {
							$('#feedback_edit_' + guid).toggle(200); 
							if ($('span#arrow_' + guid).text() == '↓') {
								$('span#arrow_' + guid).text('↑');
							} else if ($('span#arrow_' + guid).text() == '↑') {
								$('span#arrow_' + guid).text('↓');
							}
						}
					</script>
EOT;
	
	// Check for and validate status	
	$status = get_input('status');
	
	if (in_array($status, get_status_types())) {
		$is_status = true;	
	} else {
		$is_status = false;
	}
	
	$order	= get_input('o');
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);

	// if username or owner_guid was not set as input variable, we need to set page owner
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid)
			set_page_owner($page_owner_guid);
	}
	
	$area1 = '';
	
	$area2 = elgg_view_title(elgg_echo('feedback:admin:title'));
	$area2 .= $js;
	$area2 .= elgg_view("feedback/nav", array("page" => "feedback.php", "status" => $status));
	$feedback_list = '';
	
	$context = get_context();
	set_context('search');
	
	if ($is_status) {
		$feedback_list = list_entities_from_metadata('status', $status, 'object', 'feedback', 0, $limit, false, false);

	} else {		
		$feedback_list = elgg_list_entities(array('type' => 'object', 'subtype' => 'feedback', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE, 'status' => $status));
	//	$feedback_list = elgg_list_entities(array('types' => 'object', 'subtypes' => 'rubric', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
	} 
	

		
	set_context($context);
	
	if (strlen($feedback_list) > 1) {
		$area2 .= $feedback_list;
	} else {
		$area2 .=  elgg_view('feedback/noresults');
	}

	page_draw(
		elgg_echo('feedback:admin:title'),
		elgg_view_layout('two_column_left_sidebar',$area1,$area2)
	);

?>