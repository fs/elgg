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
$sac = $vars['sac'];
$owner = get_entity($sac->owner_guid);

if ($vars['invitation']) {
	$invite = '
	<div class="shared_access_invite_notice"><span>' . elgg_echo('shared_access:invited_to_sac') . "
		<a href=\"" . elgg_add_action_tokens_to_url("{$vars['url']}action/shared_access/join?guid={$sac->getGUID()}") . "\">" . elgg_echo('shared_access:join') . "</a> |
		<a href=\"" . elgg_add_action_tokens_to_url("{$vars['url']}action/shared_access/decline?guid={$sac->getGUID()}") . "\">" . elgg_echo('shared_access:decline') . "</a>
	</span></div>";
	$request = 'requested';
} else {
	$invite  = '';
	$request = '';
}

$members_count = elgg_get_entities_from_relationship(array('relationship' => 'shared_access_member', 'relationship_guid' => $sac->getGUID(), 'inverse_relationship' => TRUE, 'count' => true));

if ($members_count == 1) {
	$members_str = sprintf(elgg_echo('shared_access:member_count_singular'), $members_count);
} else {
	$members_str = sprintf(elgg_echo('shared_access:member_count'), $members_count);
}

$owner_html = "<div class='shared_access_owner_icon'><a href=\"{$owner->getURL()}\">"
		.elgg_view('profile/icon', array('entity' => $owner, 'size' => 'tiny', 'override' => 'true'))
		.'</a></div>';

if ($owner->getGUID() == $user->getGUID() || isadminloggedin()) {
	$edit_html	= "<div class='delete_button'><a onClick=\"return confirm('" .
		addslashes(elgg_echo('question:areyousure')) .
		"');\" href=\"" . elgg_add_action_tokens_to_url("{$vars['url']}action/shared_access/delete?collection={$sac->acl_id}") . "\">" . elgg_echo('delete') . "</a></div>";

	$edit_html .= "<div class='edit_collection'><a class='shared_access_edit_link ajax_content' href=\"{$vars['url']}pg/shared_access/{$sac->getGUID()}/edit\">"
		.elgg_echo('edit') . '</a></div>';

	$edit_html .= "<div class='edit_collection'><a class='shared_access_details_link ajax_content' href=\"{$vars['url']}pg/shared_access/{$sac->getGUID()}/edit\">".$members_str . '</a></div>';

	// add loaded content for admins
	if ($vars['opened']) {
		$loaded_content = elgg_view('shared_access/edit', array('sac' => $sac));
	}
} else {
	$edit_html = "<div class='leave_collection'><a onClick=\"return confirm('" .
		addslashes(elgg_echo('question:areyousure')) .
		"');\" href=\"".elgg_add_action_tokens_to_url("{$vars['url']}action/shared_access/leave?guid={$sac->getGUID()}") . "\">" . elgg_echo('shared_access:leave') . "</a></div>";

	$edit_html .= "<a class='shared_access_details_link ajax_content' href=\"{$vars['url']}pg/shared_access/{$sac->getGUID()}/details\">" . $members_str . "</a>";

	// add loaded content for admins
	if ($vars['opened']) {
		$loaded_content = elgg_view('shared_access/details', array('sac' => $sac));
	}
}

echo <<<___END
<div id="sac-{$sac->getGUID()}" class="shared_access_collection {$request}">
	$owner_html
	$edit_html
	<h2 class="shared_access_name"><a href="{$vars['url']}/pg/shared_access/riverdashboard/{$sac->getGUID()}">{$sac->title}</a></h2>
	$invite

	<div class="ajax_content_target clearfloat">$loaded_content</div>
</div>

___END;

?>