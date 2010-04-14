<?php
/**
 * Elgg video plugin uploader
 */

global $CONFIG;

//access details
$loggedin_user_access = get_default_access(get_loggedin_user());
$user_acl = get_readable_access_level($loggedin_user_access);

if(page_owner_entity() instanceof ElggGroup){
	//if in a group, set the access level to default to the group
	$access_id = page_owner_entity()->group_acl;
}else{
	$access_id = $loggedin_user_access;
}		
	
?>
<div class="ContentWrapper">
<div id="elgg_horizontal_tabbed_nav">
	<ul>
		<li class="selected">
			<a href="#" <?php echo $uploadselected; ?> onclick="javascript:$('.popup .content').load('<?php echo $vars['url'] . 'pg/embed/upload'; ?>?internalname=<?php echo $vars['internalname']; ?>'); return false"><?php echo elgg_echo('video:upload'); ?></a>
		</li>
		<li>
			<a href="#" <?php echo $embedselected; ?> onclick="javascript:$('.popup .content').load('<?php echo $vars['url'] . 'pg/embed/media'; ?>?internalname=<?php echo $vars['internalname']; ?>'); return false"><?php echo elgg_echo('video:embed'); ?></a>
		</li>
	</ul>
</div>
<?php
	
	//set required variables
	$video_upload = elgg_view("input/file",array('internalname' => "upload"));
	$video_title_label = elgg_echo('video:title');
	$video_title = elgg_view("input/text", array("internalname" => "title","value" => ''));
	$video_desc_label = elgg_echo('video:desc');
	$video_desc = elgg_view("input/longtext",array("internalname" => "description","value" => ''));
	$video_tags_label = elgg_echo('video:tags');
	$video_tags = elgg_view("input/tags", array("internalname" => "tags","value" => ''));
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
	$submit = elgg_view('input/submit',  array('internalname' => "submit",'value' => elgg_echo("upload")));
	
	
	$form_body .= <<<EOT
		<div id='upload_video'>
			<p>{$video_upload}</p>
			<p><label>{$video_title_label}:</label><br /> {$video_title}</p>
			<p class='longtext_editarea'><label>{$video_desc_label}:</label><br /> {$video_desc}</p>
			<p class='longtext_nextfield'><label>{$video_tags_label}:</label><br /> {$video_tags}</p>
			<p><label>{$video_access_label}:</label> {$video_access}</p>
		</div>
	{$container_guid}
	{$video_permission}
	{$video_guid}
	{$submit}
EOT;

	//display the form
	echo elgg_view('input/form', array('action' => "{$vars['url']}action/video/upload", 'body' => $form_body, 'internalid' => 'videoUploadForm', 'enctype' => 'multipart/form-data'));
			
?>
</div>