<?php
/**
 * Elgg video plugin uploader
 */

global $CONFIG;
$video = $vars['entity'];
if($video){
	$video_guid = $video->guid;
	$title = $video->title;
	$desc = $video->description;
	$access_id = $video->access_id;
	$tags = $video->tags;
	$permission = $video->permission;
	
	//set required variables
	$video_title_label = elgg_echo('video:title');
	$video_title = elgg_view("input/text", array("internalname" => "title","value" => $title));
	$video_desc_label = elgg_echo('video:desc');
	$video_desc = elgg_view("input/longtext",array("internalname" => "description","value" => $desc));
	$video_tags_label = elgg_echo('video:tags');
	$video_tags = elgg_view("input/tags", array("internalname" => "tags","value" => $tags));
	$video_access_label = elgg_echo('access');
	$video_access = elgg_view('input/access', array('internalname' => "access_id",'value' => $access_id));
	if (isset($vars['container_guid']))
		$container_guid = elgg_view('input/hidden',  array('internalname' => "container_guid",'value' => $vars['container_guid']));
	else
		$container_guid = '';
			
	$video_permission = elgg_echo('video:permission') . ":<br /> " . elgg_view("input/radio",array(
									"internalname" => "permission",
									"value" => $permission,
									'options' => array(
														elgg_echo('video:yes') => 'yes',
														elgg_echo('video:no') => 'no',
													   ),
													));
	
	$video_guid = elgg_view('input/hidden',  array('internalname' => "video",'value' => $video_guid));
	$submit = elgg_view('input/submit',  array('internalname' => "submit",'value' => elgg_echo("save")));

$form_body .= <<<EOT
<div class='ContentWrapper Welcome'>
	<p><label>{$video_title_label}:</label><br /> {$video_title}</p>
	<p class='longtext_editarea'><label>{$video_desc_label}:</label><br />  {$video_desc}</p>
	<p class='longtext_nextfield'><label>{$video_tags_label}:</label><br />  {$video_tags}</p>
	<p><label>{$video_access_label}:</label> {$video_access}</p>
	{$container_guid}
	{$video_permission}
	{$video_guid}
	{$submit}
</div>
EOT;

	//display the form
	echo elgg_view('input/form', array('action' => "{$vars['url']}action/video/edit", 'body' => $form_body, 'internalid' => 'videoEditForm'));

}else{
	echo elgg_echo('video:novideo');
}