<?php
/**
 * Elgg text input
 * Displays a text input field
 *
 * @package Elgg
 * @subpackage Core

 * @author Curverider Ltd

 * @link http://elgg.org/
 *
 * @uses $vars['value'] The current value, if any
 * @uses $vars['js'] Any Javascript to enter into the input tag
 * @uses $vars['internalname'] The name of the input field
 * @uses $vars['disabled'] If true then control is read-only
 * @uses $vars['class'] Class override
 */


if (isset($vars['class'])) {
	$class = $vars['class'];
} else {
	$class = "input_text";
}

$disabled = false;
if (isset($vars['disabled'])) {
	$disabled = $vars['disabled'];
}

if (!isset($vars['value']) || $vars['value'] === FALSE) {
	$vars['value'] = elgg_get_sticky_value($vars['internalname']);
}

$value = htmlentities($vars['value'], ENT_QUOTES, 'UTF-8');

?>

<input type="text" <?php if ($disabled) echo ' disabled="yes" '; ?> <?php echo $vars['js']; ?> name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> value="<?php echo $value; ?>" class="<?php echo $class ?>"/>