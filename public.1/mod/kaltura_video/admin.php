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
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	global $CONFIG;

	admin_gatekeeper();
	set_context('admin');
	$type = get_input('type'); //the type of page e.g about, terms etc

	$configured = ( get_plugin_setting('password',"kaltura_video") ? true : false );

	if(!$type) {
		$type = "server"; //default to the server config part
		if($configured) $type = "custom";
	}
	// Set admin user for user block
	set_page_owner($_SESSION['guid']);

	//display the title
	$title = elgg_view_title(elgg_echo('kalturavideo:admin'));

	$form = elgg_view('kaltura/admin.menu', array('type' => $type, 'configured' => $configured)).elgg_view('kaltura/admin',array('type' => $type, 'configured' => $configured));

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $title . elgg_view('page_elements/contentwrapper', array('body' =>
		$form)
	));

	// Display main admin menu
	page_draw(elgg_echo('kalturavideo:admin'),$body);
?>
