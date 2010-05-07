<?php
/**
 * Elgg shared access plugin
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */
$sac = $vars['sac'];
$access_id = $sac->acl_id;
$title = elgg_echo('shared_access:post_to') . " " . $sac->title . " " . elgg_echo('wire');

$chars_left_str = elgg_echo("thewire:charleft");
$doing_str = elgg_echo("thewire:doing");
$submit = elgg_view('input/submit', array('value'=>elgg_echo('thewire:post')));

$form_body = <<<___END
<script type="text/javascript">
// bind update counter changes.
$(document).ready(function() {
	$('textarea[name=note]').keydown(function(event) {
		textCounter(this, 140, '.thewire_characters_remaining_field');
	}).keyup(function() {
		textCounter(this, 140, '.thewire_characters_remaining_field');
	})
});

function textCounter(field, max, counter) {
	field = $(field)
	var len = $(field).val().length;
	var counter = $(counter);
	
	counter.html((max - len) > 0 ? max - len : 0 );
	
	if (len >= max) {
		field.val(field.val().substring(0, max));
	}
}
</script>

<div class='thewire_characters_remaining'><span class="thewire_characters_remaining_field">140</span> $chars_left_str</div>
<textarea name='note' id="thewire_small-textarea"></textarea>
$submit
<input type="hidden" name="method" value="site" />
<input type="hidden" name="access" value="$access_id" />
<input type="hidden" name="location" value="referer" />
___END;

$form = elgg_view('input/form', array('body'=>$form_body, 'action'=>$vars['url'] . 'action/conversations/add'));

?>

<h3><?php echo $title; ?></h3>
<?php echo $form; ?>
