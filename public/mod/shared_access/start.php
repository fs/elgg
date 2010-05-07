<?php
/**
 * Elgg shared access plugin
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

/**
 * Init Shared Access plugin.
 * 
 * @return unknown_type
 */
function shared_access_init() {
	global $CONFIG;

	// extend css
	elgg_extend_view('css', 'shared_access/css');
	
	// show up in Conversations and Activity sidebars
	elgg_extend_view('conversations/sidebar', 'shared_access/sidebar/thewire_ext');
	elgg_extend_view('riverdashboard/sidebar', 'shared_access/sidebar/thewire_ext');
	
	// page handler for invitations and managing shared access lists.
	register_page_handler('shared_access', 'shared_access_page_handler');
	
	// page handler for ajax.
	register_page_handler('shared_access_ajax', 'shared_access_ajax_handler');
	
	// register hooks for collection editing actions
	// no need to register with add because you cannot create a shared access collection
	// without going through the SAC interface.
	//register_plugin_hook('access:collections:addcollection', 'collection', 'shared_access_addcollection_hook');
	register_plugin_hook('access:collections:deletecollection', 'collection', 'shared_access_deletecollection_hook');
	
	// register hooks for removing and adding to SACs (and invitations)
	register_elgg_event_handler('delete', 'shared_access_member', 'shared_access_remove_user_hook');
	register_elgg_event_handler('create', 'shared_access_invitation', 'shared_access_invite_user_hook');
	register_elgg_event_handler('create', 'shared_access_member', 'shared_access_add_user_hook');
	//register_elgg_event_handler('delete', 'shared_access_invitation', 'shared_access_add_user_hook');
	
	// register actions for joining, declining, and leaving shared access collection
	register_action('shared_access/join', false, $CONFIG->pluginspath . 'shared_access/actions/join.php', false);
	register_action('shared_access/decline', false, $CONFIG->pluginspath . 'shared_access/actions/decline.php', false);
	register_action('shared_access/leave', false, $CONFIG->pluginspath . 'shared_access/actions/leave.php', false);
	register_action('shared_access/edit', false, $CONFIG->pluginspath . 'shared_access/actions/edit.php', false);
	register_action('shared_access/delete', false, $CONFIG->pluginspath . 'shared_access/actions/delete.php', false);
	
	// hook into access collections
	register_plugin_hook('access:collections:write', 'all', 'shared_access_write_acl_plugin_hook');
}

/**
 * Handles pages for shared access.
 * 
 * @param $page
 * @return unknown_type
 */
function shared_access_page_handler($page) {
	gatekeeper();
	$user = get_loggedin_user();
	if (!is_array($page) || !array_key_exists(0, $page)) {
		$page = array(0=>'home');
	}
	
	// see if we have any SACs.  Go to the manage page if we don't.
	switch($page[0]) {
		case 'home':
			$content = elgg_view('shared_access/home', array('user'=>$user));
			break;
			
		case 'new':
			$area1 = elgg_view_title(elgg_echo('shared_access:new_collection'));
			$area2 = elgg_view('shared_access/edit');
			$content = elgg_view_layout("one_column_with_sidebar", $area1 . $area2);
			break;
			
		case 'thewire':
			if (!$sac = get_entity($page[1]) OR $sac->getSubtype() != 'shared_access') {
				forward($CONFIG->site->url . 'pg/shared_access/home');
			} else {
				$body = elgg_view('conversations/shared_access', array('sac' => $sac));
			}
			
			page_draw(elgg_echo('thewire:thewire'),$body);
			
			exit;
			break;
			
		// everything else is a sac id.
		default:
			// check if we have a specific collection to show or forward to home
			if (!$sac = get_entity($page[0]) OR $sac->getSubtype() != 'shared_access') {
				forward($CONFIG->site->url . 'pg/shared_access/home');
			}
			
			$ajax = get_input('ajax', false);
			switch ($page[1]) {
				case 'details':
					$title = elgg_echo('shared_access:shared_access_details');
					$content = elgg_view('shared_access/details', array('user'=>$user, 'sac'=>$sac));
					break;
					
				case 'edit':
					$title = elgg_echo('shared_access:shared_access_details');
					$content = elgg_view('shared_access/edit', array('user'=>$user, 'sac'=>$sac));
					break;
//					
//				case 'members':
//					$title = elgg_echo('shared_access:shared_access_details');
//					$content = elgg_view('shared_access/details', array('user'=>$user, 'sac'=>$sac));
//					break;
					
				default:
				$title = elgg_echo('shared_access:shared_access');
				$content = elgg_view('shared_access/content', array('user'=>$user, 'sac'=>$sac));
				break;
			}
			break;		
	}
	// output content immediately if in ajax mode.
	// else, pull in javascript and draw a page.
	if (get_input('ajax') == true) {
		echo $content;
	} else {
		$content .= elgg_view('shared_access/js');
		page_draw($title, $content);
	}
	
	return true;
}


