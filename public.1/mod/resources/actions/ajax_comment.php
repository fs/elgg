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

	// start the elgg engine
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');
	
	// must have security token 
	action_gatekeeper();

	// Get input
	$entity_guid = (int) get_input('entity_guid');
	$comment_text = get_input('comment_text');
	$user_guid = get_input('user_guid');
	$user = get_user($user_guid);
	
	// Let's see if we can get an entity with the specified GUID
	$request = get_entity($entity_guid);
	if (!$request) {
		register_error(elgg_echo("generic_comment:notfound"));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	if (!empty($comment_text)) {
		$annotation = create_annotation($request->guid, 
										'resource_request_comment',
										serialize(array('comment_text' => $comment_text, 'comment_view_level' => RESOURCE_REQUEST_COMMENT_PUBLIC)), 
										"", 
										$user_guid, 
										$request->access_id);

		// tell user annotation posted
		if (!$annotation) {
			//register_error(elgg_echo("generic_comment:failure"));
			echo false;
			die();
		}
		
		

		// notify if poster wasn't owner and if the comment was 
		// specified as public
		if ($request->owner_guid != $user_guid) {
			notify_user($request->owner_guid,
						$user_guid,
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
		
		// Success!
		//system_message(elgg_echo("resources:success:update"));

		// Forward to the entity page
		echo true;
	} else {
		echo false;
	}
?>
