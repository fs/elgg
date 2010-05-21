<?php
    /**
     * Elgg Feedback plugin
     * Feedback interface for Elgg sites
     * 
     * @package Feedback
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Prashant Juvekar
     * @copyright Prashant Juvekar
     * @link http://www.linkedin.com/in/prashantjuvekar
     */

	$icon = elgg_view(
			'graphics/icon', array(
			'entity' => $vars['entity'],
			'size' => 'small',
		)
	);
	
	if ($vars['full'])
		set_input("full", true);
	
	$submit_page = "Unknown";
	if ( !empty($vars['entity']->page) ) {
		$submit_page = $CONFIG->wwwroot . $vars['entity']->page;
		$submit_page = "<a href='" . $submit_page . "'>" . $submit_page . "</a>";

		$page = "<b>".elgg_echo('feedback:list:page').": </b>" . $submit_page . "<br />";
	}

	$mood = elgg_echo ("feedback:mood:" . $vars['entity']->mood);
	$about = elgg_echo ("feedback:about:" . $vars['entity']->about);
	$status = elgg_echo("feedback:status:" . $vars['entity']->status);
	$id = $vars['entity']->id;
	$guid = $vars['entity']->guid;
	
	$time_created = $vars['entity']->time_created;
	$owner_guid = $vars['entity']->owner_guid;
	$owner = get_entity($owner_guid);
	
	// Admin only content
	if (isadminloggedin()) {
		$controls .= elgg_view("output/confirmlink",array('onclick' => 'return false;', 'href' => $vars['url'] . 'action/feedback/delete?guid=' . $vars['entity']->guid, 'text' => elgg_echo('delete'), 'confirm' => elgg_echo('deleteconfirm'),));
		$status = elgg_view('feedback/forms/setstatus', $vars);
	}
	
	// Gross hack to fix old metadata, can be removed eventually!
	$metadata = get_metadata_byname($guid, 'status');
	
	if ($metadata->value == "resolvedaction" || $metadata->value == "resolvednoaction") {
		$metadata->value = "resolved";
		$metadata->save();
	}
	
	$comments = $vars['entity']->getAnnotations('comment');
	
	$comments_content = '';	
	
	$likes	= 0;
	$dislikes = 0;
	$comment_count = 0;
			
	// Build comments (if any) and count respective votes
	foreach($comments as $comment) {
		$comments_content .= elgg_view_annotation($comment);
		if (is_array($comment_data = unserialize($comment->value))) {
			if ($comment_data['feedbackvote'] == 1) 
				$likes++; 
			else if ($comment_data['feedbackvote'] == 0) {
				$dislikes++;
			}			
		}	
		$comment_count++;
	}
	
	// Edit form div
	$edit_form .= "<div id='feedback_edit_$guid' style='display: " .  (($vars['full']) ? "block" : "none") . "; width: 100%;'>";
	$edit_form .= $comments_content;
	
	// Display edit form
	$edit_form .= elgg_view('feedback/forms/edit', $vars);

	$edit_form .= "</div>";
	
	//$info .= $page;
	
	$info .= "<div id='feedbackinfo'>";
	$info .= "<a href='{$vars[url]}pg/feedback/view/{$vars[entity]->getGUID()}/'>{$vars[entity]->title}</a><br />";
	
	if ($owner) {
		$info .= "<p class='listingstrapline'>".sprintf(elgg_echo("feedback:strapline"),
				"<a href=\"" . $owner->getURL() . "\">" . $owner->name ."</a>",
					date("F j, Y",$time_created)
		) . "</p>";
	} else {
		$info .= "<p class='listingstrapline'>".sprintf(elgg_echo("feedback:strapline"),
				 	$id,
					date("F j, Y",$time_created)
		) . "</p>";
	}
	
	$info .= "<a href='#' onclick=\"feedback_toggle_comments($guid); return false;\">Comments ($comment_count)&nbsp;&nbsp;&nbsp;&nbsp; Feelin' It! ($likes)&nbsp;&nbsp;&nbsp;&nbsp; Not Feelin' It! ($dislikes)&nbsp;<span id='arrow_$guid' style='font-size: 14px;'>" . (($vars['full']) ? "&uarr;" : "&darr;"). "</span></a><br /><br />";
	
	$info .= "<div style='float:left;width:30%;font-size:90%;'><b>".elgg_echo('feedback:status').": </b>$status</div>";
	$info .= "<div style='float:left;width:25%;font-size:90%;'><b>".elgg_echo('feedback:list:mood').": </b>$mood</div>";
	$info .= "<div style='float:left;width:20%;font-size:90%;'><b>".elgg_echo('feedback:list:about').": </b>$about</div>";
	$info .= "<div style='float:left;width:20%;text-align:right;'>$controls</div><br />";		
	
	//$info .= "<b>".elgg_echo('feedback:list:from').": </b>$id<br />";
	$info .= "<br />" . nl2br($vars['entity']->txt) . "<br /><br />";
	$info .= "</div>";
	
	if ($vars['full'])
		echo elgg_view_listing($icon,$info) . $edit_form;
	else 
		echo elgg_view('feedback/feedback_entity_listing',array('icon' => $icon, 'info' => $info, 'comments' => $edit_form));
?>
