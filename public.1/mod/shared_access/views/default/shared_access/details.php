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
$sac = $vars['sac'];

$body = elgg_view_title(elgg_echo('shared_access:shared_access'));

// show a standard top desc if a member
if (check_entity_relationship($user->getGUID(), 'shared_access_member', $sac->getGUID())) {

// show an invitation if only invited.
} elseif (check_entity_relationship($user->getGUID(), 'shared_access_invitation', $sac->getGUID())) {
	
}

// output members
$members = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $sac->getGUID(), 'inverse_relationship' => TRUE));

$content = '<p><label>' . elgg_echo('shared_access:collection_members') . '</label></p>';

foreach ($members as $member) {
	$content .= "<div class='member_icon'><a href=\"{$member->getURL()}\">" 
		. elgg_view('profile/icon', array('entity' => $member, 'size' => 'small', 'override' => 'true'))
		. '</a></div>';
}

if (get_input('ajax') == true) {
	echo $content;
} else {
	echo elgg_view_layout('one_column_with_sidebar', $body . $content, $boxes);
}