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
        
	$area2 = elgg_view_title(elgg_echo('googleappslogin:google_docs'));

	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">';
	$area2 .= '<div class="contentWrapper singleview">';


        $area2 .='<form action="'.$GLOBALS['change_doc_permissions_url'].'" method="post">';
        $area2 .='<h3>'.elgg_echo('googleappslogin:doc:share:wrong_permissions').'</h3>';
//        $area2 .='<input type="radio" name="answer" value="grant_view" checked>Grant view permisson<br />';
//        $area2 .='<input type="radio" name="answer" value="ignore">Ignore and continue<br />';
//        $area2 .='<input type="radio" name="answer" value="cancel">Cancel<br />';

        $area2 .='<input type="submit" value="Grant view permisson" name="answer">&nbsp;';
        $area2 .='<input type="submit" value="Ignore and continue" name="answer">&nbsp;';
        $area2 .='<input type="submit" value="Cancel" name="answer">&nbsp;';


        $area2.='</div><div class="clearfloat"></div></div>';

	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw( elgg_echo('googleappslogin:google_docs'), $body);

?>
