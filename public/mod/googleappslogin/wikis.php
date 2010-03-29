<?php

	/**
	 * Elgg googleappslogin index page
	 *
	 * @package GoogleAppsLogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
	 * @copyright FlatSourcing 2010
	 * @link http://elgg.org/
	 */

	// Load Elgg engine
	global $CONFIG;
	require_once($CONFIG->path . "engine/start.php");
	$user = $_SESSION['user'];
	
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if ($page_owner === false || is_null($page_owner)) {
		$page_owner = $_SESSION['user'];
		set_page_owner($_SESSION['guid']);
	}

	$postfix = $all ? 'everyone' : 'your';

	if ($all) {
		// list of all sites
		$sites = elgg_get_entities(array('type' => 'object', 'subtype' => 'site'));
	} else {
		// get list of logged in user
		$sites = $user->getObjects('site');
	}

	$area2 = elgg_view_title(elgg_echo('googleappslogin:sites:' . $postfix));

	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">';
	$area2 .= '<div class="contentWrapper singleview">';
	foreach ($sites as $site) {
		
		$owner = get_entity($site->owner_guid);
		//echo '<pre>';print_r($owner);exit;
		$area2 .= '
			<div class="search_listing">
				<div class="search_listing_icon">
					<div class="icon">
						<img border="0" src="/mod/googleappslogin/graphics/icon_site.jpg">
					</div>
				</div>
				<div class="search_listing_info">
					<div>
						<p><b><a href="' . $site->url . '">' . $site->title . '</a></b></p>
					</div>
		';
		if ($site->modified) {
			$area2 .= '
					<div>
						Updated ' . friendly_time($site->modified) . '
					</div>';
		}
		$area2 .= '
					<div>
						Owner: <a href="/pg/profile/' . $owner->username . '">' . $owner->name . '</a>
					</div>
				</div>
			</div>
		';
	}
	//$area2 .= elgg_list_entities(array('type' => 'object', 'subtype' => 'site', 'limit' => 4, 'full_view' => FALSE));
	$area2 .= '</div><div class="clearfloat"></div></div>';

	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Get categories, if they're installed
	//global $CONFIG;
	//$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw(elgg_echo('googleappslogin:sites') . ': ' . elgg_echo('googleappslogin:sites:' . $postfix), $body);

?>
