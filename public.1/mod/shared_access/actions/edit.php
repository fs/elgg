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

gatekeeper();
$user = get_loggedin_user();

if (!$name = get_input('name')) {
	register_error(elgg_echo('shared_access:missing_required_parameter'));
	forward($var['url'] . 'pg/shared_access/manage');
}

if ($guid = get_input('sac_guid') AND !($sac = get_entity($guid) AND $sac->getSubtype() == 'shared_access')) {
	register_error(elgg_echo('shared_access:unknown_collection'));
	forward($var['url'] . 'pg/shared_access/manage');
}

// create a new sac.
if (!$guid) {
	// create a new ACL for the access dropdown.
	$acl_name = elgg_echo('shared_access:shared') . ': ' . $name;
	$acl_id = create_access_collection($acl_name, $user->getGUID());
	
	// create a new "container" object.
	$sac = new ElggObject();
	$sac->title = $name;
	$sac->description = $desc;
	$sac->subtype = 'shared_access';
	// this has to be access_public.
	$sac->access_id = ACCESS_PUBLIC;
	$sac->acl_id = $acl_id;
	$sac->save();
	
	// add the creator as a member
	add_entity_relationship(get_loggedin_user()->getGUID(), 'shared_access_member', $sac->getGUID());
} else {
	$sac->title = $name;
	$sac->description = $desc;
	$sac->save();
}

// sort out the members
$sent_members = get_input('members');
$cur_members = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $sac->getGUID(), 'inverse_relationship' => TRUE, 'limit' => 9999));

// remove ones that aren't in the new array.
foreach ($cur_members as $member) {
	$guid = $member->getGUID();
	if (!in_array($guid, $sent_members)) {
		remove_entity_relationship($guid, 'shared_access_member', $sac->getGUID());
	}
	$key = array_search($guid, $sent_members);
	if ($key !== false) {
		unset($sent_members[$key]);
	}
}

// add ones that are in the new array.
foreach ($sent_members as $guid) {
	add_entity_relationship($guid, 'shared_access_invitation', $sac->getGUID());
}

system_message(elgg_echo('shared_access:collection_saved'));

forward($CONFIG->site->url . 'pg/shared_access');