<?php

if (array_key_exists('value', $vars)) {
	$value = $vars['value'];
} elseif ($value = get_input('q', get_input('tag', NULL))) {
	$value = $value;
} else {
	$value = elgg_echo('search');
}

$value = stripslashes($value);

?>

<div id="elgg_search">
	<form id="searchform" action="<?php echo $vars['url']; ?>pg/search/" method="get">
		<fieldset>
		<input type="text" size="21" name="q" value="<?php echo elgg_echo('search'); ?>" onblur="if (this.value=='') { this.value='<?php echo elgg_echo('search'); ?>' }" onfocus="if (this.value=='<?php echo elgg_echo('search'); ?>') { this.value='' };" class="search_input" />
		<input type="submit" value="<?php echo elgg_echo('search:go'); ?>" class="search_submit_button" />
		</fieldset>
	</form>
</div>