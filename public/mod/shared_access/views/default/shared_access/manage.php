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

$user = get_loggedin_user();
$invitations = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_invitation', 'relationship_guid' => $user->getGUID(), 'limit' => 9999));

foreach ($invitations as $sac) {
	echo elgg_view('shared_access/invitation', array('sac' => $sac));
}