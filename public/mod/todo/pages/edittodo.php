<?php
	/**
	 * Todo Edit Page
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// include the Elgg engine
	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php"; 

	// Logged in users only
	gatekeeper();
	
	$vars['entity'] = get_entity(get_input('todo_guid'));
	
	// Get the current page's owner
	if ($container = $vars['entity']->container_guid) {
		set_page_owner($container);
	}
	$page_owner = page_owner_entity();
	if ($page_owner === false || is_null($page_owner)) {
		$page_owner = $_SESSION['user'];
		set_page_owner($page_owner->getGUID());
	}

	$title = elgg_echo('todo:title:edit');
	
	// create content for main column
	$content = elgg_view_title($title);
	$content .= elgg_view("todo/forms/edittodo", $vars);
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>