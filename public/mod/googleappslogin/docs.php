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
	googleapps_fetch_oauth_data($client, false, 'docs');

	$google_docs = unserialize($_SESSION['oauth_google_docs']);

	$area2 = elgg_view_title(elgg_echo('googleappslogin:google_docs'));

	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">';
	$area2 .= '<div class="contentWrapper singleview">';


        $area2 .='<form action="'.$GLOBALS['share_doc_url'].'" method="post">';
        $area2 .='<label>Comment to add</label><br /><textarea name="comment" class="docs_comment"></textarea><br /><br />';

        $area2.='<table class="docs_table">
                                <tr>
                                        <td></td><td><b>Name</b></td><td><b>Sharing</b></td><td><b>Modified</b></td>
                                </tr>';

        $documents_collaborators=array();
	foreach ($google_docs as $id => $doc) {
            
            $collaborators = googleapps_google_docs_get_collaborators($client, $doc['id']);           
            $google_docs_collaborators[]=$collaborators;

            $permission_str=get_permission_str($collaborators);

            $area2 .= '
            <tr>
                <td><input type="radio" name="doc_id" value="'.$id.'"></td>
                <td><span class="document-icon '.$doc["type"].'"></span>
                         <a href="' . $doc["href"] . '">' . $doc["title"] . '</a></td>
                <td>'.$permission_str.'</td>
                <td>'.friendly_time( $doc["updated"] ).'</td>
            </tr>
            ';
	}
        $area2 .= '</table>';


        $_SESSION['google_docs_collaboratos']=serialize($google_docs_collaborators);    


        $area2.='<br />View access level: <select name="access">';
        $area2.='<option value="public">Public</option>';
        $area2.='<option value="logged_in">logged in users</option>';
        $area2.='</select>';


        $area2.='&nbsp;&nbsp;&nbsp;<input type="submit" value="Share doc"></form>';


        $area2.='</div><div class="clearfloat"></div></div>';

	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw( elgg_echo('googleappslogin:google_docs'), $body);

?>