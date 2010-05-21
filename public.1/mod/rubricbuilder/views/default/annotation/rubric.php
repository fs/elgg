<?php
	/**
	 * Rubric revision view
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * Some code borrowed from Pages plugin
	 */

	$annotation = $vars['annotation'];
	$entity = get_entity($annotation->entity_guid);
	
	$icon = elgg_view(
		"annotation/icon", array(
		'annotation' => $vars['annotation'],
		'size' => 'small',
	  )
	);
	
	$owner_guid = $annotation->owner_guid;
	$owner = get_entity($owner_guid);
			
	$rev = sprintf(elgg_echo('rubricbuilder:revision'), 
		friendly_time($annotation->time_created),
		
		"<a href=\"" . $owner->getURL() . "\">" . $owner->name ."</a>"
	);
	
	$link = $entity->getURL() . "?rev=" . $annotation->id;
	
	
	$revision = get_annotation($annotation->id);
	$revision = unserialize($revision->value);
	
	$title = $revision['title'];
	
	$info = <<< END
	
<div><a href="$link">{$title}</a></div>
<div>$rev</div>
END;

	echo elgg_view_listing($icon, $info);
?>