/**
 * Hooks into adding a user to a SAC.  Handles having them join the ACL.
 * @param $hook
 * @param $entity_type
 * @param $returnvalue
 * @param $params
 * @return unknown_type
 */
function shared_access_add_user_hook($event, $object_type, $object) {
	global $CONFIG;
	
	$user = get_entity($object->guid_one);
	$sac = get_entity($object->guid_two);
	
	// don't do this for owners.
	if ($user->getGUID() == $sac->owner_guid) {
		return true;
	}
	
	return add_user_to_access_collection($user->getGUID(), $sac->acl_id);
}


/**
 * Deletes shared access collection objects if the collection is deleted.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function shared_access_deletecollection_hook($hook, $entity_type, $returnvalue, $params) {
	global $CONFIG;
	
	if ($sacs = get_entities_from_metadata('acl_id', $params['collection_id'], 'object', 'shared_access')) {
		foreach ($sacs as $sac) {
			// remove user and entity relationships
			$users = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $sac->guid, 'inverse_relationship' => TRUE, 'types' => 'object', 'subtypes' => 'user', 'limit' => 9999));
			
			foreach ($users as $user) {
				remove_entity_relationship($user->getGUID(), 'shared_access_member', $sac->getGUID());
			}
			
			$users = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_invitation', 'relationship_guid' => $sac->guid, 'inverse_relationship' => TRUE, 'types' => 'object', 'subtypes' => 'user', 'limit' => 9999));

			foreach ($users as $user) {
				remove_entity_relationship($user->getGUID(), 'shared_access_invitation', $sac->getGUID());
			}
			
			// revert all entities with this access level to ACCESS_PRIVATE
			// there is absolutely no reason to do this through the API.
			//@todo should this be site default?
//			$entities = get_entities_from_access_id($params['collection_id'], null, null, null, 99999);
//			foreach ($entities as $entity) {
//				$entity->access_id = ACCESS_PRIVATE;
//			}
			$q = "UPDATE {$CONFIG->dbprefix}entities 
				SET access_id = " . ACCESS_PRIVATE . "
				WHERE access_id = {$sac->acl_id}";
			update_data($q);
			
			$sac->delete();
		}
	}
}

/**
 * Sends out an invitation when a user is invited to a shared access collection.
 * 
 * @param $event
 * @param $object_type
 * @param $object
 * @return unknown_type
 */
function shared_access_invite_user_hook($event, $object_type, $object) {
	global $CONFIG;
	
	$user = get_entity($object->guid_one);
	$sac = get_entity($object->guid_two);
	$owner = get_entity($sac->owner_guid);
	
	return notify_user($user->getGUID(), $CONFIG->site->getGUID(),
		sprintf(elgg_echo('shared_access:request_join_subject'), $owner->name, $sac->title), 
		sprintf(elgg_echo('shared_access:request_join_body'), $user->name, $sac->title, "{$CONFIG->url}pg/shared_access/manage"),
		NULL);
}

/**
 * Creates an object to store data for a shared access list.
 * Sends out invitations for those to be added
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function shared_access_write_acl_plugin_hook($hook, $entity_type, $returnvalue, $params) {
	global $CONFIG;
 
	$user = get_loggedin_user();
	$shared_str = $user->shared_access_memberships;
	
	if ($user = get_loggedin_user() 
		AND $sacs = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $user->getGUID(), 'inverse_relationship' => FALSE, 'types' => 'object', 'subtypes' => 'shared_access', 'limit' => 9999))
	) {
		foreach ($sacs as $sac) {
			//$returnvalue[$sac->acl_id] = elgg_echo('shared_access:shared') . ': ' . $sac->title . ' (by ' . get_entity($sac->owner_guid)->name . ')';
			$returnvalue[$sac->acl_id] = elgg_echo('shared_access:shared') . ': ' . $sac->title;
		}
	}
	
	return $returnvalue;
}

/**
 * 
 * 
 * @param $user_guid
 * @param $collection_id
 * 
 * @return bool on success
 */
function shared_access_remove_user_hook($user_guid, $collection_id) {
	if (!$user = get_user($user_guid) OR !$collection = get_access_collection($collection_id)) {
		return false;
	}
	
	$type = ($invitation) ? 'shared_access_invitations' : 'shared_access_memberships';
	
	if ($shared_str = $user->$type AND !empty($shared_str)) {
		$shared = explode(',', $shared_str);
		
		// remove the entry for the collection id
		array_splice($shared, array_search($collection_id, $shared), 1);
		return $user->$type = implode(',', $shared);
	} else {
		// didn't exist, so don't need to remove.
		return true;
	}
}

register_elgg_event_handler('init', 'system', 'shared_access_init');
