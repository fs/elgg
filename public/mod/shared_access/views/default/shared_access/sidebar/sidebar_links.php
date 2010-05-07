<?php
/**
 * Links that appear in Conversations sidebar
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */
	
?>
<ul class="submenu shared_access">
<?php
	if(isloggedin()){
		echo "<li><a href=\"{$vars['url']}mod/conversations/everyone.php\">" . elgg_echo('conversations:all') . "</a></li>";
		echo "<li><a href=\"{$vars['url']}pg/conversations/" . $_SESSION['user']->username . "\">". elgg_echo('conversations:read') ."</a></li>";
	}
?>
</ul>