<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$doc_id = get_input('doc_id', '-1');
$comment = get_input('comment', '');
$access = get_input('access', '');

$url_to_redirect=$CONFIG->wwwroot . 'pg/docs/my';

if($doc_id==-1) {
    system_message(elgg_echo("googleappslogin:doc:share:no_doc_id"));
    forward($url_to_redirect);
}

if( empty($comment)) {
    system_message(elgg_echo("googleappslogin:doc:share:no_comment"));
    forward($url_to_redirect);
}

$google_docs = unserialize($_SESSION['oauth_google_docs']);
$user = $_SESSION['user'];

$doc = $google_docs[$doc_id];

	function access_translate($access) {
            switch ($access) {
                case 'logged_in': return 1; break;
                case 'public': return 2; break;
                default: return -1;  // wrong access
            }
	}

$doc_activity = new ElggObject();
$doc_activity->subtype = "doc_activity";
$doc_activity->owner_guid = $user->guid;
$doc_activity->container_guid = $user->guid;

$doc_activity->access_id = access_translate($access);

$doc_activity->title = $doc['title'];
$doc_activity->text = $comment.' <a href="' . $doc["href"] . '">Open document</a> ';
$doc_activity->res_id=  $doc['id'];

$doc_activity->updated = $doc['updated'];

print_r($doc_activity);


// Now save the object
if (!$doc_activity->save()) {
        register_error('Doc activity has not saves.');
        exit;
}

// if doc is public add to river
if ($doc_activity->access_id!=0) {
    add_to_river('river/object/doc_activity/create', 'create doc', $user->guid, $doc_activity->guid, "", strtotime($date));
}

system_message(elgg_echo("googleappslogin:doc:share:ok"));

// Forward to the docs
forward($url_to_redirect);

?>
