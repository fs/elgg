<?php
/**
* Kaltura video client
* @package ElggKalturaVideo
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 3
* @author Ivan Vergés <ivan@microstudi.net>
* @copyright Ivan Vergés 2010
* @link http://microstudi.net/elgg/
**/

function kaltura_video_init() {
	// Load system configuration
	global $CONFIG,$KALTURA_CURRENT_TINYMCE_FILE;

	//Add the javascript
	elgg_extend_view('metatags', 'kaltura/jscripts');

	$addbutton = get_plugin_setting('addbutton', 'kaltura_video');
	if (!$addbutton) $addbutton = 'simple';

	if( in_array($addbutton , array('simple','tinymce')) ) {

		include_once(dirname(__FILE__)."/kaltura/api_client/definitions.php");

		//needs to be loaded after htmlawed
		//this is for allow html <object> tags
		$CONFIG->htmlawed_config['safe'] = false;

		$KALTURA_CURRENT_TINYMCE_FILE = '';
		foreach($KALTURA_TINYMCE_PATHS as $plugin => $path) {
			if(is_plugin_enabled($plugin) && is_file($CONFIG->pluginspath.$path)) {
				$KALTURA_CURRENT_TINYMCE_FILE = $path;
				break;
			}
		}

		if( $addbutton == 'tinymce'	) {
			set_view_location('input/longtext', $CONFIG->pluginspath . 'kaltura_video/kaltura/views/');
		}
		else {
			elgg_extend_view('input/longtext', 'kaltura/addvideobutton',9);
			//elgg_extend_view('input/longtext','embed/link',10);
		}
	}


	// Set up menu for logged in users
	if (isloggedin()) {
		add_menu(elgg_echo('kalturavideo:label:adminvideos'), $CONFIG->wwwroot . "pg/kaltura_video/" . $_SESSION['user']->username);
	// And for logged out users
	} else {
		add_menu(elgg_echo('blog'), $CONFIG->wwwroot . "mod/kaltura_video/everyone.php",array());
	}

	// Extend system CSS with our own styles, which are defined in the blog/css view
	elgg_extend_view('css','kaltura/css');

	// Extend hover-over menu
	elgg_extend_view('profile/menu/links','kaltura/menu');

	// Add to groups context
	elgg_extend_view('groups/right_column', 'kaltura/groupprofile');
	//if you prefer to see the widgets in the left part of the groups pages:
	//extend_view('groups/left_column','kaltura/groupprofile');

	// Add group menu option
	add_group_tool_option('kaltura_video',elgg_echo('kalturavideo:enablevideo'),true);

	// Register a page handler, so we can have nice URLs
	register_page_handler('kaltura_video','kaltura_video_page_handler');
	// Register a admin page handler
	register_page_handler('kaltura_video_admin','kaltura_video_page_handler');

	// Register a url handler
	register_entity_url_handler('kaltura_video_url','object', 'kaltura_video');

	// Register granular notification for this type
	if (is_callable('register_notification_object')) {
		register_notification_object('object', 'kaltura_video', elgg_echo('kalturavideo:newvideo'));
	}

	// Listen to notification events and supply a more useful message
	register_plugin_hook('notify:entity:message', 'object', 'kaltura_notify_message');

	// Add profile widget
    add_widget_type('kaltura_video',elgg_echo('kalturavideo:label:latest'),elgg_echo('kalturavideo:text:widgetdesc'));

   // Add index widget
	$enableindexwidget = get_plugin_setting('enableindexwidget', 'kaltura_video');
	if (!$enableindexwidget) $enableindexwidget = 'single';

	if( in_array($enableindexwidget , array('single', 'multi')) ) {
		elgg_extend_view('index/righthandside', 'kaltura/customindex.videos');
	}


	// Register entity type
	register_entity_type('object','kaltura_video');

	//actions for the plugin
	register_action("kaltura_video/delete", false, $CONFIG->pluginspath . "kaltura_video/actions/delete.php");
	register_action("kaltura_video/update", false, $CONFIG->pluginspath . "kaltura_video/actions/update.php");
	register_action("kaltura_video/rate", false, $CONFIG->pluginspath . "kaltura_video/actions/rate.php");

	if(isadminloggedin()) {
		register_action("kaltura_video/admin", false, $CONFIG->pluginspath . "kaltura_video/actions/admin.php");
		register_action("kaltura_video/wizard", false, $CONFIG->pluginspath . "kaltura_video/actions/wizard.php");
	}
}

