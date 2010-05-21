<p>
<?php 

	// set default value if user hasn't set it
	$domain = $vars['entity']->domain;
	if (!isset($domain)) $domain = "";

	echo elgg_echo('tgs_tweaks:domain'); 
	echo elgg_view('input/text',array('internalname'=>'params[domain]','value'=>$domain));
	
?>
</p>