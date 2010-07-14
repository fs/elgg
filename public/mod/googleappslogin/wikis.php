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
                $res=googleapps_sync_sites(true, $user);
		$sites = $res['site_entities'];
	}

	$area2 = elgg_view_title(elgg_echo('googleappslogin:sites:' . $postfix));

	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">';
	$area2 .= '<div class="contentWrapper singleview">';
	
	$site_list = array();
	
	foreach ($sites as $number => $site) {

		if (isset($site_list[$site->site_id])) {
			$actual_site = $site_list[$site->site_id];
			if ($actual_site->owner_guid != $site->owner_guid) {
				if ($actual_site->other_owners == null) {
					$other_owners = array();
				} else {
					$other_owners = unserialize($actual_site->other_owners);
				}
				
				$other_owners[$site->owner_guid] = $site->owner_guid;
				$actual_site->other_owners = serialize(array_unique($other_owners));
				unset($sites[$number]);
			}
		} else {
			$site_list[$site->site_id] = $site;
		}
	}
	
	foreach ($site_list as $number => $site) {            
		//echo '<pre>';print_r($site->other_owners);
		$owner = get_entity($site->owner_guid);
		$owners = array();
		$owners[] = $owner;
		
		$other_owners = array();
		if (!empty($site->other_owners)) {
			$other_owners = unserialize($site->other_owners);
			foreach ($other_owners as $owner) {
				$owners[] = get_entity($owner);
			}
		}
		$c = 0;
		$owners_string = '';
		foreach ($owners as $owner) {


			$owners_string .= '<a href="/pg/profile/' . $owner->username . '">' . $owner->name . '</a>';
			if ($c + 1 < count($owners)) {
				$owners_string .= ', ';
			}
			$c++;
		}
		
		$area2 .= '
			<div class="search_listing">
				<div class="search_listing_icon">
					<div class="icon">
						<img border="0" src="/mod/googleappslogin/graphics/icon_site.jpg">
					</div>
				</div>
				<div>
					<div>
						<p><b><a href="' . $site->url . '">' . $site->title . '</a></b></p>
					</div>
		';



		if ($site->modified) {
			$area2 .= '
					<div>
						Updated ' . friendly_time(  $site->modified) . '
					</div>';
		}
		$area2 .= '
					<div>
						Owners: ' . $owners_string . '
					</div>
				</div>
			</div>
		';
	}
	//$area2 .= elgg_list_entities(array('type' => 'object', 'subtype' => 'site', 'limit' => 4, 'full_view' => FALSE));
	$area2 .= '</div><div class="clearfloat"></div></div>';

	$body = elgg_view_layout("one_column", $area1 . $area2, $area3);

	// Get categories, if they're installed
	//global $CONFIG;
	//$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));

	// Display them in the page
	$body = elgg_view_layout("one_column_with_sidebar", $area1 . $area2, $area3);

	// Display page
	page_draw(elgg_echo('googleappslogin:sites') . ': ' . elgg_echo('googleappslogin:sites:' . $postfix), $body);

?>
