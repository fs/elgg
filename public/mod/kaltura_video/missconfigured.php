<?php
/**
* Kaltura video client
* @package ElggKalturaVideo
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 3
* @author Ivan Vergés <ivan@microstudi.net>
* @copyright Ivan Vergés 2010
* @link http://microstudi.net/elgg/
**/

require_once($CONFIG->pluginspath."kaltura_video/kaltura/api_client/includes.php");

$body = elgg_view_title(elgg_echo("kalturavideo:error:notconfigured"));

$body .= '<div class="contentWrapper">'.kaltura_get_error_page("","",false)."</div>";

//global $autofeed;
//$autofeed = false;
// Display main admin menu
page_draw($title,elgg_view_layout("two_column_left_sidebar", '', $body));

?>
