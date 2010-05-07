<?php
/**
 * Elgg shared access plugin language pack
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

$english = array(
	/** Generic **/
	'shared_access:shared_access' => 'Channels',
	'shared_access:shared' => 'Channel',
	'shared_access:members' => 'Members',
	'shared_access:shared_access_name' => 'Name',
	'shared_access:shared_access_description' => 'Description',
	'shared_access:no_shared_content' => 'No shared content found.',
	'shared_access:back_home' => 'Back to all channels',
	'shared_access:notifications' => 'Notification',
	'shared_access:post_to_wire' => 'Post to channel wire',
	'shared_access:post_to' => 'Post to',
	'shared_access:no_shared_access_collections' => 'No channels',
	'shared_access:collections' => 'Channels',
	'shared_access:info' => 'Channels are a lightweight way to filter content based on an access level.',

	/** Admin **/
	'shared_access:new_collection' => 'Add a new channel',
	'shared_access:member_count_singular' => '%s member',
	'shared_access:member_count' => '%s members',
	'shared_access:collection_members' => 'Members',
	'shared_access:invite_users' => 'Invite Users',
	'shared_access:invited_to_sac' => 'You have been invited to join this channel: ',
	'shared_access:declined' => 'You have declined the invitation.',
	'shared_access:joined' => 'You have joined the invitation.',
	'shared_access:collection_saved' => 'Channel saved.',
	'shared_access:decline' => 'Decline',
	'shared_access:join' => 'Join',

	'shared_access:show_in_tools_menu' => 'Show link in tools menu?',

	/** Emails **/
	'shared_access:request_join_subject' => '%s wants you to join %s!',
	'shared_access:request_join_body' => 'Hi %s,

You have been invited to share content with the "%s" channel.  Click below to accept the invitation:

%s",',

);
				
add_translation("en",$english);
?>