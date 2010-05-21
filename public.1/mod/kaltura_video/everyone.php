<?php
/**
* Kaltura video client
* @package ElggKalturaVideo
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 3
* @author Ivan Vergés <ivan@microstudi.net>
* @copyright Ivan Vergés 2010
* @link http://microstudi.net/elgg/
**/


	// Load Elgg engine
		define('everyonekaltura_video','true');
		require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the current page's owner
		$page_owner = $_SESSION['user'];
		set_page_owner($_SESSION['guid']);

		$limit = get_input("limit", 10);
		$offset = get_input("offset", 0);

		$area2 = elgg_view_title(elgg_echo('kalturavideo:label:allvideos'));

		$area2 .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'kaltura_video', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));

		// get tagcloud
		// $area3 = "This will be a tagcloud for all blog posts";

		// Get categories, if they're installed
		global $CONFIG;
		$area3 = elgg_view('kaltura/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=kaltura_video&tagtype=universal_categories&tag=','subtype' => 'kaltura_video'));

		$body = elgg_view_layout("two_column_left_sidebar", '', $area2, $area3);

	// Display page
		page_draw(elgg_echo('kalturavideo:label:allvideos'),$body);

?>
