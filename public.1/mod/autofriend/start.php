<?php
/**
 * Elgg autofriend 
 *
 * @package ElggAutoFriend
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright Think Global School 2009-2010
 * @link http://www.thinkglobalschool.com
 */

function autofriend_init() {
	// Register an event handler to catch the creation of new users
	register_elgg_event_handler('create', 'user', 'autofriend_event',501);
}



/** 
 * Autofriend event 
 * 
 * @param $event
 * @param $object_type
 * @param $object
 */ 
function autofriend_event($event, $object_type, $object) {	
	// Get site members
	$members = get_site_members($object['site_guid'], 0);
	if (($members) && is_array($members)) {
		foreach ($members as $member) {
			if ($object instanceof ElggUser) {
				// Add newly created user to each members friends
				$member->addFriend($object->getGUID());
				// Add member to new user's friends 
				$object->addFriend($member->getGUID());
			}
		}		
	}
}


// Register event 
register_elgg_event_handler('init', 'system', 'autofriend_init');

?>