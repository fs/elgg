<?php

$url_to_redirect=$CONFIG->wwwroot . 'pg/docs/my';

$answer = get_input('answer');

$data = unserialize( $_SESSION['google_docs_to_share_data'] );
$doc_id=$data['doc_id'];
$comment=$data['comment'];
$access=$data['access'];

$google_docs = unserialize($_SESSION['oauth_google_docs']);
$user = $_SESSION['user'];
$doc = $google_docs[$doc_id];


$client = authorized_client(true);

switch ($answer) {
    
    case 'grant_view':
        googleapps_change_doc_sharing($client, $doc[id], $access) ;
        share_document($doc, $user, $comment, $url_to_redirect);
        break;
    case 'ignore': 
        share_document($doc, $user, $comment, $url_to_redirect);
        break;

    case 'cancel':        
        break;
        
}

forward($url_to_redirect);

?>
