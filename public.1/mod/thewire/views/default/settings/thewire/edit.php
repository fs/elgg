<p>
<?php 

	// set default value if user hasn't set it
	$limitchars = $vars['entity']->limitchars;
	if (!isset($limitchars)) $limitchars = true;

	echo elgg_echo('thewire:limitchars'); 
	
	echo elgg_view('input/pulldown', array(
			'internalname' => 'params[limitchars]',
			'options_values' => array(	true => 'Yes',
										false => 'No'
									),
			'value' => $limitchars
		));
?>
</p>