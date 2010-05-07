<?php
	/**
	 * Elgg captcha plugin captcha hook view override.
	 * 
	 * @package ElggCaptcha
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	// check if captcha functions are loaded
	if ( !function_exists ( "captcha_generate_token" ) ) {
		return;
	}

	// Generate a token which is then passed into the captcha algorithm for verification
	$token = captcha_generate_token();
?>
<div class="captcha">
	<input type="hidden" name="captcha_token" value="<?php echo $token; ?>" />
	<div class="captcha-left">
		<img class="captcha-input-image" src="<?php echo $vars['url'] . "pg/captcha/$token"; ?>" />
	</div>
	<div class="captcha-middle">&nbsp;&nbsp;>>&nbsp;&nbsp;</div>
	<div class="captcha-right">
		<?php echo elgg_view('input/text', array('internalname' => 'captcha_input', 'class' => 'captcha-input-text')); ?>
	</div>
	<div style="clear:both;"></div>
</div>
