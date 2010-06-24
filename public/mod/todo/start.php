<?php
	/**
	 * Todo Start
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * DEPENDS ON EMBEDENABLER PLUGIN (Not sure why at the moment)
	 */
	
	/*********************** TODO: (Code related) ************************/
	// - Cleaner way to handle different content attachments (views, callbacks.. yadda)
	// - Prettier everything (Rubric select, view rubric modal popup, etc.. )

	function todo_init() {
		global $CONFIG;
		
		// Lib
	    include $CONFIG->pluginspath . 'todo/lib/todo.php';

		// Assignment (todo) access levels
		define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
		define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
		
		// Determine if optional plugins are enabled
		define('TODO_RUBRIC_ENABLED', is_plugin_enabled('rubricbuilder') ? true : false);
		
		// Relationship for assignees
		define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
		
		// Relationship for submissions 
		define('SUBMISSION_RELATIONSHIP', 'submittedto');
		
		// View Modes
		define('TODO_MODE_ASSIGNER', 0);
		define('TODO_MODE_ASSIGNEE', 1);
		
		get_todo_groups_array();
		
		// Extend CSS
		elgg_extend_view('css','todo/css');
		
		// Extend Metatags (for js)
		elgg_extend_view('metatags','todo/metatags');
		
		// Add groups menu
		elgg_extend_view('groups/menu/links', 'todo/menu'); 
		
		// add the group pages tool option     
        add_group_tool_option('todo',elgg_echo('groups:enabletodo'),true);

		// Page handler
		register_page_handler('todo','todo_page_handler');

		// Add to tools menu
		add_menu(elgg_echo("todo:title"), $CONFIG->wwwroot . 'pg/todo');

		// Add submenus
		register_elgg_event_handler('pagesetup','system','todo_submenus');
		
		// Register an annotation handler for comments etc
		register_plugin_hook('entity:annotate', 'object', 'todo_annotate_comments');
		register_plugin_hook('entity:annotate', 'object', 'submission_annotate_comments');
			
		// Set up url handlers
		register_entity_url_handler('todo_url','object', 'todo');
		register_entity_url_handler('todo_submission_url','object', 'todosubmission');

		// Register actions
		register_action('todo/createtodo', false, $CONFIG->pluginspath . 'todo/actions/createtodo.php');
		register_action('todo/deletetodo', false, $CONFIG->pluginspath . 'todo/actions/deletetodo.php');
		register_action('todo/edittodo', false, $CONFIG->pluginspath . 'todo/actions/edittodo.php');
		register_action('todo/unassign', false, $CONFIG->pluginspath . 'todo/actions/unassign.php');
		register_action('todo/createsubmission', false, $CONFIG->pluginspath . 'todo/actions/createsubmission.php');
		register_action('todo/deletesubmission', false, $CONFIG->pluginspath . 'todo/actions/deletesubmission.php');

		
		// Register type
		register_entity_type('object', 'todo');		

		return true;
	}

	function todo_page_handler($page) {
		global $CONFIG;
		
		switch ($page[0]) {
			case 'createtodo':
				include $CONFIG->pluginspath . 'todo/pages/createtodo.php';
				break;
			case 'viewtodo':
				set_input("todo_guid", $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/viewtodo.php';
				break;
			case 'edittodo':
				if ($page[1]) {
					set_input('todo_guid', $page[1]);
				}
				include $CONFIG->pluginspath . 'todo/pages/edittodo.php';
				break;
			case 'viewsubmission':
				set_input("submission_guid", $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/viewsubmission.php';
				break;
			case 'owned':
				// Set page owner
				if (isset($page[1]))
					set_input('username',$page[1]);
				include $CONFIG->pluginspath . 'todo/pages/ownedtodos.php';
				break;
			case 'everyone':
				include $CONFIG->pluginspath . 'todo/pages/alltodos.php';
				break;
			case 'assigned':
			default:
				include $CONFIG->pluginspath . 'todo/pages/assignedtodos.php';
				break;
		}
		
		return true;
	}

	function todo_submenus() {
		global $CONFIG;
		$page_owner = page_owner_entity();
		
		if (get_context() == 'todo' && $page_owner instanceof ElggGroup) {
			if (can_write_to_container(get_loggedin_userid(), $page_owner->getGUID())) {
				add_submenu_item(elgg_echo("todo:menu:groupcreatetodo"), $CONFIG->wwwroot . 'pg/todo/createtodo/?container_guid=' . $page_owner->getGUID(), 'groupview');
			}
			add_submenu_item(elgg_echo("todo:menu:groupassignedtodos"), $CONFIG->wwwroot . 'pg/todo/owned/' . $page_owner->username, 'groupview');
		}
	 	
		if (get_context() == 'todo') {
			add_submenu_item(elgg_echo("todo:menu:yourtodos"), $CONFIG->wwwroot . 'pg/todo', 'userview');
			add_submenu_item(elgg_echo("todo:menu:assignedtodos"), $CONFIG->wwwroot . 'pg/todo/owned/', 'userview');
			add_submenu_item(elgg_echo("todo:menu:alltodos"), $CONFIG->wwwroot . 'pg/todo/everyone/', 'userview');			
			add_submenu_item(elgg_echo("todo:menu:createtodo"), $CONFIG->wwwroot . 'pg/todo/createtodo/', 'userview');
		}
		
		if (get_context() == 'groups' && $page_owner instanceof ElggGroup) {
			if($page_owner->todo_enable != "no") {
				add_submenu_item(sprintf(elgg_echo("todo:group"),$page_owner->name), $CONFIG->wwwroot . "pg/todo/owned/" . $page_owner->username);
			}
		}
	}
	
	/**
	 * Populates the ->getUrl() method for todo entities
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function todo_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/todo/viewtodo/{$entity->guid}/";
	}
	
	/**
	 * Populates the ->getUrl() method for todo submission entities
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function todo_submission_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/todo/viewsubmission/{$entity->guid}/";
	}
	
	/**
	 * Hook into the framework and provide comments on todo entities.
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 * @return unknown
	 */
	function todo_annotate_comments($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$full = $params['full'];
		
		if (
			($entity instanceof ElggEntity) &&	// Is the right type 
			($entity->getSubtype() == 'todo') &&  // Is the right subtype
			($full) // This is the full view
		)
		{
			// Display comments
			return elgg_view_comments($entity);
		}
		
	}
	
	/**
	 * Hook into the framework and provide comments on submission entities.
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 * @return unknown
	 */
	function submission_annotate_comments($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$full = $params['full'];
		
		if (
			($entity instanceof ElggEntity) &&	// Is the right type 
			($entity->getSubtype() == 'todosubmission') &&  // Is the right subtype
			($full) // This is the full view
		)
		{
			// Display comments
			return elgg_view_comments($entity);
		}
		
	}
	
	register_elgg_event_handler('init', 'system', 'todo_init');
?>