<?php

$url_to_redirect=$CONFIG->wwwroot . 'pg/docs/my';

$answer = get_input('answer');

$data = unserialize( $_SESSION['google_docs_to_share_data'] );
$doc_id=$data['doc_id'];
$comment=$data['comment'];
$access=$data['access'];
$group_id=$data['group'];

$google_docs = unserialize($_SESSION['oauth_google_docs']);
$user = $_SESSION['user'];
$doc = $google_docs[$doc_id];


$client = authorized_client(true);

switch ($answer) {
    
    case 'Grant view permisson':

        if ($access == 'group') {
            $members = get_group_members_mails($group_id);            
            googleapps_change_doc_sharing($client, $doc['id'], $members) ;
            share_document($doc, $user, $comment, $access, $members);
            break;
        }

        googleapps_change_doc_sharing($client, $doc['id'], $access) ;
        share_document($doc, $user, $comment, $access);
        break;
    case 'Ignore and continue':

        if ($access == 'group') {
            $members = get_group_members_mails($group_id);
            share_document($doc, $user, $comment, $access, $members);
            break;
        }

        share_document($doc, $user, $comment, $access);
        break;

    case 'Cancel':
        break;        
}

forward($url_to_redirect);

?>
