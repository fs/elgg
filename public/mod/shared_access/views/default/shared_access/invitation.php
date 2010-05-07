<?php
/**
 * Elgg shared access
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

$name = $vars['sac']->title;
$desc = $vars['sac']->description;
$id = $vars['sac']->getGUID();
$owner_name = get_user($vars['sac']->owner_guid)->name;

$accept_link = $vars['config']->url . '/action/shared_access/join?guid=' . $id;
$accept = elgg_echo('accept');
$decline_link = $vars['config']->url . '/action/shared_access/decline?guid=' . $id;
$decline = elgg_echo('decline');

echo <<<___END
<div>
	<p>$name ($owner_name)</p>
	
	<a href="$accept_link">$accept</a>
	<a href="$decline_link">$decline</a>
</div>

___END;
?>