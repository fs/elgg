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

$user = $vars['user'];

$body = "<div class='content_header_title'>".elgg_view_title(elgg_echo('shared_access:shared_access'))."</div>";

$new_collection_link = "<div class='content_header_options'><a href=\"{$vars['url']}pg/shared_access/new\" class='action_button'>" . elgg_echo('shared_access:new_collection') . "</a></div>";

// grab invitations
$sacs = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_invitation', 'relationship_guid' => $user->getGUID() ));

$sacs_html = '';
foreach ($sacs as $sac) {
	$sacs_html .= elgg_view('shared_access/collection', array('sac'=>$sac, 'user'=>$user, 'invitation'=>true));
}

// grab all sacs
$sacs = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $user->getGUID() ));
foreach ($sacs as $sac) {
	$sacs_html .= elgg_view('shared_access/collection', array('sac'=>$sac, 'user'=>$user));
}

if ($sacs_html == '') {
	$sacs_html = "<p class='margin_top'>".elgg_echo('shared_access:no_shared_access_collections')."</p>";
}

$body = "<div id='content_header' class='clearfloat'>" . $body . $new_collection_link . "</div>" . $sacs_html;

$boxes = elgg_view('shared_access/sidebar/info');
echo elgg_view_layout('one_column_with_sidebar', $body, $boxes);
