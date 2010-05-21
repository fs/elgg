<?php
/**
 * Elgg feedback entity listing
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */
?>

<div class="search_listing">

	<div class="search_listing_icon">
		<?php

			echo $vars['icon'];

		?>
	</div>
	<div class="search_listing_info">
		<?php

			echo $vars['info'];

		?>
	</div>
	<div style='clear:both;'></div>
	<div class="feedback_comments">
		<?php
			echo $vars['comments'];
		?>
	</div>
</div>
