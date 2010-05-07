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

//@todo make this link to a full list of members
// possible add live search to it.
$members = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $vars['sac']->getGUID(), 'inverse_relationship' => TRUE));

$members_html = '';
foreach ($members as $member) {
	$members_html .= "<div class='member_icon'><a href=\"{$member->getURL()}\">" 
		. elgg_view('profile/icon', array('entity' => $member, 'size' => 'tiny', 'override' => 'true'))
		. '</a></div>';
}
?>
<h3><?php echo elgg_echo('shared_access:members'); ?></h3>
<?php echo $members_html; ?>
