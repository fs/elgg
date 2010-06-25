<?php
	/**
	 * Todo Status View, displays status of todo's submissions
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * @uses $vars
	 * 
	 */

	$todo = $vars['entity'];
	
	$assignees = get_todo_assignees($todo->getGUID());
	
	$content = "<div class='todo'>
					<table class='status_table'>
						<tr>
							<th>" . elgg_echo('todo:label:assignee') . "</th>
							<th>" . elgg_echo('todo:label:status') . "</th>
							<th>" . elgg_echo('todo:label:datecompleted') . "</th>
							<th>" . elgg_echo('todo:label:submission') . "</th>
						</tr>";
	
	$count = 0;
	foreach ($assignees as $assignee) {
		$class = '';
		if ($count % 2 == 0) {
			$class .= ' alt'; 
		}
		
		$status = '-';
		$date = '-';
		$url = '-';
		
		if ($submission = has_user_submitted($assignee->guid, $vars['entity']->getGUID())) {
			$status = 'Complete';
			$date = date("F j, Y", $submission->time_created);
			$url = "<a href='{$submission->getURL()}'>View</a>";
		}
		
		$content .= '<tr>';
		$content .= 	"<td class='$class'>$assignee->name</td>";
		$content .= 	"<td class='$class'>$status</td>";
		$content .= 	"<td class='$class'>$date</td>";
		$content .= 	"<td class='$class'>$url</td>";
		$content .= '</tr>';
		$count++;
	}
	
	
	$content .= '</table></div>';

	echo $content;
	
?>

