<?php
	/**
	 * Resources - Update request
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Make sure we're logged in; forward to the front page if not
	gatekeeper();

	// Get input
	$entity_guid = (int) get_input('entity_guid');
	$comment_text = get_input('generic_comment');
	
	$comment_view_level = get_input('comment_view_level');
	
	if (is_array($comment_view_level)) 
		$comment_view_level = (bool)$comment_view_level[0];
	

	// Let's see if we can get an entity with the specified GUID
	$request = get_entity($entity_guid);
	if (!$request) {
		register_error(elgg_echo("generic_comment:notfound"));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$user = get_loggedin_user();
	
	if (!empty($comment_text)) {
		$annotation = create_annotation($request->guid, 
										'resource_request_comment',
										serialize(array('comment_text' => $comment_text, 'comment_view_level' => $comment_view_level)), 
										"", 
										$user->guid, 
										$request->access_id);

		// tell user annotation posted
		if (!$annotation) {
			register_error(elgg_echo("generic_comment:failure"));
			forward($_SERVER['HTTP_REFERER']);
		}

		// notify if poster wasn't owner and if the comment was 
		// specified as public
		if ($request->owner_guid != $user->guid && $comment_view_level != RESOURCE_REQUEST_COMMENT_ADMIN) {
			notify_user($request->owner_guid,
						$user->guid,
						elgg_echo('generic_comment:email:subject'),
						sprintf(
							elgg_echo('generic_comment:email:body'),
							$request->title,
							$user->name,
							$comment_text,
							$request->getURL(),
							$user->name,
							$user->getURL()
						)
					);
		}
	}
	
	// Success!
	system_message(elgg_echo("resources:success:update"));
	
	// Forward to the entity page
	forward($request->getURL());	
?>
