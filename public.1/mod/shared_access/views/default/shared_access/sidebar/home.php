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

$home = "<a href=\"{$vars['url']}pg/shared_access/home\">" . elgg_echo('shared_access:back_home') . '</a>';

?>

<div class="sidebarBox">
	<?php echo $home; ?>
</div>