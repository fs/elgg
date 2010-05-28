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
	
	

        $client = authorized_client(true);
	googleapps_fetch_oauth_data($client, false, 'folders docs');

//	// Get google docs folders
//	$folders = unserialize($_SESSION['oauth_google_folders']);
//	$google_folder = $folders[$google_folder];
//	$main_folders = child_folders('', $folders);

	$google_docs = unserialize($_SESSION['oauth_google_docs']);


	$area2 = elgg_view_title(elgg_echo('googleappslogin:google_docs'));

	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">';
	$area2 .= '<div class="contentWrapper singleview">';

        $area2 .='<label>Comment to add</label><br /><textarea name="comment" class="docs_comment"></textarea><br /><br />';

        $area2.='<table class="docs_table">
                                <tr>
                                        <td></td><td>Name</td><td>Folder/Sharing</td><td>Modified</td>
                                </tr>';

	foreach ($google_docs as $id => $doc) {            
		$area2 .= '			
                <tr>
                    <td><input type="radio" name="doc" value="'.$id.'"></td>
                    <td><span class="document-icon '.$doc["type"].'"></span>
                             <a href="' . $doc["href"] . '">' . $doc["title"] . '</a></td>
                    <td></td>
                    <td>'.friendly_time( $doc["updated"] ).'</td>
                </tr>
		';
	}
	//$area2 .= elgg_list_entities(array('type' => 'object', 'subtype' => 'site', 'limit' => 4, 'full_view' => FALSE));
	$area2 .= '</table></div><div class="clearfloat"></div></div>';

	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw( elgg_echo('googleappslogin:google_docs'), $body);
?>
