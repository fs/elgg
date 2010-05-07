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

if (!$guid = get_input('guid', false) OR !($sac = get_entity($guid) AND $sac->getSubtype() == 'shared_access')) {
	register_error(elgg_echo('shared_access:errordeclining'));
} else {
	if (remove_entity_relationship($user->getGUID(), 'shared_access_invitation', $guid)) {
		system_message(sprintf(elgg_echo('shared_access:declined'), $sac->title));
	} else {
		register_error(elgg_echo('shared_access:errordeclining'));
	}
}

forward($_SERVER['HTTP_REFERER']);