/**
 * Returns a more meaningful message
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function kaltura_video_notify_message($hook, $entity_type, $returnvalue, $params)
{
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (($entity instanceof ElggEntity) && ($entity->getSubtype() == 'kaltura_video'))
	{
		$descr = $entity->description;
		$title = $entity->title;
		if ($method == 'sms') {
			$owner = $entity->getOwnerEntity();
			return $owner->username . ' via video: ' . $title;
		}
		if ($method == 'email') {
			$owner = $entity->getOwnerEntity();
			return $owner->username . ' via video: ' . $title . "\n\n" . $descr . "\n\n" . $entity->getURL();
		}
	}
	return null;
}
function kaltura_video_url($post) {
		global $CONFIG;
		$title = $post->title;
		$title = friendly_title($title);
		return $CONFIG->url . "pg/kaltura_video/" . $post->getOwnerEntity()->username . "/show/" . $post->getGUID() . "/" . $title;

}

/**
* Post init gumph.
*/
function kaltura_video_page_setup()
{
	global $CONFIG;

	if (get_context() == 'admin' && isadminloggedin()) {
		add_submenu_item(elgg_echo('kalturavideo:admin'), $CONFIG->wwwroot . 'pg/kaltura_video_admin/');
	}

	$page_owner = page_owner_entity();

	if (get_context()=='kaltura_video' && get_plugin_setting("password","kaltura_video"))
	{
		if ((page_owner() == $_SESSION['guid'] || !page_owner()) && isloggedin()) {
			add_submenu_item(elgg_echo('kalturavideo:label:myvideos'), $CONFIG->wwwroot."pg/kaltura_video/" . $_SESSION['user']->username);
			add_submenu_item(elgg_echo('kalturavideo:label:friendsvideos'), $CONFIG->wwwroot."pg/kaltura_video/" . $_SESSION['user']->username ."/friends/");
			if(is_plugin_enabled('groups')) {
				//this page is to search all groups videos, not ready yet
				//add_submenu_item(elgg_echo('kalturavideo:label:allgroupvideos'), $CONFIG->wwwroot."mod/kaltura_video/groups.php");
			}
			add_submenu_item(elgg_echo('kalturavideo:label:allvideos'), $CONFIG->wwwroot."mod/kaltura_video/everyone.php");

		} else if (page_owner()) {
			add_submenu_item(sprintf(elgg_echo('kalturavideo:user'),$page_owner->name),$CONFIG->wwwroot."pg/kaltura_video/" . $page_owner->username);
			if ($page_owner instanceof ElggUser) { // Sorry groups, this isn't for you.
				add_submenu_item(sprintf(elgg_echo('kalturavideo:user:friends'),$page_owner->name),$CONFIG->wwwroot."pg/kaltura_video/" . $page_owner->username ."/friends/" );
			}
			add_submenu_item(elgg_echo('kalturavideo:label:allvideos'), $CONFIG->wwwroot."mod/kaltura_video/everyone.php");
		} else {
			add_submenu_item(elgg_echo('kalturavideo:label:allvideos'), $CONFIG->wwwroot."mod/kaltura_video/everyone.php");
		}

		if (can_write_to_container(0, page_owner()) && isloggedin())
			add_submenu_item(elgg_echo('kalturavideo:label:newvideo'), "#kaltura_create",'pagesactions');


	}
	// Group submenu option
	if ($page_owner instanceof ElggGroup && get_context() == 'groups') {
		if($page_owner->kaltura_video_enable != "no") {
			add_submenu_item(sprintf(elgg_echo("kalturavideo:label:groupvideos"),$page_owner->name), $CONFIG->wwwroot . "pg/kaltura_video/" . $page_owner->username);
		}
	}

}

/**
* feeds page handler; allows the use of fancy URLs
*
* @param array $page From the page_handler function
* @return true|false Depending on success
*/

function kaltura_video_page_handler($page) {
	global $CONFIG;

	if(get_context()=='kaltura_video_admin') {
		include(dirname(__FILE__) . "/admin.php");
		return true;
	}

	if(!get_plugin_setting("password","kaltura_video")){
		// If the URL is just 'feeds/username', or just 'feeds/', load the standard feeds index
		include(dirname(__FILE__) . "/missconfigured.php");
		return true;
	}

	// The first component of a blog URL is the username
	if (isset($page[0])) {
		set_input('username',$page[0]);
	}

	// The second part dictates what we're doing
	if (isset($page[1])) {
		switch($page[1]) {
			case 'friends':
							include(dirname(__FILE__) . "/friends.php");
							return true;
							break;
			case 'show':
							set_input('videopost',$page[2]);
							include(dirname(__FILE__) . "/show.php");
							return true;
							break;

			default:
							include(dirname(__FILE__) . "/index.php");
							return true;
		}
	// If the URL is just 'blog/username', or just 'blog/', load the standard blog index
	} else {
		@include(dirname(__FILE__) . "/index.php");
		return true;
	}

	return false;
}


// Make sure the status initialisation function is called on initialisation
// we want this register the last, that's is only to hack the html cleaner
// if we want to allow <object> tags (only with option addbutton enabled)
register_elgg_event_handler('init','system','kaltura_video_init',9999);
register_elgg_event_handler('pagesetup','system','kaltura_video_page_setup');

?>
