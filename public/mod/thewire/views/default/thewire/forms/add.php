<?php

	/**
	 * Elgg thewire edit/add page
	 * 
	 * @package ElggTheWire
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 * 
	 */

		$wire_user = get_input('wire_username');
		if (!empty($wire_user)) { $msg = '@' . $wire_user . ' '; } else { $msg = ''; }
		
		// set default value if user hasn't set it
		$limitchars = get_plugin_setting('limitchars','thewire');
		if (!empty($limitchars)) $limitchars = true;
		
		$access_id = (int)get_default_access();

?>
<div class="post_to_wire">
<h3><?php echo elgg_echo("thewire:doing"); ?></h3>
<script>
function textCounter(field,cntfield,maxlimit) {
    // if too long...trim it!
    if (field.value.length > maxlimit) {
        field.value = field.value.substring(0, maxlimit);
    } else {
        // otherwise, update 'characters left' counter
        cntfield.value = maxlimit - field.value.length;
    }
}
</script>

	<form action="<?php echo $vars['url']; ?>action/thewire/add" method="post" name="noteForm">
			<?php
				if ($limitchars) {
			    $display .= "<textarea name='note' value='' onKeyDown=\"textCounter(document.noteForm.note,document.noteForm.remLen1,140)\" onKeyUp=\"textCounter(document.noteForm.note,document.noteForm.remLen1,140)\" id=\"thewire_large-textarea\">{$msg}</textarea>";
          $display .= "<div class='thewire_characters_remaining'><input readonly type=\"text\" name=\"remLen1\" size=\"3\" maxlength=\"3\" value=\"140\" class=\"thewire_characters_remaining_field\">";
          $display .= elgg_echo("thewire:charleft") . "</div>";
				} else {
					$display .= "<textarea name='note' value='' id=\"thewire_large-textarea\">{$msg}</textarea>";
//					$display .= "<div class='thewire_tips'>" . elgg_echo("thewire:tips") . "</div>";
				}
				$display .= "<div class='thewire_tips'>" . elgg_echo("thewire:tips") . "</div>";
					echo $display;
				  echo elgg_view('input/securitytoken');
			?>
			<input type="hidden" name="method" value="site" />
			<? echo elgg_echo("thewire:access") . elgg_view('input/access', array('internalname' => 'access_id', 'value' => $access_id)); ?>
			<input type="submit" value="<?php echo elgg_echo("thewire:postbutton"); ?>" />
	</form>
</div>
<?php echo elgg_view('input/urlshortener'); ?>