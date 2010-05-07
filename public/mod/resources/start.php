<?php
	/**
	 * Resources start.php
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	/**
	* Resources init function
	* 
	* @return bool
	*/
	function resources_init() {
		global $CONFIG;
		
		// Constants for types
		define('RESOURCE_REQUEST_TYPE_CURRICULUM', 1);
		define('RESOURCE_REQUEST_TYPE_TECHNOLOGY', 2);
		define('RESOURCE_REQUEST_TYPE_PD', 3);
		define('RESOURCE_REQUEST_TYPE_OTHER', 99);
		
		// Constants for request status'
		define('RESOURCE_REQUEST_STATUS_OPEN', 1);
		define('RESOURCE_REQUEST_STATUS_APPROVED', 2);
		define('RESOURCE_REQUEST_STATUS_NOTAPPROVED', 3);
		
		// Constants for comment view levels 
		define('RESOURCE_REQUEST_COMMENT_PUBLIC', 1);
		define('RESOURCE_REQUEST_COMMENT_ADMIN', 0);

		// Extend CSS
		extend_view('css','resources/css');
		
		// Extend Metatags (for js)
		elgg_extend_view('metatags','resources/metatags');
		
		// Extend initialise for js
		elgg_extend_view('js/initialise_elgg','resources/js');
		
		// Page handler
		register_page_handler('resources','resources_page_handler');

		// Add to tools menu
		add_menu(elgg_echo("resources:title"), $CONFIG->wwwroot . 'pg/resources');

		// Add submenus
		register_elgg_event_handler('pagesetup','system','resources_submenus');
		
		// Set up url handler
		register_entity_url_handler('resource_request_url','object', 'resourcerequest');
		
		// Permissions plugin hook
		register_plugin_hook('permissions_check', 'object', 'resources_write_permission_check');
		
		// Comments plugin hook
		register_plugin_hook('comments', 'object', 'resources_view_comments');
		
		// Search plugin hook
		register_plugin_hook('search', 'object', 'resources_search');
		
		// Plugin hook to modify tag results
		register_plugin_hook('search', 'tags', 'resources_tags');

		// Register actions
		register_action('resources/create', false, $CONFIG->pluginspath . 'resources/actions/create.php');
		register_action('resources/delete', false, $CONFIG->pluginspath . 'resources/actions/delete.php');
		register_action('resources/edit', false, $CONFIG->pluginspath . 'resources/actions/edit.php');
		register_action('resources/setstatus', false, $CONFIG->pluginspath . 'resources/actions/setstatus.php');
		register_action('resources/comment', false, $CONFIG->pluginspath . 'resources/actions/comment.php');
		register_action('resources/ajaxcomment', false, $CONFIG->pluginspath . 'resources/actions/ajax_comment.php');
		
		// Register type
		register_entity_type('object', 'resourcerequest');	
		
		return true;
	}

	/**
	* Resources page handler 
	*
	* @param string $page
	* @return bool
	*/
	function resources_page_handler($page) {
		global $CONFIG;

		
		switch ($page[0])
		{
			case 'create':
				include $CONFIG->pluginspath . 'resources/pages/create.php';
				break;
			case 'view':
				set_input("request_guid", $page[1]);
				include $CONFIG->pluginspath . 'resources/pages/view.php';
				break;
			case 'edit':
				if ($page[1]) {
					set_input('request_guid', $page[1]);
				}
				include $CONFIG->pluginspath . 'resources/pages/edit.php';
			case 'admin':
				set_input('view_only', true);
				include $CONFIG->pluginspath . 'resources/pages/admin.php';
				break;
			case 'reject': 
				include $CONFIG->pluginspath . 'resources/pages/reject.php';
				break;
			default:
				set_input('view_only', true);
				include $CONFIG->pluginspath . 'resources/pages/index.php';
				break;
		}
		
		return true;
	}
	
	/**
	* Return an array of resource types for use 
	* in pulldowns
	* 
	* @return array
	*/
	function get_resource_status_array() {
		return array(
						RESOURCE_REQUEST_STATUS_OPEN		=> elgg_echo("resources:status:open") ,
						RESOURCE_REQUEST_STATUS_APPROVED 	=> elgg_echo("resources:status:approved") ,
						RESOURCE_REQUEST_STATUS_NOTAPPROVED	=> elgg_echo("resources:status:rejected") ,
					);
	}
	
	/**
	* Return an array of resource types for use 
	* in pulldowns
	* 
	* @return array
	*/
	function get_resource_types() {
		return array(
						RESOURCE_REQUEST_TYPE_CURRICULUM	=> elgg_echo("resources:type:curriculum") ,
						RESOURCE_REQUEST_TYPE_TECHNOLOGY 	=> elgg_echo("resources:type:technology") ,
						RESOURCE_REQUEST_TYPE_PD			=> elgg_echo("resources:type:pd") ,
						RESOURCE_REQUEST_TYPE_OTHER			=> elgg_echo("resources:type:other") ,
					);
	}

	/**
	* Determine if given user is a resource admin
	* 
	* @return bool
	*/
	function isresourceadmin($user) {
		$user_guids = array();
		
		for ($idx=1; $idx <= 5; $idx++) {
			$name = get_plugin_setting('admin_user_'.$idx, 'resources');
			if (!empty($name)) { 
				if ($adminuser = get_user_by_username($name)) { 
					$user_guids[] = $adminuser->guid; 
				} 
			}
		}

		if (in_array($user->guid, $user_guids))
			return true;
		else 
			return false;
	}
	
	/**
	* Determine if given user is an resource admin
	* Admins are supplied in the plugin settings
	*
	* @return bool
	*/
	function isresourceadminloggedin() {
		if (isadminloggedin()) {
			return true;
		}
		
		if (!isloggedin()) {
			return false;
		}

		$current_user = get_loggedin_user();
				
		return isresourceadmin($current_user);
	}
	
	/**
	* Ensure that we have a logged in user and  
	* that user belongs to this plugins list of admins
	* 
	* @return mixed
	*/
	function resource_admin_gatekeeper() {
		gatekeeper();

		if (!isresourceadminloggedin()) {
			$_SESSION['last_forward_from'] = current_page_url();
			register_error(elgg_echo('adminrequired'));
			forward();
		}
	}
	
	/**
	 * Populates the ->getUrl() method for resource requests
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function resource_request_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/resources/view/{$entity->guid}/";
	}

	/**
	* Set up resources related submenu's
	*/
	function resources_submenus() {
		global $CONFIG;
		
		if (get_context() == 'resources') {
			add_submenu_item(elgg_echo("resources:menu:yourresources"), $CONFIG->wwwroot . 'pg/resources');
			add_submenu_item(elgg_echo("resources:menu:createresource"), $CONFIG->wwwroot . 'pg/resources/create/');
			if (isresourceadminloggedin()) {
				add_submenu_item(elgg_echo('resources:menu:admin'), $CONFIG->wwwroot . 'pg/resources/admin/');
			}
		}
	}

	/**
	 * Plugin hook to override search behavior
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function resources_search($hook, $entity_type, $returnvalue, $params) {
		if ($params['subtype'] == 'resourcerequest' && !isresourceadminloggedin()) {
			return false;
		}
	}
	
	/**
	 * Plugin hook to override tag search behavior
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function resources_tags($hook, $entity_type, $returnvalue, $params) {

		foreach ($returnvalue['entities'] as $key => $entity) {
			if ($entity->getSubtype() == "resourcerequest") {
				unset($returnvalue['entities'][$key]);
				$count++;
			}
		}		
		return $returnvalue;
	}
	
	/**
	 * Extend typical comments behavior, now shows custom form and list
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function resources_view_comments($hook, $entity_type, $returnvalue, $params) {
		// make sure we're dealing with a resourcerequest subtype
		if ($params['entity']->getSubtype() == 'resourcerequest') {
			$request_comments = "<div id='ajax_comments_container'>";
			$request_comments .= list_annotations($params['entity']->getGUID(),'resource_request_comment');
			$request_comments .= elgg_view('resources/forms/comment',array('entity' => $params['entity']));
			$request_comments .= "</div>";
			return $request_comments;
		}
	}
	
	/**
	 * Extend permissions checking to extend can-edit for resource request admins
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 */
	function resources_write_permission_check($hook, $entity_type, $returnvalue, $params) {
		if ($params['entity']->getSubtype() == 'resourcerequest') {
			
			$user = $params['user'];
			if (isresourceadmin($user)) {
				return true;
			}
		}
	}
	
	register_elgg_event_handler('init', 'system', 'resources_init');
?>