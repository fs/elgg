<?php
	/**
	 * Todo Entity View
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Check for valid entity
	if (isset($vars['entity']) && $vars['entity'] instanceof ElggObject && $vars['entity']) {
		if ($vars['full']) {
				echo elgg_view("todo/todofullview",$vars);
		} else {
			echo elgg_view("todo/todolisting",$vars);
		}
	} else {
		// If were here something went wrong..
		$url = 'javascript:history.go(-1);';
		$owner = $vars['user'];
		$canedit = false;
	}
?>