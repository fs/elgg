<p>
	<?php echo elgg_echo('feedback:settings:disablepublic'); ?>
	<select name="params[disablepublic]">
  		<option value="1" <?php if ($vars['entity']->disablepublic == 1) echo " selected=\"yes\" "; ?>>Yes</option>
  		<option value="0" <?php if ($vars['entity']->disablepublic == 0) echo " selected=\"yes\" "; ?>>No</option>
  	</select>
	<br />
	
	<?php echo elgg_echo('feedback:settings:riverdisplay'); ?>
	<select name="params[enableriver]">
  		<option value="1" <?php if ($vars['entity']->enableriver == 1) echo " selected=\"yes\" "; ?>>Yes</option>
  		<option value="0" <?php if ($vars['entity']->enableriver == 0) echo " selected=\"yes\" "; ?>>No</option>
  	</select>
	<br />

	<?php echo elgg_echo('feedback:user_1'); ?>

    <input type='text' size='60' name='params[user_1]' value="<?php echo $vars['entity']->user_1; ?>" />
    <br />

    <?php echo elgg_echo('feedback:user_2'); ?>

    <input type='text' size='60' name='params[user_2]' value="<?php echo $vars['entity']->user_2; ?>" />
    <br />

	<?php echo elgg_echo('feedback:user_3'); ?>

    <input type='text' size='60' name='params[user_3]' value="<?php echo $vars['entity']->user_3; ?>" />
    <br />

	<?php echo elgg_echo('feedback:user_4'); ?>

    <input type='text' size='60' name='params[user_4]' value="<?php echo $vars['entity']->user_4; ?>" />
    <br />

	<?php echo elgg_echo('feedback:user_5'); ?>

    <input type='text' size='60' name='params[user_5]' value="<?php echo $vars['entity']->user_5; ?>" />
    <br />
</p>
