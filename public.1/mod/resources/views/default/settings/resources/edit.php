<p>
<?php 
	/**
	 * Resources settings form
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
?>

	<?php echo elgg_echo('resources:label:adminusers'); ?>
	<br />

    <input type='text' size='60' name='params[admin_user_1]' value="<?php echo $vars['entity']->admin_user_1; ?>" />
    <br />

    <input type='text' size='60' name='params[admin_user_2]' value="<?php echo $vars['entity']->admin_user_2; ?>" />
    <br />

    <input type='text' size='60' name='params[admin_user_3]' value="<?php echo $vars['entity']->admin_user_3; ?>" />
    <br />

    <input type='text' size='60' name='params[admin_user_4]' value="<?php echo $vars['entity']->admin_user_4; ?>" />
    <br />

    <input type='text' size='60' name='params[admin_user_5]' value="<?php echo $vars['entity']->admin_user_5; ?>" />
    <br />
</p>