<?php
/**
 * Elgg 1 column with sidebar canvas layout
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */
?>
<div id="elgg_content" class="clearfloat sidebar">
	<div id="elgg_sidebar">
		<?php
			echo elgg_view('page_elements/elgg_sidebar', $vars);
		?>
	</div>
	
	<div id="elgg_page_contents" class="clearfloat">
		<?php 
			if (isset($vars['area1'])) {
				echo $vars['area1'];
			}
		?>
	</div>
</div>
