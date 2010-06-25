<?php
	/**
	 * View Todo Page
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
	
	$todo_guid = get_input('todo_guid');
	
	$todo = get_entity($todo_guid);
	
	$container = $todo->container_guid;

	if ($container) {
		set_page_owner($container);
	} else {
		set_page_owner($pages->owner_guid);
	}
	
	$title = $todo->title;	
	// create content for main column
 	$content = elgg_view_entity($todo, true);
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>