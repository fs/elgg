<?php
	$maxvideosize = $vars['entity']->maxvideosize;
	if (!$maxvideosize) $maxvideosize = (int) 10240; //set the default maximum file size to 10MB (1024KB * 10 = 10240KB = 10MB)

	$num_upload = $vars['entity']->num_upload;
	if (!$num_upload) $num_upload = (int) 5; //set the default maximum file size to 10MB (1024KB * 10 = 10240KB = 10MB)

	$save_original = $vars['entity']->save_original;
	if (!$save_original) $save_original = 'no';
?>
<p>
	<?php echo elgg_echo('video:settings:maxvideosize'); ?>

	<?php echo elgg_view('input/text', array('internalname' => 'params[maxvideosize]', 'value' => $maxvideosize)); ?>
</p>
<p>
	<?php echo elgg_echo('video:saveoriginal'); ?>

	<?php echo elgg_view("input/pulldown",array(
									"internalname" => "params[save_original]",
									"value" => $save_original,
									'options' => array(
														elgg_echo('video:no') => 'no',
														elgg_echo('video:yes') => 'yes',
													),
													)); ?>
</p>