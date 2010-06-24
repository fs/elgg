<?php
	/**
	 * Todo Full View
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	$user = get_loggedin_user();
	
	$page_owner = page_owner_entity();
		
	// Determine how we are going to view this todo
	$is_owner = $vars['entity']->canEdit();
	$is_assignee = is_todo_assignee($vars['entity']->getGUID(), $user->getGUID());
	
	$url = $vars['entity']->getURL();
	$owner = $vars['entity']->getOwnerEntity();
	$title = $vars['entity']->title;
	$return_required = $vars['entity']->return_required;
	$due_date = is_int($vars['entity']->due_date) ? date("F j, Y", $vars['entity']->due_date) : $vars['entity']->due_date;
		
	// Start putting content together
	$description_label = elgg_echo("todo:label:description");
	$description_content = elgg_view('output/longtext', array('value' => $vars['entity']->description));
	
	$duedate_label = elgg_echo("todo:label:duedate");
	$duedate_content = elgg_view('output/longtext', array('value' => $due_date));
	
	//$assignees_label = elgg_echo("todo:label:assignees");
	//$assignees_content = elgg_view('todo/assigneelist', array('assignees' => get_todo_assignees($vars['entity']->getGUID())));
	
	$return_label = elgg_echo("todo:label:returnrequired");
	$return_content = ($return_required ? "Yes": 'No' );
	
	$status_label = elgg_echo("todo:label:status");
	
	$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
				
	$strapline = sprintf(elgg_echo("todo:strapline"), $due_date);
	$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> ";
	$strapline .= sprintf(elgg_echo("comments")) . " (" . elgg_count_comments($vars['entity']) . ")";

	$submission_form = elgg_view('todo/forms/submission', $vars);
	
	// Optional functionality
	if (TODO_RUBRIC_ENABLED && $rubric = get_entity($vars['entity']->rubric_guid)) {
		$controls .= "<a href='{$rubric->getURL()}'>" . elgg_echo('todo:label:viewrubric') . "</a>";
	}
	
	// Set status content for viewers (will be changed, updated depending on how this todo is viewed)
	if (have_assignees_completed_todo($vars['entity']->getGUID())) {
		$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
	} else {
		$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
	}
	
	// Assignee only content
	if ($is_assignee) {
		
		if ($submission = has_user_submitted($user->getGUID(), $vars['entity']->getGUID())) {
			$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
			$controls .= "&nbsp;&nbsp;&nbsp;<a id='view_submission' href='" . $submission->getURL() . "'>" . elgg_echo("todo:label:viewsubmission") . "</a>";
		} else {
			$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
			// If we need to return something for this todo, the complete link will point to the submission form
			if ($vars['entity']->return_required) {
				$controls .= "&nbsp;&nbsp;&nbsp;<a id='create_submission' href='#'>" . elgg_echo("todo:label:completetodo") . "</a>";
			} else {
				// No return required, link to createsubmissionaction and create blank submission
				$controls .= "&nbsp;&nbsp;&nbsp;<a id='create_blank_submission' href='#'>" . elgg_echo("todo:label:completetodo") . "</a>";
			}
		}
		
	} 
	
	// Owner only Content
	if ($is_owner) {
			$status_content .= elgg_view('todo/todostatus', $vars);
			$controls .= "&nbsp;&nbsp;&nbsp;<a href='{$vars['url']}pg/todo/edittodo/{$vars['entity']->getGUID()}'>" . elgg_echo("edit") . "</a>";
			$controls .= "&nbsp;&nbsp;&nbsp;" . elgg_view("output/confirmlink", 
									array(
										'href' => $vars['url'] . "action/todo/deletetodo?todo_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('delete'),
										'confirm' => elgg_echo('deleteconfirm'),
									));
	}

	if ($tags) {
		$tags = "<p class='fulltags'>
					" . $tags . "
				</p>";
	} else {
		$tags = '<p></p>';
	}
	
	// AJAX Endpoint for submissions
	$submission_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot . 'mod/todo/actions/createsubmission.php');
	
	// JS
	$script = <<<EOT
	<script type='text/javascript'>
	$(function() {
		/** SET UP DIALOG POPUP **/
		$('#submission_dialog').dialog({
							autoOpen: false,
							width: 725,
							modal: true,
							open: function(event, ui) { 
								$(".ui-dialog-titlebar-close").hide(); 
								if (typeof(tinyMCE) !== 'undefined') {
									tinyMCE.execCommand('mceAddControl', false, 'submission_description');
								}
							},
							beforeclose: function(event, ui) {
								if (typeof(tinyMCE) !== 'undefined') {
						    		tinyMCE.execCommand('mceRemoveControl', false, 'submission_description');
								}
						    },
							buttons: {
								"X": function() { 
									$(this).dialog("close"); 
								} 
							}
						});
		
		$("a#create_submission").click(
			function() {
				$("#submission_dialog").dialog("open");
				return false;
			}
		);
		
		$("a#create_blank_submission").click(
			function() {
				sendSubmission();
				setTimeout ('window.location.reload()', 800);
				return false;
			}
		);
		
		$("form#todo_submission_form").submit(
			function() {
				/** May not be tinyMCE **/
				if (typeof(tinyMCE) !== 'undefined') {
					var comment = tinyMCE.get('submission_description').getContent();
					$("textarea#submission_description").val(comment);
				}
				else {
					var comment = $("textarea#submission_description").val();
					
				}
				
				var content = $("#submission_content").val();
					
				if (content) {
					sendSubmission();
					if (typeof(tinyMCE) !== 'undefined') {
			    		tinyMCE.execCommand('mceRemoveControl', false, 'submission_description');
					}
					$("#submission_dialog").dialog("close");
					setTimeout ('window.location.reload()', 800);
			
				} else {
					// error
					$("#submission_error_message").show().html("** Content is required");
				}
				return false;
			}
		);
		
		function sendSubmission() {
			data = $("form#todo_submission_form").serializeArray();
			$.ajax({
				url: "$submission_url",
				type: "POST",
				data: data,
				cache: false, 
				dataType: "html", 
				error: function() {
					//alert('There was an error');	
				},
				success: function(data) {
				}
				
			});
		}			
	});
	</script>
	
EOT;
	
	// Put content together
	$info = <<<EOT
			<div class='contentWrapper singleview'>
				<div class='todo'>
					<div class='todo_header'>
						<div class='todo_header_title'><h2><a href='$url'>$title</a></h2></div>
						<div class='todo_header_controls'>
							$controls
						</div>
						<div style='clear:both;'></div>
					</div>
					<div class='strapline'>
						$strapline
					</div>
					$tags
					<div class='clearfloat'></div>
					<div class='description'>
						<label>$description_label</label><br />
						$description_content
					</div>
					<div>
						<label>$duedate_label</label><br />
						$duedate_content
					</div>
					<!--<div>
						<label>$assignees_label</label><br />
						$assignees_content
					</div><br />-->
					<div>
						<label>$return_label</label><br />
						$return_content
					</div><br />
					<div>
						<label>$status_label</label><br />
						$status_content
					</div><br />
				</div>
			</div>
			<div id="submission_dialog" style="display: none;" >$submission_form</div>
EOT;
	
	// Echo content
	echo $script . $info;
?>
