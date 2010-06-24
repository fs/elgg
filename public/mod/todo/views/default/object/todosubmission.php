<?php
	/**
	 * Todo Submission Entity View
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Check for valid entity
	if (isset($vars['entity']) && $vars['entity'] instanceof ElggObject) {
				
		$url = $vars['entity']->getURL();
		$owner = $vars['entity']->getOwnerEntity();
		$canedit = $vars['entity']->canEdit();
		$title = $vars['entity']->title;
		$todo = get_entity($vars['entity']->todo_guid);
		$contents = unserialize($vars['entity']->content);
		
		$assignee_label = elgg_echo('todo:label:assignee');
		$assignee_content = $owner->name;
		
		$todo_title_label = elgg_echo('todo:label:todo');
		$todo_title_content = elgg_view('output/url', array('href' => $todo->getURL(), 'text' => $todo->title));
		
		$date_label = elgg_echo('todo:label:datecompleted');
		$date_content =  date("F j, Y", $vars['entity']->time_created);
		
		if ($contents) {
			$work_submitted_label = elgg_echo('todo:label:worksubmitted');
		
			foreach ($contents as $content) {
				$guid = (int)$content;
				if (is_int($guid) && $entity = get_entity($guid)) {
					$href = $entity->getURL();
					$text = $entity->title;
				} else {
					$href = $text = $content;
				}
				$work_submitted_content .= "<li>" . elgg_view('output/url', array('href' => $href, 'text' => $text)). "</li>";
			}
		}
		
		if ($moreinfo_content = $vars['entity']->description) {
			$moreinfo_label = elgg_echo('todo:label:moreinfo');
		}
		
		
		// Content
		$strapline = sprintf(elgg_echo("todo:strapline"), date("F j, Y",$vars['entity']->time_created));
		$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> ";
		$strapline .= sprintf(elgg_echo("comments")) . " (" . elgg_count_comments($vars['entity']) . ")";
		
		if ($canedit) {
				$controls .= elgg_view("output/confirmlink", 
										array(
											'href' => $vars['url'] . "action/todo/deletesubmission?submission_guid=" . $vars['entity']->getGUID(),
											'text' => elgg_echo('delete'),
											'confirm' => elgg_echo('deleteconfirm'),
										)) . "&nbsp;&nbsp;&nbsp;";
										
		}
		
		$info = <<<EOT
				<div class='contentWrapper singleview'>
					<div class='todo'>
						<div class='assignee'>
							<label>$assignee_label</label><br />
							$assignee_content
						</div><br />
						<div class='todo_info'>
							<label>$todo_title_label</label><br />
							$todo_title_content
						</div><br />
						<div>
							<label>$date_label</label><br />
							$date_content
						</div><br />
						<div class='work_submitted'>
							<label>$work_submitted_label</label><br />
							<ul>
							$work_submitted_content
							</ul>
						</div><br />
						<div class='description'>
							<label>$moreinfo_label</label>
							$moreinfo_content
						</div>
						$controls
					</div>
				</div>
EOT;
		echo $info;
		
		
	} else {
		// If were here something went wrong..
		$url = 'javascript:history.go(-1);';
		$owner = $vars['user'];
		$canedit = false;
	}
?>