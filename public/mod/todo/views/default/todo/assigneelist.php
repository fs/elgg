<?php
	/**
	 * Todo Assignee List, a nice formatted list assignees
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * @uses $vars['assignees'] - users/groups entities
	 * 
	 */

	$assignees = $vars['assignees'];
	
	
	$content = "<div class='todo'><table class='assignee_table'>";
	
	$count = 0;
	foreach ($assignees as $assignee) {
		$class = 'assignee';
		if ($count % 2 == 0) {
			$class .= ' alt'; 
		}
		$content .= "<tr><td class='$class'>";
		$content .= $assignee->name;		
		$content .= '</td></tr>';
		$count++;
	}
	
	
	$content .= '</table></div>';

	echo $content;
	
?>

