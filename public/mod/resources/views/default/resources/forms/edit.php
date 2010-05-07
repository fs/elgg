<?php
	/**
	 * Resource Request create/edit form 
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Check if we've got an entity, if so, we're editing.
	if (isset($vars['entity'])) {
		$action 		= "resources/edit";
		$title 		 	= $vars['entity']->title;
		$description 	= $vars['entity']->description;
		$type 			= $vars['entity']->type;
		$tags 			= $vars['entity']->tags;	
		
		$container_hidden = elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid']));
		$entity_hidden  = elgg_view('input/hidden', array('internalname' => 'request_guid', 'value' => $vars['entity']->getGUID()));
		
		
	} else {
	// No entity, creating new one
		$action = "resources/create";
		$title = "";
		$description = "";
		$type = "";
		$tags = "";
		
		$container_hidden = "";
		$entity_hidden = "";
	}
	
	if (empty($description)) {
		$description = $vars['user']->resource_request_description;
		if (!empty($description)) {
			$title = $vars['user']->resource_request_title;
			$tags = $vars['user']->resource_request_tags;
			$type = $vars['user']->resource_request_type;
		}
	}
	
	
	// Labels/Input
	$title_label = elgg_echo('title');
	$title_input = elgg_view('input/text', array('internalname' => 'request_title', 'value' => $title));
	
	$description_label = elgg_echo("description");
	$description_input = elgg_view("input/longtext", array('internalname' => 'request_description', 'value' => $description));
	
	$type_label = elgg_echo('resources:label:types');
	$type_input = elgg_view('input/pulldown', array('internalname' => 'request_type', 'options_values' => get_resource_types(), 'value' => $type));
	
	$tag_label = elgg_echo('tags');
    $tag_input = elgg_view('input/tags', array('internalname' => 'request_tags', 'value' => $tags));
	
	$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('submit')));
	
	
	
	// Build Form Body
	$form_body = <<<EOT
	
	<div class='contentWrapper'>
		<p>
			<label>$title_label</label><br />
	        $title_input
		</p>
		<p>
			<label>$description_label</label><br />
	        $description_input
		</p>
		<p>
			<label>$type_label</label><br />
	        $type_input
		</p>
		<p>
			<label>$tag_label</label><br />
	        $tag_input
		</p>
		<p>
			$submit_input
			$container_hidden
			$entity_hidden
		</p>
	</div>
	
EOT;

	echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'resource_request_post_forms'));
?>