<?php
$data = unserialize($_SESSION['google_docs_to_share_data']);
$comment = $data['comment'];
$tags = $data['tags'];
$access = $data['access'];
$group_id = $data['group'];
$doc_id = $data['doc_id'];


$google_docs = unserialize($_SESSION['oauth_google_docs']);
$doc = $google_docs[$doc_id];
$user = $_SESSION['user'];
$client = authorized_client(true);

switch (get_input('answer')) {	
    case 'Grant view permisson':
        if ($access == 'group') {
            $members = get_group_members_mails($group_id);
            $share_to_members = get_members_not_shared($group_id, $doc);
            googleapps_change_doc_sharing($client, $doc['id'], $share_to_members) ; // change permissions
            share_document($doc, $user, $comment, $tags, $access, $members);
            break;
        }
        googleapps_change_doc_sharing($client, $doc['id'], $access) ;
        share_document($doc, $user, $comment, $tags, $access);
        break;
    case 'Ignore and continue':

        if ($access == 'group') {
            $members = get_group_members_mails($group_id);
            share_document($doc, $user, $comment, $tags, $access, $members); // share to group members
            break;
        }
        share_document($doc, $user, $comment, $tags, $access);
        break;

    case 'Cancel':  echo 'Canceled'; exit; break;

}

     die ( elgg_echo("googleappslogin:doc:share:ok") );
     exit;

?>
