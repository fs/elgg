<?php
	/**
	 * RubricBuilder start.php
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	/** Rubric builder initialisation **/
	function rubricbuilder_init() {
		global $CONFIG;
		include_once('lib/rubric.php');

		// Extend CSS
		elgg_extend_view('css','rubricbuilder/css');
		
		// Extend Metatags (for js)
		elgg_extend_view('metatags','rubricbuilder/metatags'); 
		
		// Register page handler
		register_page_handler('rubric','rubricbuilder_page_handler');
		
		// Set up url handler
		register_entity_url_handler('rubric_url','object', 'rubric');

		// Add rubrics to main menu
		add_menu('Rubrics', $CONFIG->wwwroot . 'pg/rubric/index/');

		// Event handler for submenus
		register_elgg_event_handler('pagesetup','system','rubricbuilder_submenus');

		// Register actions
		register_action('rubric/add', false, $CONFIG->pluginspath . 'rubricbuilder/actions/add.php');
		register_action('rubric/edit', false, $CONFIG->pluginspath . 'rubricbuilder/actions/edit.php');
		register_action('rubric/delete', false, $CONFIG->pluginspath . 'rubricbuilder/actions/delete.php');
		register_action('rubric/fork', false, $CONFIG->pluginspath . 'rubricbuilder/actions/fork.php');
		register_action('rubric/restore', false, $CONFIG->pluginspath . 'rubricbuilder/actions/restore.php');
				
		// Add widget 
		add_widget_type('rubric',elgg_echo('Rubrics'),elgg_echo('rubricbuilder:widget:description'));
		
		// Register an annotation handler for comments etc
		register_plugin_hook('entity:annotate', 'object', 'rubric_annotate_comments');
		
		// Register plugin hook to extend permissions checking to include write access
		register_plugin_hook('permissions_check', 'object', 'rubric_write_permission_check');
		
	    // This operation only affects the db on the first call for this subtype
	    // If you change the class name, you'll have to hand-edit the db
		run_function_once("rubricbuilder_run_once");
		register_entity_type('object', 'rubric');	
	}
	
	
	/**
	* Rubricbuilder's Page Handler
	* 
	* @param array $page From the page_handler function
	* @return true|false Depending on success
	*
	*/
	function rubricbuilder_page_handler($page) {
		global $CONFIG;
		
		switch ($page[0])
		{

			case 'friends':
				include $CONFIG->pluginspath . 'rubricbuilder/pages/friends.php';
				break;
			case 'everyone':
				include $CONFIG->pluginspath . 'rubricbuilder/pages/everyone.php';
				break;
			case 'add':
				include $CONFIG->pluginspath . 'rubricbuilder/pages/add.php';
				break;
			case 'edit':
				if ($page[1]) {
					set_input('rubric_guid', $page[1]);
					include $CONFIG->pluginspath . 'rubricbuilder/pages/edit.php';
				} else { 
					include $CONFIG->pluginspath . 'rubricbuilder/pages/index.php';
				}
				break;
			case "history":
				if (isset($page[1])) {
					set_input('rubric_guid', $page[1]);
					add_submenu_item(elgg_echo('rubricbuilder:label:view'), $CONFIG->url . "pg/rubric/view/{$page[1]}", 'rubriclinks');
					include $CONFIG->pluginspath . 'rubricbuilder/pages/history.php';
				} else {
					include $CONFIG->pluginspath . 'rubricbuilder/pages/index.php';
				}
				break;
			case "view" :		
    			if (isset($page[1])) {
    				set_input('rubric_guid', $page[1]);				
					if (isset($page[2])) {
						set_input('rubric_revision', $page[2]);
					}
						
					include($CONFIG->pluginspath . "rubricbuilder/pages/view.php");
				} else {
					include $CONFIG->pluginspath . 'rubricbuilder/pages/index.php';
				}	
				
				break;
			case "list" : 
				if (isset($page[1])) {
					set_input("listuser", $page[1]);
				}
				include $CONFIG->pluginspath . 'rubricbuilder/pages/index.php';
				break;
			default:
				// If we're here, all we've got is a username
				set_input('username', $page[0]);
				include $CONFIG->pluginspath . 'rubricbuilder/pages/index.php';
				break;				
			
		
		}
		
		return true;
	}
	
	/**
	 * Extend permissions checking to extend can-edit for write users.
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function rubric_write_permission_check($hook, $entity_type, $returnvalue, $params)
	{
		if ($params['entity']->getSubtype() == 'rubric') {
		
			$write_permission = $params['entity']->write_access_id;
			$user = $params['user'];
			
			if (($write_permission !== null) && ($user)) {
				$list = get_access_array($user->guid); // get_access_list($user->guid);
				if (($write_permission != 0) && (in_array($write_permission,$list))) {
					return true;
				} else if ($write_permission == -2 && ($user)) {
					if ($user->isFriendOf($params['entity']->getOwner())) {
						return true;
					}
				}
			}
		}
	}

	function rubricbuilder_submenus() {
		global $CONFIG;
		
		if (get_context() == 'rubric') {
			add_submenu_item(elgg_echo('rubricbuilder:myrubrics'), $CONFIG->wwwroot . 'pg/rubric/' .$_SESSION['user']->username);
			add_submenu_item(elgg_echo('rubricbuilder:friendsrubrics'), $CONFIG->wwwroot . 'pg/rubric/friends/');
			add_submenu_item(elgg_echo('rubricbuilder:allrubrics'), $CONFIG->wwwroot . 'pg/rubric/everyone/');
			add_submenu_item(elgg_echo('rubricbuilder:create'), $CONFIG->wwwroot . 'pg/rubric/add/');
		}
		
	}
	
	/**
	 * Hook into the framework and provide comments on rubric entities.
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 * @return unknown
	 */
	function rubric_annotate_comments($hook, $entity_type, $returnvalue, $params)
	{
		$entity = $params['entity'];
		$full = $params['full'];
		
		if (
			($entity instanceof ElggEntity) &&	// Is the right type 
			($entity->getSubtype() == 'rubric') &&  // Is the right subtype
			($entity->comments_on!='Off') && // Comments are enabled
			($full) // This is the full view
		)
		{
			// Display comments
			return elgg_view_comments($entity);
		}
		
	}
	
	/**
	 * Populates the ->getUrl() method for rubrics
	 *
	 * @param ElggEntity entity
	 * @return string rubric url
	 */
	function rubric_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/rubric/view/{$entity->guid}/";
	}

	/** 
	* Runonce for rubrics
	* 
	* Registers the rubrics subtype
	*
	*/
	function rubricbuilder_run_once() {
		add_subtype('object', 'rubric', 'Rubric');
	}
	
	// Helpful debug function
	function print_r_html ($arr) {
	        ?><pre><?
	        print_r($arr);
	        ?></pre><?
	}


	
	register_elgg_event_handler('init', 'system', 'rubric_init');
	register_elgg_event_handler('init', 'system', 'rubricbuilder_init');
?>