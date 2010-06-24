<?php
	/**
	 * Todo listing view
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 	
	 */
	
	// Determine how we are going to view this todo
	$user = get_loggedin_user();
	$is_owner = $vars['entity']->canEdit();
	$is_assignee = is_todo_assignee($vars['entity']->getGUID(), $user->getGUID());

	$url = $vars['entity']->getURL();
	$owner = $vars['entity']->getOwnerEntity();
	$canedit = $is_owner; 
	$title = $vars['entity']->title;
	$due_date = is_int($vars['entity']->due_date) ? date("F j, Y", $vars['entity']->due_date) : $vars['entity']->due_date;

	// Content
	$icon = elgg_view("graphics/icon", array('entity' => $vars['entity'],'size' => 'small'));

	$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));

	$strapline = sprintf(elgg_echo("todo:strapline"), $due_date);
	$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> ";
	$strapline .= sprintf(elgg_echo("comments")) . " (" . elgg_count_comments($vars['entity']) . ")";

	if ($canedit) {

			$controls .= elgg_view("output/confirmlink", 
									array(
										'href' => $vars['url'] . "action/todo/deletetodo?todo_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('delete'),
										'confirm' => elgg_echo('deleteconfirm'),
									)) . "&nbsp;&nbsp;&nbsp;";

			$controls .= "<a href={$vars['url']}pg/todo/edittodo/{$vars['entity']->getGUID()}>" . elgg_echo("edit") . "</a>";
	}

	if ($tags) {
		$tags = "<p class='listingtags'>
					" . $tags . "
				</p>";
	} else {
		$tags = '<p></p>';
	}

	$info = <<<EOT
		<div class='todo'>
			<p>
				<b><a href='$url'>$title</a></b>
			</p>
			<p class='listingstrapline'>
				$strapline
			</p>
			$tags
			<p class='controls'>
				$controls
			</p>
		</div>
EOT;

	echo elgg_view_listing($icon, $info);
?>