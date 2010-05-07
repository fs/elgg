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

if (!$collection_id = get_input('collection_id', false) OR !$collection = get_access_collection($collection_id)) {
	register_error(elgg_echo('shared_access:errorleaving'));
} else {
	if (shared_access_remove_user_from_shared_collection(get_loggedin_user()->getGUID(), $collection->id, 'shared_access_membership')) {
		system_message(sprintf(elgg_echo('shared_access:left'), $collection->name));
	} else {
		register_error(elgg_echo('shared_access:errorleaving'));
	}
}

forward($_SERVER['HTTP_REFERER']);