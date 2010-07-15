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
	$groups = elgg_get_entities_from_relationship(array('relationship' => 'member', 'relationship_guid' => get_loggedin_userid(), 'types' => 'group', 'limit' => 9999));
	$shared_access = elgg_get_entities_from_relationship(array(	'relationship' => 'shared_access_member', 'relationship_guid' => get_loggedin_userid(), 'limit' => 999));

	$area2 = elgg_view_title(elgg_echo('googleappslogin:google_docs'));
	$area2 .= '<form action="'.$GLOBALS['share_doc_url'].'" method="post" onsubmit="return ajax_submit(this)" >';
	// Get a list of google sites
	$area2 .= '<div id="googleappslogin">Loading....</div>';
	$area2 .= '';

	$area2.='<br />View access level: <select name="access" id="access" onchange="showGroups()">';
	$area2.='<option value="public">Public</option>';
	$area2.='<option value="logged_in">Logged in users</option>';
	if (is_array($groups)) {
		$area2.='<option value="group">Group or Shared Access</option>';
	}
	$area2.='<option value="match">Match permissions of Google doc</option>';
	$area2.='</select>';


	$group_and_channels_list='&nbsp;<span id="group_list"><select name="group_channel">';

	foreach ($groups as $group) {
		$group_and_channels_list .= '<option value="gr'.$group->guid.'">'.$group->name.'</option>';
	}

	foreach ($shared_access as $shared) {
		$group_and_channels_list .= '<option value="ch'.$shared->guid.'">'.$shared->title.'</option>';
	}

	$group_and_channels_list .= '</select></span>';

	$area2 .= $group_and_channels_list;
	$area2 .= '&nbsp;&nbsp;&nbsp;<input type="submit" value="Share doc"></form>';
	$area2 .= '</div><div class="clearfloat"></div></div>';

	switch (get_input('action')) {
	default:
		// Display them in the page
		$body = elgg_view_layout('one_column', $area2);
		// Display page
		page_draw( elgg_echo('googleappslogin:google_docs'), $body);
	break;
	case 'documents':
		$client = authorized_client(true);
		googleapps_fetch_oauth_data($client, false, 'docs');
		$google_docs = unserialize($_SESSION['oauth_google_docs']);


		$area = '';
		$area .= '<link rel="stylesheet" href="/mod/googleappslogin/css/style.css" type="text/css" /> ';
		$area .= '<link rel="stylesheet" href="/mod/googleappslogin/css/jquery-ui-173/css/custom-theme/jquery-ui-1.7.3.custom.css" type="text/css" /> ';
		$area .= '<script type="text/javascript" src="/mod/googleappslogin/jquery.tablesorter.js"></script> ';
		$area .= '<script>
			function sort_number (n) {
				if (n < 10) {
					return "00" + n;
				} else if (n < 100) {
					return "0" + n;
				} else {
					return n.toString();
				}
			};
			$(function(){
				$("#docs_table").tablesorter({
					textExtraction: function (x) {
						var n = parseInt(x.firstChild.innerHTML, 10);
						return isNaN(n) ? x.innerHTML : sort_number(n);
					}
				})
			});</script>';
		$area .= '<div class="contentWrapper singleview">';


		$area .= '<label>Comment to add</label><br /><textarea name="comment" class="docs_comment"></textarea><br /><br />';
                $area .= '<label>Tags</label><br /><input type="text" name="tags" class="docs_tags"></textarea><br /><br />';
		$area .= '<div class="docs_table">            
		  <table width="100%" id="docs_table" class="tablesorter">
		    <thead>
		      <tr><th width="70"></th><th  width="200"><b>Name</b></th><th><b>Sharing</b></th><th><b>Modified</b></th></tr>
		    </thead><tbody>';


		$documents_collaborators=array();
		foreach ($google_docs as $id => $doc) {

		    $collaborators =$doc['collaborators'];
		    $permission_str=get_permission_str($collaborators);

		    $area .= '
		    <tr>
			<td><input type="radio" name="doc_id" value="'.$id.'"></td>
			<td><span class="document-icon '.$doc["type"].'"></span>
				 <a href="' . $doc["href"] . '">' . $doc["trunc_title"] . '</a></td>
			<td>'.$permission_str.'</td>
			<td>'.friendly_time( $doc["updated"] ).'</td>
		    </tr>
		    ';
		}
		$area .= '</tbody></table></div>';
		echo $area;
	exit;
	}

?>

<script type="text/javascript">
var group_list = document.getElementById('group_list');
group_list.style.display='none';

function showGroups(){    
    var val = document.getElementById('access').value;
    if(val=="group") {
        group_list.style.display='';
    } else {
        group_list.style.display='none';
    }

}
function load_docs() {
	$("#googleappslogin").load("?action=documents");
}

function ajax_submit(x) {
	var data = {};
	$($(x).serializeArray()).each(function (i, e) {
		data[e.name] = e.value;
	});
	$.post(x.action.replace(/^http(s?):\/\/.*?\//, "/"), data, function (r) {
		var $dlg = $("<div></div>").html(r).dialog().find('form').submit(function () {
			$dlg.parents('.ui-dialog').remove();
		});
		if (r.toUpperCase() === 'OK') {
			load_docs();
		}
	});
	return false;
}
$(load_docs);
</script>