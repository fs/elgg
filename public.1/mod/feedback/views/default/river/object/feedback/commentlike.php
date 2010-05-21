<?php
	/**
	 * Feedback comment 'like' river view
	 * 
	 * @package Feedback
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);
	$url = $object->getURL();
	
	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$title .= "<a href=\"" . $object->getURL() . "\">" . $object->title .  "</a>";
	
	$string = sprintf(elgg_echo("feedback:river:comment:like"),$url, $title);
	
	
?>

<?php echo $string; ?>