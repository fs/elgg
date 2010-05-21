<?php
	/**
	 * Rubric widget edit
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

?>
<p>
	<?php echo elgg_echo("rubricbuilder:num"); ?>
	<input type="text" name="params[rubric_num]" value="<?php echo htmlentities($vars['entity']->rubric_num); ?>" />	
</p>